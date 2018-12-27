<?php
/**
 * Created by PhpStorm.
 * User: crist
 * Date: 12/12/2018
 * Time: 22:57
 */

namespace ZfMetalTest\Restful\Listener;


use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use ZfMetal\Restful\Controller\MainController;
use ZfMetalTest\Restful\Entity\Foo;

class TestListener implements ListenerAggregateInterface
{

    private $listeners = [];

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     * @param int $priority
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedEventManager = $events->getSharedManager();
        $this->listeners[] = $sharedEventManager->attach(MainController::class,'create_foo_before', [$this, 'log']);
        $this->listeners[] = $sharedEventManager->attach(MainController::class,'create_foo_after', [$this, 'log']);
        $this->listeners[] = $sharedEventManager->attach(MainController::class,'update_foo_before', [$this, 'log']);
        $this->listeners[] = $sharedEventManager->attach(MainController::class,'update_foo_after', [$this, 'log']);

    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        // TODO: Implement detach() method.
    }

    function log(EventInterface $event){
        /** @var Foo $object */
        $object = $event->getParam("object");
       // echo "TestListener. EventName: ".$event->getName()." Title: ".$object->getTitle().PHP_EOL;

    }
}