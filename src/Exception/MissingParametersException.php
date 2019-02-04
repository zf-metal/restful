<?php

namespace ZfMetal\Restful\Exception;


class MissingParametersException extends \Exception
{

    protected $message = "Missing parameters in your request";
    protected $code = 400;
}