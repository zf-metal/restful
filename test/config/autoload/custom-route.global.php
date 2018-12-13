<?php

return [
    'router' => [
        'routes' => [
            'zfmcErp' => [
                'type' => \Zend\Router\Http\Literal::class,
                'mayTerminate' => false,
                'options' => [
                    'route' => '/custom/api',
                ],
                'child_routes' => [
                    'api' => [
                        'type' => \Zend\Router\Http\Segment::class,
                        'mayTerminate' => false,
                        'options' => [
                            'route' => '/:entityAlias[/:id]',
                            'defaults' => [
                                'controller' => \ZfMetal\Restful\Controller\MainController::class,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]
];