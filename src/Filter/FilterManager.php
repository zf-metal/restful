<?php

namespace ZfMetal\Restful\Filter;

use Zend\Http\Request;


class FilterManager
{


    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em = null;


    /**
     * FilterManager constructor.
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    public function getEntityRepository($entityClassName)
    {
        return $this->getEm()->getRepository($entityClassName);
    }


    /**
     * @param $entityClassName
     * @param array $query
     * @return array
     */
    public function filterEntityByRequestQuery($entityClassName, \Zend\Stdlib\ParametersInterface $query)
    {

        $qb = $this->getEntityRepository($entityClassName)->createQueryBuilder('u')->select('u');

        //PAGINATION
        if ($query["page"]) {
            if (is_numeric($query["page"]) && $query["page"] > 1) {
                $num = ($query["limit"] && is_numeric($query["limit"])) ? $query["limit"] : 10;
                $qb->setFirstResult(($query["page"] - 1) * $num);
            }
            unset($query["page"]);
        }

        //LIMIT
        if ($query["limit"] ) {
            if(is_numeric($query["limit"])){
                $qb->setMaxResults($query["limit"]);
            }
            unset($query["limit"]);
        }

        //ORDER
        if ($query["orderby"]) {

            $orderBy =  $query["orderby"];

            if ($query["orderdirection"] == "DESC" || $query["orderdirection"] == "ASC") {
                $orderDirection = $query["orderdirection"];
                unset($query["orderdirection"]);
            } else {
                $orderDirection = "ASC";
            }

            $qb->orderBy('u.' . $orderBy, $orderDirection);
            unset($query["orderby"]);

        }

        //FILTERS
        $filterType = Builder::TYPE_SYMBOL;

        if (key_exists("filterType", $query)) {
            $filterType = $query["filterType"];
        }

        $builder = new Builder($query, $filterType);
        $builder->build();

        $DoctrineQueryBuilderFilter = new DoctrineQueryBuilderFilter($qb, $builder->getFilters());
        $qb = $DoctrineQueryBuilderFilter->applyFilters();

        return $qb->getQuery()->getResult();
    }


    public function getEm()
    {
        return $this->em;
    }

    public function setEm(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }


}
