<?php

namespace ZfMetal\Restful\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Indaxia\OTR\Traits\Transformable;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use ZfMetal\Commons\Facade\Service\FormBuilder;
use ZfMetal\Commons\Facade\Service\FormProcess;
use ZfMetal\Restful\Filter\Builder;
use ZfMetal\Restful\Filter\DoctrineQueryBuilderFilter;
use ZfMetal\Restful\Options\ModuleOptions;

/**
 * MainController
 *
 *
 *
 * @author
 * @license
 * @link
 */
class MainController extends AbstractRestfulController
{

    const CONTENT_TYPE_JSON = 'json';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em = null;


    /**
     * @var string
     */
    protected $entityClass;


    /**
     * @var string
     */
    protected $entityAlias;



    public function getEm()
    {
        return $this->em;
    }

    public function setEm(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getEntityAlias()
    {
        if (!$this->entityAlias) {
            $entityAlias = $this->params("entityAlias");
            if ($entityAlias) {
                $this->entityAlias = $entityAlias;
            } else {
                throw new \Exception("EntityAlias route parameter not found");
            }
        }
        return $this->entityAlias;
    }

    /**
     * @param string $entityAlias
     */
    public function setEntityAlias($entityAlias)
    {
        $this->entityAlias = $entityAlias;
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function getEntityClass()
    {
        if (!$this->entityClass) {
            if (key_exists($this->getEntityAlias(), $this->getOptions()->getEntityAliases())) {
                $this->entityClass = $this->getOptions()->getEntityAliases()[$this->getEntityAlias()];
            } else {
                throw new \Exception("EntityAlias is not defined in config");
            }
        }

        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }


    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return ModuleOptions
     */
    public function getOptions()
    {
        return $this->zfMetalRestfulOptions();
    }


    /**
     * @return \Doctrine\ORM\EntityRepository
     * @throws \Exception
     */
    public function getEntityRepository()
    {
        return $this->getEm()->getRepository($this->getEntityClass());
    }

    protected function findAll()
    {
        //$data= $this->getEntityRepository()->findAll();

        $qb = $this->getEntityRepository()->createQueryBuilder('u');

        $data = $qb->select('u')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $data;
    }


    protected function filterQuery($query)
    {

        $qb = $this->getEntityRepository()->createQueryBuilder('u')->select('u');

        $builder = new Builder($query, Builder::TYPE_SYMBOL);
        $builder->build();

        $DoctrineQueryBuilderFilter = new DoctrineQueryBuilderFilter($qb, $builder->getFilters());
        $qb = $DoctrineQueryBuilderFilter->applyFilters();


        return $qb->getQuery()->getResult();
    }

    /**
     * Return list of resources
     *
     * @return array
     */
    public function get($id = null)
    {
        try {
            $query = $this->getRequest()->getQuery();

            $objects = $this->filterQuery($query);

            $results = Transformable::toArrays($objects);

            return new JsonModel($results);

        } catch (\Exception $e) {
            return $this->sendErrorResponse($e);
        }
    }

    /**
     * Return list of resources
     *
     * @return array
     */
    public function getList()
    {
        try {
            $query = $this->getRequest()->getQuery();

            $objects = $this->filterQuery($query);

            $results = Transformable::toArrays($objects);

            return new JsonModel($results);

        } catch (\Exception $e) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            $a = [
                "message" => $e->getMessage()
            ];
            return new JsonModel($a);
        }
    }


    public function create($data)
    {
        try {

            $form = FormBuilder::generate($this->getEm(), $this->getEntityClass());

            $entityClass = $this->getEntityClass();
            $object = new $entityClass;
            $form->bind($object);

            $result = FormProcess::process($this->getEm(), $form, false, $data)->getArrayResult();

            return new JsonModel($result);

        } catch (\Exception $e) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            $a = [
                "message" => $e->getMessage()
            ];
            return new JsonModel($a);
        }
    }


    public function update($id,$data){
        try {

            $form = FormBuilder::generate($this->getEm(), $this->getEntityClass());

            $object = $this->getEntityRepository()->find($id);
            $form->bind($object);

            $result = FormProcess::process($this->getEm(), $form, false, $data)->getArrayResult();

            return new JsonModel($result);

        } catch (\Exception $e) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            $a = [
                "message" => $e->getMessage()
            ];
            return new JsonModel($a);
        }

    }


    public function delete($id){
        try {
            $object = $this->getEntityRepository()->find($id);
            $this->getEm()->remove($object);
            $this->getEm()->flush();
            $a = [
                "message" => "Object Delete"
            ];

            return new JsonModel($a);
        } catch (\Exception $e) {
           return $this->sendErrorResponse($e);
        }


    }

    /**
     * @param $e
     * @return JsonModel
     */
    public function sendErrorResponse(\Exception $e)
    {
        $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
        $a = [
            "messages" => $e->getMessage()
        ];
        return new JsonModel($a);
    }

}

