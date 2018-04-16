<?php

namespace STG\DEIM\Auditoria\Bundle\AuditoriaBundle\Tests\Services;

use STG\DEIM\Auditoria\Bundle\AuditoriaBundle\Entity\RegistroAuditoria;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

include 'AuditoriaAnnotationsTestClasses.php';

/**
 * Description of AuditoriaServiceTest
 *
 * @author fbaroni@santafe.gov.ar
 */
class AuditoriaServiceTest extends WebTestCase
{

    protected static $auditoriaService;
    protected static $container;

    public static function setUpBeforeClass()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        self::$container = $kernel->getContainer();
        self::$auditoriaService = self::$container->get('stg.deim.auditoria.auditoria.auditoria');
    }

    public function testIsAuditableObjectTest()
    {

        $auditableObject = new AuditableClass();
        $nonAuditableObject = new NonAuditableClass();
        $this->assertTrue(self::$auditoriaService
                        ->isAuditable($auditableObject));

        $this->assertFalse(self::$auditoriaService
                        ->isAuditable($nonAuditableObject));

    }
}
