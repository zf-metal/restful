<?php

namespace ZfMetal\Restful;

/**
 * Module
 *
 *
 *
 * @author
 * @license
 * @link
 */
class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }


}

