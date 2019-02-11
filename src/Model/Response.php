<?php
/**
 * Created by IntelliJ IDEA.
 * User: crist
 * Date: 11/2/2019
 * Time: 12:59
 */

namespace ZfMetal\Restful\Model;


class Response
{

    /**
     * @var bool
     */
    public $status = false;

    /**
     * @var string
     */
    public $message = "";


    /**
     * @var array
     */
    public $errors = [];


    /**
     * @var array
     */
    public $items = [];

    /**
     * @var
     */
    public $item;

    /**
     * @var
     */
    public $id;

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }


    /**
     * @param $key string
     * @param $message string
     */
    public function addError($key,$message)
    {
        $this->errors[$key][] = $message;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param mixed $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }



    public function toArray(){

        $result = [];

        $result["status"] =  $this->getStatus();

        if($this->getErrors()){
            $result["errors"] =  $this->getErrors();
        }

        if($this->getMessage()){
            $result["message"] =  $this->getMessage();
        }

        if($this->getItems()){
            $result["items"] =  $this->getItems();
        }

        if($this->getItem()){
            $result["item"] =  $this->getItem();
        }

        if($this->getId()){
            $result["id"] =  $this->getId();
        }

        return $result;
    }



}