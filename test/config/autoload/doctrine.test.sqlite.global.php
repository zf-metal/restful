<?php
namespace ZfMetalTest\Restful;

return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => \Doctrine\DBAL\Driver\PDOSqlite\Driver::class,
                'params' => array(
                    'path' => __DIR__."/../../data/sqlite.db"
                )
            ),
        ),
        'driver' => array(
            __NAMESPACE__ => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => __DIR__ . '/../../Entity'
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__
                )
            )
        )

    )
);