<?php

namespace ZfMetal\Restful\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use ZfMetal\Commons\Facade\Service\FormBuilder;
use ZfMetal\Commons\Facade\Service\FormProcess;
use ZfMetal\Restful\Exception\ItemNotExistException;
use ZfMetal\Restful\Exception\ValidationException;
use ZfMetal\Restful\Filter\Builder;
use ZfMetal\Restful\Filter\DoctrineQueryBuilderFilter;
use ZfMetal\Restful\Options\ModuleOptions;
use ZfMetal\Restful\Transformation\Transform;

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
        $this->layout()->setTerminal(true);
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


    protected function filterQuery($query)
    {

        $qb = $this->getEntityRepository()->createQueryBuilder('u')->select('u');

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

            $transform = new Transform();
            $results = $transform->toArrays($objects);

            return new JsonModel($results);

        } catch (\Exception $e) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            $a = [
                "message" => $e->getMessage()
            ];
            return new JsonModel($a);
        }
    }


    /**
     * Return list of resources
     *
     * @return array
     */
    public function get($id = null)
    {
        try {

            if ($id) {
                $object = $this->getEntityRepository()->find($id);
                if (!$object) {
                    throw new ItemNotExistException();
                }

                $transform = new Transform();
                $results = $transform->toArray($object);

            }

            return new JsonModel($results);
        } catch (ItemNotExistException $e) {
            return $this->responseSpecificException($e);
        } catch (\Exception $e) {
            return $this->responseGeneralException($e);
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

            if (!$result["status"]) {
                throw new ValidationException();
            } else {
                $result["message"] = "The item was created successfully";
            }

            $this->getResponse()->setStatusCode(Response::STATUS_CODE_201);

            return new JsonModel($result);

        } catch (ValidationException $e) {
            return $this->responseValidationException($e, $result["errors"]);
        } catch (\Exception $e) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            $a = [
                "message" => $e->getMessage()
            ];
            return new JsonModel($a);
        }
    }


    public function update($id, $data)
    {
        try {

            $form = FormBuilder::generate($this->getEm(), $this->getEntityClass());

            $object = $this->getEntityRepository()->find($id);
            if (!$object) {
                throw new ItemNotExistException();
            }


            $form->bind($object);

            $result = FormProcess::process($this->getEm(), $form, false, $data)->getArrayResult();

            if (!$result["status"]) {
                throw new ValidationException();
            } else {
                $result["message"] = "The item was updated successfully";
            }

            $this->getResponse()->setStatusCode(Response::STATUS_CODE_200);

            return new JsonModel($result);

        } catch (ItemNotExistException $e) {
            return $this->responseSpecificException($e);
        } catch (ValidationException $e) {
            return $this->responseValidationException($e, $result["errors"]);
        } catch (\Exception $e) {
            return $this->responseGeneralException($e);
        }

    }


    public function delete($id)
    {

        try {
            $object = $this->getEntityRepository()->find($id);
            if (!$object) {
                throw new ItemNotExistException();
            }
            $this->getEm()->remove($object);
            $this->getEm()->flush();
            $a = [
                "message" => "Item Delete"
            ];

            return new JsonModel($a);
        } catch (ItemNotExistException $e) {
            return $this->responseSpecificException($e);
        } catch (\Exception $e) {
            return $this->responseGeneralException($e);
        }


    }

    /**
     * @param \Exception $e
     * @return JsonModel
     */
    public function responseGeneralException(\Exception $e)
    {
        $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
        $a = [
            "message" => $e->getMessage()
        ];
        return new JsonModel($a);
    }

    /**
     * @param \Exception $e
     * @param null|array $data
     * @return \Zend\View\Model\JsonModel
     */
    public function responseSpecificException(\Exception $e, $data = null)
    {
        $this->getResponse()->setStatusCode($e->getCode());
        $a = [
            "message" => $e->getMessage()
        ];

        if ($data) {
            $a = array_merge_recursive($a, $data);
        }

        $jm = new JsonModel($a);
        return $jm;
    }

    /**
     * @param \Exception $e
     * @param null|array $data
     * @return \Zend\View\Model\JsonModel
     */
    public function responseValidationException(\Exception $e, $errors = null)
    {
        $this->getResponse()->setStatusCode($e->getCode());
        $a = [
            "message" => $e->getMessage(),
            "errors" => $errors
        ];

        $jm = new JsonModel($a);
        return $jm;
    }

}

