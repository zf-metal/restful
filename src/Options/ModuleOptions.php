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


    private $returnItemOnUpdate = false;

    public function getEntityAliases()
    {
        return $this->entityAliases;
    }

    public function setEntityAliases($entityAliases)
    {
        $this->entityAliases= $entityAliases;
    }

    /**
     * @return bool
     */
    public function getReturnItemOnUpdate()
    {
        return $this->returnItemOnUpdate;
    }

    /**
     * @param bool $returnItemOnUpdate
     */
    public function setReturnItemOnUpdate($returnItemOnUpdate)
    {
        $this->returnItemOnUpdate = $returnItemOnUpdate;
    }





}

