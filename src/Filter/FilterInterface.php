<?php
/**
 * Created by PhpStorm.
 * User: crist
 * Date: 15/5/2018
 * Time: 18:07
 */

namespace ZfMetal\Restful\Filter;


interface FilterInterface
{

    /**
     * @return mixed
     */
    function prepare();

    function getInputFilterKey();

    function getInputFilterValue();

    function getFieldName();

    function getOperator();

    function getValue();

}