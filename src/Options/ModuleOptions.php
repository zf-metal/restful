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

    private $entityAutocompleteKeys = '';


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

    /**
     * @return string
     */
    public function getEntityAutocompleteKeys()
    {
        return $this->entityAutocompleteKeys;
    }

    /**
     * @param string $entityAutocompleteKeys
     */
    public function setEntityAutocompleteKeys($entityAutocompleteKeys)
    {
        $this->entityAutocompleteKeys = $entityAutocompleteKeys;
    }






}

