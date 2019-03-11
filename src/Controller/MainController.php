<?php

namespace ZfMetal\Restful\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use ZfMetal\Commons\Facade\Service\FormBuilder;
use ZfMetal\Commons\Facade\Service\FormProcess;
use ZfMetal\Log\Facade\Logger;
use ZfMetal\Restful\Exception\DataBaseException;
use ZfMetal\Restful\Exception\ItemNotExistException;
use ZfMetal\Restful\Exception\ValidationException;
use ZfMetal\Restful\Filter\Builder;
use ZfMetal\Restful\Filter\DoctrineQueryBuilderFilter;
use ZfMetal\Restful\Filter\FilterManager;
use ZfMetal\Restful\Options\ModuleOptions;
use ZfMetal\Restful\Transformation\Policy\Annotation;
use ZfMetal\Restful\Transformation\Policy\Interfaces\Auto;
use ZfMetal\Restful\Transformation\Transform;

/**
 * MainController
 *
 *
 * @method ModuleOptions zfMetalRestfulOptions()
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


    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * Override with your local policies of you entity
     * @var array
     */
    protected $policies = [];


    protected $status = false;

    protected $errors = [];


    /**
     * @var Form
     */
    protected $form = null;

    /**
     * @return Form
     */
    public function getForm()
    {
        if(!$this->form){
            $this->form = FormBuilder::generate($this->getEm(), $this->getEntityClass());
        }
        return $this->form;
    }

    /**
     * @param Form $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }



    public function getEm()
    {
        return $this->em;
    }

    public function setEm(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return FilterManager
     */
    public function getFilterManager()
    {
        if (!$this->filterManager) {
            $this->filterManager = new FilterManager($this->getEm());
        }
        return $this->filterManager;
    }

    /**
     * @param FilterManager $filterManager
     */
    public function setFilterManager($filterManager)
    {
        $this->filterManager = $filterManager;
    }

    /**
     * @return array
     */
    public function getPolicies()
    {
        return $this->policies;
    }

    /**
     * @param array $policies
     */
    public function setPolicies($policies)
    {
        $this->policies = $policies;
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

    /**
     *
     * @return null|Annotation
     */
    protected function getEntityLocalPolicies()
    {
        if ($this->policies) {
            $localPolicy = (new \ZfMetal\Restful\Transformation\Policy\Auto())->inside(
                $this->policies
            );
            return $localPolicy;
        }
        return null;
    }

    protected function filterQuery($query)
    {
        return $this->getFilterManager()->filterEntityByRequestQuery($this->getEntityClass(), $query);
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

            $transform = new Transform($this->getEntityLocalPolicies());
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
                $transform = new Transform($this->getEntityLocalPolicies());
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

        $response = new \ZfMetal\Restful\Model\Response();

        try {
            $entityClass = $this->getEntityClass();
            $object = new $entityClass;
            $this->getForm()->bind($object);
            $this->getForm()->setData($data);

            if ($this->getForm()->isValid()) {
                $this->getEventManager()->trigger('create_' . $this->getEntityAlias() . '_before', $this, ["object" => $object]);
                try {
                    $this->getEm()->persist($object);
                    $this->getEm()->flush();
                } catch (\Exception $e) {
                    Logger::exception($e);
                    throw new DataBaseException();
                }
                $this->getEventManager()->trigger('create_' . $this->getEntityAlias() . '_after', $this, ["object" => $object]);
                $response->setStatus(true);
            } else {
                foreach ($this->getForm()->getMessages() as $key => $messages) {
                    foreach ($messages as $msj) {
                        $response->addError($key, $msj);
                    }
                }
                $response->setStatus(false);
            }

            if (!$response->getStatus()) {
                throw new ValidationException();
            } else {
                $response->setMessage("The item was created successfully");
            }

            $response->setId($object->getId());


            if ($this->zfMetalRestfulOptions()->getReturnItemOnUpdate()) {
                $transform = new Transform($this->getEntityLocalPolicies());
                $item = $transform->toArray($object);
                $response->setItem(json_encode($item));

            }

            $this->getResponse()->setStatusCode(Response::STATUS_CODE_201);

            return new JsonModel($response->toArray());

        } catch (ValidationException $e) {
            return $this->responseValidationException($e, $response->getErrors());
        } catch (DataBaseException $e) {
            return $this->responseDataBaseException($e);
        } catch (\Exception $e) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            $response->setStatus(false);
            $response->setMessage($e->getMessage());
            return new JsonModel($response->toArray());
        }
    }


    public function update($id, $data)
    {

        $response = new \ZfMetal\Restful\Model\Response();


        try {


            $object = $this->getEntityRepository()->find($id);
            if (!$object) {
                throw new ItemNotExistException();
            }


            $this->getForm()->bind($object);
            $this->getForm()->setData($data);

            if ($this->getForm()->isValid()) {
                $this->getEventManager()->trigger('update_' . $this->getEntityAlias() . '_before', $this, ["object" => $object]);
                try {
                    $this->getEm()->persist($object);
                    $this->getEm()->flush();
                } catch (\Exception $e) {
                    Logger::exception($e);
                    throw new DataBaseException();
                }
                $this->getEventManager()->trigger('update_' . $this->getEntityAlias() . '_after', $this, ["object" => $object]);
                $response->setStatus(true);
            } else {
                foreach ($this->getForm()->getMessages() as $key => $messages) {
                    foreach ($messages as $msj) {
                        $response->addError($key, $msj);
                    }
                }
                $response->setStatus(false);
            }

            if (!$response->getStatus()) {
                throw new ValidationException();
            } else {
                $response->setMessage("The item was updated successfully");
            }

            $response->setId($object->getId());


            if ($this->zfMetalRestfulOptions()->getReturnItemOnUpdate()) {
                $transform = new Transform($this->getEntityLocalPolicies());
                $item = $transform->toArray($object);
                $response->setItem(json_encode($item));

            }

            $this->getResponse()->setStatusCode(Response::STATUS_CODE_200);

            return new JsonModel($response->toArray());
        } catch (DataBaseException $e) {
            return $this->responseDataBaseException($e);
        } catch (ItemNotExistException $e) {
            return $this->responseSpecificException($e);
        } catch (ValidationException $e) {
            return $this->responseValidationException($e, $response->getErrors());
        } catch (\Exception $e) {
            return $this->responseGeneralException($e);
        }

    }


    public function delete($id)
    {

        $response = new \ZfMetal\Restful\Model\Response();

        try {
            $object = $this->getEntityRepository()->find($id);
            if (!$object) {
                throw new ItemNotExistException();
            }
            $this->getEm()->remove($object);
            $this->getEm()->flush();

            $response->setStatus(true);
            $response->setMessage("Item Delete");

            return new JsonModel($response->toArray());
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
            "status" => false,
            "message" => $e->getMessage()
        ];
        return new JsonModel($a);
    }


    /**
     * @param \Exception $e
     * @return JsonModel
     */
    public function responseDataBaseException(\Exception $e)
    {

        $response = new \ZfMetal\Restful\Model\Response();
        $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
        $response->setStatus(false);
        $response->setMessage($e->getMessage());
        return new JsonModel($response->toArray());
    }

    /**
     * @param \Exception $e
     * @param null|array $data
     * @return \Zend\View\Model\JsonModel
     */
    public function responseSpecificException(\Exception $e, $data = null)
    {
        $this->getResponse()->setStatusCode($e->getCode());

        $response = new \ZfMetal\Restful\Model\Response();
        $response->setStatus(false);
        $response->setMessage($e->getMessage());


        if ($data) {
            $a = array_merge_recursive($response->toArray(), $data);
        } else {
            $a = $response->toArray();
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
        $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);

        $response = new \ZfMetal\Restful\Model\Response();
        $response->setStatus(false);
        $response->setMessage($e->getMessage());
        $response->setErrors($errors);

        return new JsonModel($response->toArray());
    }

}

