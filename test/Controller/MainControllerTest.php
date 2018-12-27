<?php

namespace ZfMetalTest\Restful\Controller;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\ServiceManager;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;
use ZfMetal\Restful\Controller\MainController;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use ZfMetalTest\DataFixture\FooLoader;
use ZfMetalTest\Restful\Entity\Foo;

class MainControllerTest extends AbstractConsoleControllerTestCase
{
    protected $traceError = true;

    public function setUp()
    {

        ini_set('xdebug.var_display_max_depth','10');
        ini_set('xdebug.var_display_max_children','256');
        ini_set('xdebug.var_display_max_data','1024');
        $this->setApplicationConfig(
            include __DIR__ . '/../config/application.config.php'
        );

        parent::setUp();


    }

    public function getEm()
    {
        return $this->getApplicationServiceLocator()->get(EntityManager::class);
    }


    protected function addEventOnCreate()
    {
        /** @var EventManager $eventManager */
        $eventManager = $this->getApplicationServiceLocator()->get("EventManager");
        $eventManager->getSharedManager();
        $eventManager->getSharedManager()->attach(MainController::class, 'create_foo_before', function ($e) {
            echo "addEventOnCreate" . PHP_EOL;
        });

    }


    /**
     * Se genera la estructura de la base de datos (Creacion de tablas)
     */
    public function testGenerateStructure()
    {

        $this->setUseConsoleRequest(true);
        $this->dispatch('orm:schema-tool:update --force');
        $this->assertResponseStatusCode(0);
        //$this->assertConsoleOutputContains("Updating database schema");
    }

    /**
     * @depends testGenerateStructure
     * Se popula las tablas con datos necesarios (Permisos, Roles, Usuarios y sus relaciones)
     */
    public function testCreateData()
    {
        $this->setUseConsoleRequest(false);
        $loader = new Loader();
        $loader->addFixture(new \ZfMetalTest\Restful\DataFixture\FooLoader());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->getEm(), $purger);
        $executor->execute($loader->getFixtures());
    }


    /**
     * @depends testCreateData
     * METHOD POST
     * ACTION create
     * DESC create item
     */

    public function testCreate()
    {
        $this->setUseConsoleRequest(false);
        //$this->addEventOnCreate();

        $params = [
            "title" => "test title create",
        ];

        $this->dispatch("/api/foo", "POST",
            $params);

        $jsonToCompare = [
            "status" => true,
            'id' => 2,
            "message" => "The item was created successfully"
        ];


        $this->assertJsonStringEqualsJsonString($this->getResponse()->getContent(), json_encode($jsonToCompare));
        $this->assertResponseStatusCode(201);
    }

    /**
     * @depends testCreate
     * METHOD GET
     * ACTION get
     * DESC get item
     */

    public function testGet()
    {
        $this->setUseConsoleRequest(false);


        $this->dispatch("/api/foo/2", "GET");

        $jsonToCompare = [
            'id' => 2,
            "title" => "test title create",
        ];


        $this->assertJsonStringEqualsJsonString($this->getResponse()->getContent(), json_encode($jsonToCompare));
        $this->assertResponseStatusCode(200);
    }

    /**
     * @depends testCreate
     * METHOD PUT
     * ACTION update
     * DESC update item
     */

    public function testUpdate()
    {
        $this->setUseConsoleRequest(false);

        $params = [
            "title" => "test title updated",
        ];

        $this->dispatch("/api/foo/1", "PUT",
            $params);

        $jsonToCompare = [
            "status" => true,
            'id' => 1,
            "message" => "The item was updated successfully"
        ];


        $this->assertJsonStringEqualsJsonString($this->getResponse()->getContent(), json_encode($jsonToCompare));
        $this->assertResponseStatusCode(200);
    }




    /**
     * @depends testCreateData
     * METHOD POST
     * ACTION create with custom route
     * DESC crear un nuevo usuario
     */

    public function testCreateWithCustomRoute()
    {
        $this->setUseConsoleRequest(false);
        //$this->addEventOnCreate();

        $params = [
            "title" => "custom route create foo",
        ];

        $this->dispatch("/custom/api/foo", "POST",
            $params);

        $jsonToCompare = [
            "status" => true,
            'id' => 3,
            "message" => "The item was created successfully"
        ];


        $this->assertJsonStringEqualsJsonString($this->getResponse()->getContent(), json_encode($jsonToCompare));
        $this->assertResponseStatusCode(201);
    }



    /**
     * @depends testUpdate
     * METHOD POST
     * ACTION autocomplete
     * DESC Get list for autocomplete function
     */

    public function testAutocomplete()
    {
        $this->setUseConsoleRequest(false);


        $this->dispatch("/api/foo/autocomplete", "POST",
            ["search" => "test"]
        );

        $jsonToCompare = [
            ["key" => 1, "value" => "test title updated"],
            ["key" => 2, "value" => "test title create"]
        ];

echo $this->getResponse()->getContent();

        $this->assertJsonStringEqualsJsonString($this->getResponse()->getContent(), json_encode($jsonToCompare));
        $this->assertResponseStatusCode(200);
    }
}