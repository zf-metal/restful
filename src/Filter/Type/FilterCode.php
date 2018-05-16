<?php

namespace ZfMetal\Restful\Filter\Type;

use ZfMetal\Restful\Filter\FilterInterface;


/**
 * Description of Filter
 *
 * @author Cristian Incarnato <cristian.cdi@gmail.com>
 */
class FilterCode extends AbstractFilter implements FilterInterface
{


    /**
     * @var bool
     */
    protected $r = false;


    /**
     * FilterCode constructor.
     * @param $inputFilterKey
     * @param $inputFilterValue
     */
    function __construct($inputFilterKey, $inputFilterValue)
    {
        $this->inputFilterKey = $inputFilterKey;
        $this->value = $inputFilterValue;
        $this->inputFilterValue = trim((string)$inputFilterValue);
        $this->prepare();
    }


    protected function equal()
    {
        if (key_exists("eq", $this->inputFilterKey)) {
            $this->operator = self::EQUAL;
            $this->r = true;
        }
    }

    public function prepare()
    {
//TODO ALL
    }



}
