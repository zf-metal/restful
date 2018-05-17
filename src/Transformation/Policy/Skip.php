<?php
namespace  ZfMetal\Restful\Transformation\Policy;

use \Doctrine\ORM\Mapping as ORM;

/** ITransformable policy.
 * Skips the field in both ITransformabe::fromArray and ITransformabe::toArray.
 * Opposite to Accept.
 * @Annotation */
class Skip 
    extends  \ZfMetal\Restful\Transformation\Policy\Annotation
    implements \ZfMetal\Restful\Transformation\Policy\Interfaces\Skip
{        
    public function inside(array $policy) {
        throw new  \ZfMetal\Restful\Transformation\Exceptions\PolicyException("Policy\\Skip cannot contain policies");
    }

    public $priority = 0.9;
}