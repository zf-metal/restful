<?php

return [
    'router' => [
        'routes' => [
            'zfmr' => [
                'type' => 'Literal',
                'mayTerminate' => false,
                'options' => [
                    'route' => '/zfmr',
                ],
                'child_routes' => [
                    'api' => [
                        'type' => 'Segment',
                        'mayTerminate' => false,
                        'options' => [
                            'route' => '/api/:entityAlias[/:id]',
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