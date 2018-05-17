<?php

return [
    'view_manager' => [
        'display_exceptions' => true,
        'display_not_found_reason' => false,
       // 'exception_template' => 'zf-metal/restful/error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/zf-metal/restful/layout/layout.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ],
];
