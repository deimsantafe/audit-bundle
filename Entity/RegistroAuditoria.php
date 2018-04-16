<?php

namespace STG\DEIM\Auditoria\Bundle\AuditoriaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Symfony\Component\Validator\Constraints as Assert;
use STG\DEIM\Themes\Bundles\AplicativoBundle\Annotation\Filter;

/**
 * RegistroAuditoria
 *
 * @ORM\Table(name="registroAuditoria")
 * @ORM\Entity()
 */
class RegistroAuditoria
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", length=45, nullable=false)
     * @Filter(type="text")
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_cliente", type="string", length=45, nullable=false)
     * @Filter(type="text")
     */
    private $ipCliente;

    /**
     * @var string
     *
     * @ORM\Column(name="entidad", type="text", nullable=false)
     * @Filter(type="text")
     */
    private $entidad;

    /**
     * @var string
     *
     * @ORM\Column(name="fecha",  type="datetime", length=255, nullable=false)
     * @Filter(type="fecha")
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="campo", type="text", nullable=false)
     * @Filter(type="text")
     */
    private $campo;

    /**
     * @var string
     *
     * @ORM\Column(name="valor_anterior", type="text", nullable=true)
     * @Filter(type="text")
     */
    private $valorAnterior;

    /**
     * @var string
     *
     * @ORM\Column(name="valor_nuevo", type="text", nullable=true)
     * @Filter(type="text")
     */
    private $valorNuevo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_evento", type="string", length=45, nullable=false)
     * @Filter(type="text")
     */
    private $nombreEvento;

    /**
     * @var integer
     *
     * @ORM\Column(name="entidad_pk", type="integer", nullable=true)
     */
    private $entidadPK;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_tabla", type="string", length=45, nullable=false)
     */
    private $nombreTabla;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=45, nullable=false)
     */
    private $uuid;


    /**
     * @var string
     *
     * @ORM\Column(name="nombre_controlador", type="string", length=255, nullable=false)
     * @Filter(type="text", label="Acción negocio")
     */
    private $nombreControlador;


    /**
     * @var string
     *
     * @ORM\Column(name="nombre_accion", type="string", length=255, nullable=false)
     * @Filter(type="text")
     */
    private $nombreAccion;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set usuario
     *
     * @param string $usuario
     * @return RegistroAuditoria
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set entidad
     *
     * @param string $entidad
     * @return RegistroAuditoria
     */
    public function setEntidad($entidad)
    {
        $this->entidad = $entidad;

        return $this;
    }

    /**
     * Get entidad
     *
     * @return string
     */
    public function getEntidad()
    {
        return $this->entidad;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return RegistroAuditoria
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set campo
     *
     * @param string $campo
     * @return RegistroAuditoria
     */
    public function setCampo($campo)
    {
        $this->campo = $campo;

        return $this;
    }

    /**
     * Get campo
     *
     * @return string
     */
    public function getCampo()
    {
        return $this->campo;
    }

    /**
     * Set valorAnterior
     *
     * @param string $valorAnterior
     * @return RegistroAuditoria
     */
    public function setValorAnterior($valorAnterior)
    {
        if ($valorAnterior instanceof \DateTime) {
            $this->valorAnterior = $valorAnterior->format('d-m-Y');
        } elseif (is_array($valorAnterior) || is_object($valorAnterior)) {
            $this->valorAnterior = json_encode($valorAnterior);
        } else {
            $this->valorAnterior = $valorAnterior;
        }

        return $this;
    }

    /**
     * Get valorAnterior
     *
     * @return string
     */
    public function getValorAnterior()
    {
        return $this->valorAnterior;
    }

    /**
     * Set valorNuevo
     *
     * @param string $valorNuevo
     * @return RegistroAuditoria
     */
    public function setValorNuevo($valorNuevo)
    {
        if ($valorNuevo instanceof \DateTime) {
            $this->valorNuevo = $valorNuevo->format('d-m-Y');
        } elseif (is_array($valorNuevo) || is_object($valorNuevo)) {
            $this->valorNuevo = json_encode($valorNuevo);
        } else {
            $this->valorNuevo = $valorNuevo;
        }

        return $this;
    }

    /**
     * Get valorNuevo
     *
     * @return string
     */
    public function getValorNuevo()
    {
        return $this->valorNuevo;
    }

    /**
     * Set nombreEvento
     *
     * @param string $nombreEvento
     * @return RegistroAuditoria
     */
    public function setNombreEvento($nombreEvento)
    {
        $this->nombreEvento = $nombreEvento;

        return $this;
    }

    /**
     * Get nombreEvento
     *
     * @return string
     */
    public function getNombreEvento()
    {
        return $this->nombreEvento;
    }

    /**
     * Set entidadPK
     *
     * @param integer $entidadPK
     * @return RegistroAuditoria
     */
    public function setEntidadPK($entidadPK)
    {
        $this->entidadPK = $entidadPK;

        return $this;
    }

    /**
     * Get entidadPK
     *
     * @return integer
     */
    public function getEntidadPK()
    {
        return $this->entidadPK;
    }

    /**
     * Set nombreTabla
     *
     * @param string $nombreTabla
     * @return RegistroAuditoria
     */
    public function setNombreTabla($nombreTabla)
    {
        $this->nombreTabla = $nombreTabla;

        return $this;
    }

    /**
     * Get nombreTabla
     *
     * @return string
     */
    public function getNombreTabla()
    {
        return $this->nombreTabla;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     * @return RegistroAuditoria
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }


    /**
     * Set nombreControlador
     *
     * @param string $nombreControlador
     * @return RegistroAuditoria
     */
    public function setNombreControlador($nombreControlador)
    {
        $this->nombreControlador = $nombreControlador;

        return $this;
    }

    /**
     * Get nombreControlador
     *
     * @return string
     */
    public function getNombreControlador()
    {
        return $this->nombreControlador;
    }

    /**
     * Get nombreCortoControlador
     *
     * @return string
     */
    public function getNombreCortoControlador()
    {
        $namespaceControlador = explode('\\', $this->nombreControlador);

        return str_replace('Controller', '', array_pop($namespaceControlador));
    }

    /**
     * Set nombreAccion
     *
     * @param string $nombreAccion
     * @return RegistroAuditoria
     */
    public function setNombreAccion($nombreAccion)
    {
        $this->nombreAccion = $nombreAccion;

        return $this;
    }

    /**
     * Get nombreAccion
     *
     * @return string
     */
    public function getNombreAccion()
    {
        return $this->nombreAccion;
    }

    public function getIpCliente()
    {
        return $this->ipCliente;
    }

    public function setIpCliente($ipCliente)
    {
        $this->ipCliente = $ipCliente;

        return $this;
    }


}