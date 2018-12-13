<?php

$date = new \DateTime();

return[
    'zf-metal-log.options' => [
        'log_file' => __DIR__.'/../../logs/' . $date->format('Y-m-d') . '.log',
        'filter' => \Zend\Log\Logger::INFO,
    ]
];