<?php

namespace ZfMetal\Restful\Filter\Type;

use ZfMetal\Restful\Filter\FilterInterface;


/**
 * Description of Filter
 *
 * @author Cristian Incarnato <cristian.cdi@gmail.com>
 */
class FilterSimple extends AbstractFilter implements FilterInterface
{

    /**
     * define if a relational column
     *
     * @var boolean
     */
    protected $relational = false;

    /**
     * FilterSimple only filter by exact match between fieldname and value.
     * @param $inputFilterKey
     * @param $inputFilterValue
     */
    function __construct($inputFilterKey, $inputFilterValue)
    {
        $this->inputFilterKey = $inputFilterKey;
        $this->inputFilterValue = trim((string)$inputFilterValue);
        $this->prepare();
    }

    public function prepare()
    {

        $this->fieldName = $this->getInputFilterKey();
        $this->operator = self::EQUAL;
        $this->value = $this->getInputFilterValue();
    }





}
