<?php

return array(
    'controller_plugins' => array(
        'factories' => array(
            \ZfMetal\Restful\Controller\Plugin\Options::class => \ZfMetal\Restful\Factory\Controller\Plugin\OptionsFactory::class,
        ),
        'aliases' => array(
            'zfMetalRestfulOptions' => \ZfMetal\Restful\Controller\Plugin\Options::class,
        ),
    ),
);