<?php
namespace ZfMetal\Restful\Facade\Service;

use ZfMetal\Commons\Facade\Accessor;

class Resource extends Accessor
{

    public static function getServiceName()
    {
        return 'zf-metal-restful-resource';
    }

}