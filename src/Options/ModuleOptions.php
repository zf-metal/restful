<?php

namespace ZfMetal\Restful\Options;

/**
 * ModuleOptions
 *
 *
 *
 * @author
 * @license
 * @link
 */
class ModuleOptions extends \Zend\Stdlib\AbstractOptions
{

    private $entityAliases = '';

    public function getEntityAliases()
    {
        return $this->entityAliases;
    }

    public function setEntityAliases($entityAliases)
    {
        $this->entityAliases= $entityAliases;
    }


}

