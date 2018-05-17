<?php
/**
 * Created by PhpStorm.
 * User: crist
 * Date: 16/5/2018
 * Time: 11:38
 */

namespace ZfMetal\Restful\Exception;


class ItemNotExistException extends \Exception
{

    protected $message = "The item does not exist";
    protected $code = 404;
}