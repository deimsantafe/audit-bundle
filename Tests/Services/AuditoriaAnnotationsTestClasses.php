<?php

namespace STG\DEIM\Auditoria\Bundle\AuditoriaBundle\Tests\Services;

use JMS\Serializer\Annotation\Type;
use STG\DEIM\Auditoria\Bundle\AuditoriaBundle\Annotation\Auditable;

/**
 * AuditableClass
 * @Auditable()
 */
class AuditableClass
{

    /**
     * @Type("string")
     */
    public $atributo;

}

/**
 * NonAuditableClass
 */
class NonAuditableClass
{
    
}

/**
 * PreUpdatePrePersistAuditableClass
 * @Auditable(events={"preUpdate", "prePersist"})
 */
class PreUpdatePrePersistAuditableClass
{
    
}
