<?php


return  [
    'zf-metal-restful.options' => [
        'return_item_on_update' => false,
        //Match URL Param entityAlias with Doctrine Entity
        'entity_aliases' => [
            'alias' => 'entity_class'
        ],
        //Map of Field to use for autocomplete action
        'entity_autocomplete_keys' => [
            'alias' => 'field'
        ]

    ]
];