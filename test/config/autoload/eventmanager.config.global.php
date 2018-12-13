<?php

return [
    'service_manager' => [
        'factories' => [
            \ZfMetalTest\Restful\Listener\TestListener::class => \Zend\ServiceManager\Factory\InvokableFactory::class
        ],
    ],
    'listeners' => [
        \ZfMetalTest\Restful\Listener\TestListener::class
    ],

];