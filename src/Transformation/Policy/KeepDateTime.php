<?php
namespace  ZfMetal\Restful\Transformation\Policy;

use \ZfMetal\Restful\Transformation\Policy\Interfaces;
use \Doctrine\ORM\Mapping as ORM;

/** ITransformable policy.
 * Donesn't convert \DateTime to ISO8601 string in ITransformabe::toArray
 * @see http://www.iso.org/iso/catalogue_detail?csnumber=40874
 * Note: ITransformable always works in UTC timezone.
 * @Annotation */
class KeepDateTime
    extends  \ZfMetal\Restful\Transformation\Policy\Annotation
    implements Interfaces\KeepDateTime {

    public $priority = 0.2;
}