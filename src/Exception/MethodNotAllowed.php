<?php
/**
 * Created by PhpStorm.
 * User: crist
 * Date: 16/5/2018
 * Time: 11:38
 */

namespace ZfMetal\Restful\Exception;


class MethodNotAllowed extends \Exception
{

    protected $message = "Method Not Allowed";
    protected $code = 405;
}