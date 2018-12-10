<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'ZfMetalRestful.options' => \ZfMetal\Restful\Factory\Options\ModuleOptionsFactory::class,
            'zf-metal-restful-resource' => \ZfMetal\Restful\Factory\Service\ResourceFactory::class,
            \ZfMetal\Restful\Filter\FilterManager::class => \ZfMetal\Restful\Factory\Filter\FilterManagerFactory::class
        ),
    ),
);