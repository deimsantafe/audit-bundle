<?php

namespace STG\DEIM\Auditoria\Bundle\AuditoriaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use STG\DEIM\Auditoria\Bundle\AuditoriaBundle\Entity\RegistroAuditoria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    private $nombreEntidad;

    public function __construct()
    {
        $this->nombreEntidad = 'AuditoriaBundle:RegistroAuditoria';
    }

    /**
     * @Route("/")
     */
    public function indexAction()
    {
        // Filtros
        $filtros = $this->get('stg.deim.themes.aplicativo.filtro')
                ->getFiltros(new RegistroAuditoria());

        $consulta = $this->get('stg.deim.themes.aplicativo.filtro')
                ->generarConsultaMultiplesFiltros($this->nombreEntidad, 
                        $filtros, 'fecha DESC');

        // Se configura el paginador
        $paginacion = $this->get('stg.deim.themes.aplicativo.paginator')
                ->getPagination($consulta, 15);

        return $this->render($this->nombreEntidad . ':index.html.twig', array(
                    'filtros' => $filtros,
                    'paginacion' => $paginacion,
        ));
    }

}
