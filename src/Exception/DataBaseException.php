<?php
/**
 * Created by PhpStorm.
 * User: crist
 * Date: 16/5/2018
 * Time: 11:38
 */

namespace ZfMetal\Restful\Exception;


class DataBaseException extends \Exception
{

    protected $message = "There was a problem persisting the data";
    protected $code = 500;
}