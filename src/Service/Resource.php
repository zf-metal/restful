<?php

namespace ZfMetal\Restful\Service;

use ZfMetal\Restful\Options\ModuleOptions;

class Resource
{


    /**
     * @var \Doctrine\ORM\EntityManager
     */
    public $em = null;

    /**
     * @var ModuleOptions
     */
    public $moduleOptions = null;

    public function getEm()
    {
        return $this->em;
    }

    public function setEm(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return ModuleOptions
     */
    public function getModuleOptions()
    {
        return $this->moduleOptions;
    }

    /**
     * @param ModuleOptions $moduleOptions
     */
    public function setModuleOptions($moduleOptions)
    {
        $this->moduleOptions = $moduleOptions;
    }




    public function __construct(\Doctrine\ORM\EntityManager $em, ModuleOptions $moduleOptions)
    {
        $this->em = $em;
        $this->moduleOptions = $moduleOptions;
    }

    public function getEntityRepository($entityClassName){
        return $this->getEm()->getRepository($entityClassName);
    }


    public function getList($entityClassName,$json = true){

        $data = $this->getEntityRepository($entityClassName)
            ->createQueryBuilder('u')
            ->select('u')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if($json){
            return json_encode($data);
        }

        return $data;
    }

}