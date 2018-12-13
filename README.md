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


**Obs:** You can create your own restful routes and invoke \ZfMetal\Restful\Controller\MainController

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
