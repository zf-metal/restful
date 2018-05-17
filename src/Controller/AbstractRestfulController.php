<?php

namespace ZfMetal\Restful\Controller;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Indaxia\OTR\Traits\Transformable;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractRestfulController as ARC;
use Zend\View\Model\JsonModel;
use ZfMetal\Commons\Facade\Service\FormBuilder;
use ZfMetal\Commons\Facade\Service\FormProcess;
use ZfMetal\Restful\Exception\ItemNotExistException;
use ZfMetal\Restful\Exception\MethodNotAllowed;
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
abstract class AbstractRestfulController extends ARC
{


    /**
     * Return list of resources
     *
     * @return array
     */
    public function get($id = null)
    {
        try {
            throw new MethodNotAllowed();
        } catch (MethodNotAllowed $e) {
            return $this->responseSpecificException($e);
        } catch (\Exception $e) {
            return $this->responseGeneralException($e);
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
            throw new MethodNotAllowed();
        } catch (MethodNotAllowed $e) {
            return $this->responseSpecificException($e);
        } catch (\Exception $e) {
            return $this->responseGeneralException($e);
        }
    }


    public function create($data)
    {
        try {
            throw new MethodNotAllowed();
        } catch (MethodNotAllowed $e) {
            return $this->responseSpecificException($e);
        } catch (\Exception $e) {
            return $this->responseGeneralException($e);
        }
    }


    public function update($id, $data)
    {
        try {
            throw new MethodNotAllowed();
        } catch (MethodNotAllowed $e) {
            return $this->responseSpecificException($e);
        } catch (\Exception $e) {
            return $this->responseGeneralException($e);
        }

    }


    public function delete($id)
    {
        try {
            throw new MethodNotAllowed();
        } catch (MethodNotAllowed $e) {
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
     * @return JsonModel
     */
    public function responseSpecificException(\Exception $e)
    {
        $this->getResponse()->setStatusCode($e->getCode());
        $a = [
            "message" => $e->getMessage()
        ];
        return new JsonModel($a);
    }

}

