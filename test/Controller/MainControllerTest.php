<?php

namespace ZfMetalTest\Restful\Controller;

use ZfMetal\Restful\Controller\MainController;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class MainControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    public function setUp()
    {
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
        $configOverrides = [
            'zf-metal-restful.options'  => array(
            'entity_aliases' => array(
                'foo' => \ZfMetalTest\Restful\Entity\Foo::class,
            ),
        ),];

        $this->setApplicationConfig(ArrayUtils::merge(
        // Grabbing the full application configuration:
            include __DIR__ . '/../config/application.config.php',
            $configOverrides
        ));


        parent::setUp();


    }

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/zfmr/api/foo');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('ZfMetal\Restful');
        $this->assertControllerName(MainController::class);
        $this->assertControllerClass('MainController');
        $this->assertActionName("get");
        $this->assertMatchedRouteName('zfmr/api');
    }


    function getPostData()
    {
        return [
        ];
    }
}