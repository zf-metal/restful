<?php
/**
 * Created by PhpStorm.
 * User: crist
 * Date: 16/5/2018
 * Time: 11:38
 */

namespace ZfMetal\Restful\Exception;


class ValidationException extends \Exception
{

    protected $message = "Validation errors in your request";
    protected $code = 400;
}