<?php
namespace ZfMetal\Restful\Transformation\Policy\Common;


class Json
{

    public static function format($service,$transformed){
        return json_decode($service,true);
    }

}