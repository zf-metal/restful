<?php

return  [
    'zf-metal-restful.options' => [
        'return_item_on_update' => false,
        'entity_aliases' => [
            'foo' => \ZfMetalTest\Restful\Entity\Foo::class
        ]
    ]
];