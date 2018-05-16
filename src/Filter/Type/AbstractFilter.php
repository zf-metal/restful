<?php
/**
 * Created by PhpStorm.
 * User: crist
 * Date: 15/5/2018
 * Time: 19:56
 */

namespace ZfMetal\Restful\Filter\Type;


abstract class AbstractFilter
{

    const EQUAL = '= %s';
    const NOT_EQUAL = '!= %s';
    const GREATER_EQUAL = '>= %s';
    const GREATER = '> %s';
    const LESS_EQUAL = '<= %s';
    const LESS = '< %s';
    const IN = '=(%s)';
    const NOT_IN = '!=(%s)';
    const BETWEEN = '%s <> %s';


    const LIKE = '~ *%s*';
    const LIKE_LEFT = '~ *%s';
    const LIKE_RIGHT = '~ %s*';
    const NOT_LIKE = '!~ *%s*';
    const NOT_LIKE_LEFT = '!~ *%s';
    const NOT_LIKE_RIGHT = '!~ %s*';


    /**
     * original value from input
     *
     * @var string
     */
    protected $inputFilterKey;

    /**
     * original value from input
     *
     * @var string
     */
    protected $inputFilterValue;


    /**
     * Field Name of Entity
     *
     * @var String
     */
    protected $fieldName;

    /**
     * Operator for filter
     *
     * @var string
     */
    protected $operator = self::EQUAL;

    /**
     * value to filter
     *
     * @var string|array
     */
    protected $value;

    /**
     * @return string
     */
    public function getInputFilterKey()
    {
        return $this->inputFilterKey;
    }


    function getInputFilterValue()
    {
        return $this->inputFilterValue;
    }

    /**
     * @return String
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }


    function getOperator()
    {
        return $this->operator;
    }

    function getValue()
    {
        return $this->value;
    }
}