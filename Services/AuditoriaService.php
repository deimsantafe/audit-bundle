<?php

namespace STG\DEIM\Auditoria\Bundle\AuditoriaBundle\Services;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Psr\Log\LoggerInterface;
use STG\DEIM\Auditoria\Bundle\AuditoriaBundle\Annotation\Auditable;
use STG\DEIM\Auditoria\Bundle\AuditoriaBundle\Entity\RegistroAuditoria;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class AuditoriaService
{

    protected $logger;
    protected $tokenStorage;
    protected $reader;

    public function __construct(LoggerInterface $logger, TokenStorage $tokenStorage)
    {
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
        $this->reader = new AnnotationReader();
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $this->auditEventHandler($args);
    }

    public function auditEventHandler($args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        $insertions = $uow->getScheduledEntityInsertions();
        $updates = $uow->getScheduledEntityUpdates();
        $deletions = $uow->getScheduledEntityDeletions();


        foreach ($insertions as $entidad) {
            if ($this->isAuditable($entidad)) {
                $this->auditEntity($entidad, $em, 'INSERT');
            }
        }
        foreach ($updates as $entidad) {
            if ($this->isAuditable($entidad)) {
                $this->auditEntity($entidad, $em, 'UPDATE');
            }
        }

        foreach ($deletions as $entidad) {
            if ($this->isAuditable($entidad)) {
                $this->auditEntity($entidad, $em, 'REMOVE');
            }
        }

        foreach ($uow->getScheduledCollectionUpdates() as $association) {
            $entidadPadre = $association->getOwner();

            if ($this->isAuditable($entidadPadre)) {
                $this->guardarRegistrosDeAuditoriaCollection(
                    $em, $association);
            }

        }
    }

    public function auditEntity($entidad, $entityManager, $nombreEvento)
    {
        $this->logger->info($nombreEvento . ' Auditando entidad ' .
            get_class($entidad));
        try {
            $this->guardarRegistrosDeAuditoria(
                $entityManager, $entidad, $nombreEvento);
        } catch (\Exception $ex) {
            $this->logger->error('Error al auditar ' . get_class($entidad) .
                ' = ' . $ex->getMessage() . '(' . $ex->getCode() . ')');
        }
    }

    public function isAuditable($entidad)
    {
        $class = new \ReflectionClass($entidad);

        $annotations = $this->reader->getClassAnnotations($class);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Auditable) {
                return true;
            }
        }
        return false;
    }

    private function guardarRegistrosDeAuditoria($em, $entidad, $nombreEvento)
    {

        $uow = $em->getUnitOfWork();
        //como en onFlush ya se calcularon los cambios, tenemos que calcularlos nuevamente
        //TODO refactor para casos de muchos ingresos
        $registroMetadata = $em
            ->getClassMetadata('STG\DEIM\Auditoria\Bundle\AuditoriaBundle\Entity\RegistroAuditoria');

        $nombreUsuarioActual = ($this->getToken() &&
            $this->getToken()->getUser() != 'anon.') ?
            $this->getToken()->getUser()->getId() : 'ANONIMO';

        $uuid = $this->guid();

        if ($nombreEvento == 'INSERT' || $nombreEvento == 'UPDATE') {
            $changeSet = $uow->getEntityChangeSet($entidad);

            foreach ($changeSet as $nombreCampo => $change) {

                $registroAuditoria = new RegistroAuditoria();
                $registroAuditoria->setNombreTabla($em->
                getClassMetadata(get_class($entidad))->getTableName());
                $registroAuditoria->setUuid($uuid);
                $registroAuditoria->setEntidadPK($entidad->getId());
                $registroAuditoria->setUsuario($nombreUsuarioActual);
                $registroAuditoria->setIpCliente($this->container->get('request')->getClientIp());
                $registroAuditoria->setCampo($nombreCampo);
                $registroAuditoria->setValorAnterior((is_object($change[0]) && !($change[0] instanceof \DateTime)) ? $change[0]->getId() : $change[0]);
                $registroAuditoria->setValorNuevo((is_object($change[1]) && !($change[1] instanceof \DateTime)) ? $change[1]->getId() : $change[1]);
                $registroAuditoria->setNombreEvento($nombreEvento);
                $registroAuditoria->setFecha(new \DateTime('now'));
                $registroAuditoria->setEntidad(get_class($entidad));
                $this->getAccionNegocio($registroAuditoria);

                $em->persist($registroAuditoria);
                $uow->computeChangeSet($registroMetadata, $registroAuditoria);

            }
        } elseif ($nombreEvento == 'REMOVE') {

            $changeSet = $uow->getOriginalEntityData($entidad);

            foreach ($changeSet as $nombreCampo => $change) {

                $registroAuditoria = new RegistroAuditoria();
                $registroAuditoria->setNombreTabla($em->
                getClassMetadata(get_class($entidad))->getTableName());
                $registroAuditoria->setUuid($uuid);
                $registroAuditoria->setEntidadPK($entidad->getId());
                $registroAuditoria->setUsuario($nombreUsuarioActual);
                $registroAuditoria->setIpCliente($this->container->get('request')->getClientIp());
                $registroAuditoria->setCampo($nombreCampo);
                $registroAuditoria->setValorAnterior(is_object($change) && !($change instanceof \DateTime) ? $change->getId() : $change);
                $registroAuditoria->setNombreEvento($nombreEvento);
                $registroAuditoria->setFecha(new \DateTime('now'));
                $registroAuditoria->setEntidad(get_class($entidad));
                $this->getAccionNegocio($registroAuditoria);

                $em->persist($registroAuditoria);
                $uow->computeChangeSet($registroMetadata, $registroAuditoria);
            }
        }
    }


    private function guardarRegistrosDeAuditoriaCollection($em, $entidad, $nombreEvento = "")
    {

        $uow = $em->getUnitOfWork();
        //como en onFlush ya se calcularon los cambios, tenemos que calcularlos nuevamente
        //TODO refactor para casos de muchos ingresos
        $registroMetadata = $em
            ->getClassMetadata('STG\DEIM\Auditoria\Bundle\AuditoriaBundle\Entity\RegistroAuditoria');

        $nombreUsuarioActual = ($this->getToken() &&
            $this->getToken()->getUser() != 'anon.') ?
            $this->getToken()->getUser()->getId() : 'ANONIMO';

        $uuid = $this->guid();

        $entidadPadre = $entidad->getOwner();

        $deleted = $entidad->getDeleteDiff();

        $inserted = $entidad->getInsertDiff();


        if (!empty($inserted)) {

            $nombreEvento = 'INSERT';

            foreach ($inserted as $key => $value) {

                $registroAuditoria = new RegistroAuditoria();
                $registroAuditoria->setNombreTabla($em->
                getClassMetadata(get_class($value))->getTableName());
                $registroAuditoria->setUuid($uuid);
                $registroAuditoria->setEntidadPK($entidadPadre->getId());
                $registroAuditoria->setUsuario($nombreUsuarioActual);
                $registroAuditoria->setIpCliente($this->container->get('request')->getClientIp());
                $registroAuditoria->setCampo("");
                $registroAuditoria->setValorNuevo($value->getId());
                $registroAuditoria->setNombreEvento($nombreEvento);
                $registroAuditoria->setFecha(new \DateTime('now'));
                $registroAuditoria->setEntidad(get_class($entidadPadre));
                $this->getAccionNegocio($registroAuditoria);

                $em->persist($registroAuditoria);
                $uow->computeChangeSet($registroMetadata, $registroAuditoria);
            }
        }

        if (!empty($deleted)) {

            $nombreEvento = 'DELETE';

            foreach ($deleted as $key => $value) {

                $registroAuditoria = new RegistroAuditoria();
                $registroAuditoria->setNombreTabla($em->
                getClassMetadata(get_class($value))->getTableName());
                $registroAuditoria->setUuid($uuid);
                $registroAuditoria->setEntidadPK($entidadPadre->getId());
                $registroAuditoria->setUsuario($nombreUsuarioActual);
                $registroAuditoria->setIpCliente($this->container->get('request')->getClientIp());
                $registroAuditoria->setCampo("");
                $registroAuditoria->setValorAnterior($value->getId());
                $registroAuditoria->setNombreEvento($nombreEvento);
                $registroAuditoria->setFecha(new \DateTime('now'));
                $registroAuditoria->setEntidad(get_class($entidadPadre));
                $this->getAccionNegocio($registroAuditoria);

                $em->persist($registroAuditoria);
                $uow->computeChangeSet($registroMetadata, $registroAuditoria);
            }
        }
    }

    private function guid()
    {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((double)microtime() * 10000); //optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            $uuid = chr(123)// "{"
                . substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12)
                . chr(125); // "}"
            return trim($uuid, "{}");
        }
    }

    private function getAccionNegocio(RegistroAuditoria $registroAuditoria)
    {
        $routeCollection = $this->container->get('router')->getRouteCollection();

        $route = $this->container->get('request')->get('_route');

        $routeDefaults = $routeCollection->get($route)->getDefaults();

        $controller = explode('::', $routeDefaults['_controller']);

        $controllerClass = $controller[0];

        $controllerMethod = str_replace('Action', '', $controller[1]);


        $registroAuditoria->setNombreControlador($controllerClass);

        $registroAuditoria->setNombreAccion($controllerMethod);
    }

    /**
     * @return TokenStorage
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    /**
     * @return null|\Symfony\Component\Security\Core\Authentication\Token\TokenInterface
     */
    private function getToken()
    {
        return $this->getTokenStorage()->getToken();
    }

}
