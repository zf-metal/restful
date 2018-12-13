# ZfMetal\Restful

## Dependencies

- "doctrine/doctrine-orm-module"
- "zf-metal/commons"
- "zf-metal/log"
- "zendframework/zend-mvc"
- "zendframework/zend-json"

## Default Routes

#### Action: Get 1 item
- Method: GET
- URL: /zfmr/api/entityalias/:id

#### Action: Get list 
- Method: GET
- URL: /zfmr/api/entityalias

#### Action: Create Item
- Method: POST
- URL: /zfmr/api/entityalias
- Params: Entity Fields & Values

#### Action: Update Item
- Method: PUT
- URL: /zfmr/api/entityalias/:id
- Params: Entity Fields & Values

#### Action: Delete Item
- Method: DELETE
- URL: /zfmr/api/entityalias/:id

## Custom Routes
You can create your own restful routes and invoke \ZfMetal\Restful\Controller\MainController

```
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
```
## Configure Entities

Add zf-metal-restful.global.php in config/autoload

```
<?php

return  [
    'zf-metal-restful.options' => [
        'entity_aliases' => [
            'alias' => 'entity_class'
        ]
    ]
];
```
