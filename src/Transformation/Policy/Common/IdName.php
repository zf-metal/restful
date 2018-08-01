<?php
namespace ZfMetal\Restful\Transformation\Policy\Common;


class IdName
{

    public static function transform($object,$transformed){
        if($object && method_exists($object,"getId") && method_exists($object,"getName")){
            return ["id"=> $object->getId(),"name" => $object->getName()];
        }
        return $transformed;
    }


}