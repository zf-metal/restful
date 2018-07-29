<?php
namespace ZfMetal\Restful\Transformation\Policy\Common;


class Id
{

    public static function transform($object,$transformed){
        if($object && method_exists($object,"getId")){
            return $object->getId();
        }
        return $transformed;
    }

}