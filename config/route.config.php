<?php

return [
    'router' => [
        'routes' => [
            'zfmr' => [
                'type' => 'Literal',
                'mayTerminate' => false,
                'options' => [
                    'route' => '/api',
                ],
                'child_routes' => [
                    'api' => [
                        'type' => 'Segment',
                        'mayTerminate' => false,
                        'options' => [
                            'route' => '/:entityAlias[/:id]',
                            'defaults' => [
                                'controller' => \ZfMetal\Restful\Controller\MainController::CLASS,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];