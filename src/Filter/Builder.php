<?php
/**
 * Created by PhpStorm.
 * User: crist
 * Date: 15/5/2018
 * Time: 18:17
 */

namespace ZfMetal\Restful\Filter;


use ZfMetal\Restful\Filter\Type\FilterCode;
use ZfMetal\Restful\Filter\Type\FilterSimple;
use ZfMetal\Restful\Filter\Type\FilterSymbol;

class Builder
{

    const TYPE_SIMPLE = "simple";
    const TYPE_SYMBOL = "symbol";
    const TYPE_CODE = "code";

    const TYPE_SIMPLE_CLASS = FilterSimple::class;
    const TYPE_SYMBOL_CLASS = FilterSymbol::class;
    const TYPE_CODE_CLASS = FilterCode::class;

    /**
     * @var Filters
     */
    protected $filters;

    /**
     * @var \Zend\Stdlib\ParametersInterface|mixed
     */
    protected $query;

    /**
     * @var string
     */
    protected $type;

    /**
     * Builder constructor.
     * @param mixed|\Zend\Stdlib\ParametersInterface $query
     */
    public function __construct($query,$type = SELF::TYPE_SIMPLE)
    {
        $this->setQuery($query);
        $this->setType($type);
        $this->filters = new Filters();
    }

    public function build(){

        foreach ($this->getQuery() as $key => $value) {
            $filter = new $this->type($key,$value);
            $this->filters->addFilter($filter);
        }
        return $this->filters;
    }

    /**
     * @return Filters
     */
    public function getFilters()
    {
        return $this->filters;
    }



    /**
     * @return mixed|\Zend\Stdlib\ParametersInterface
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed|\Zend\Stdlib\ParametersInterface $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @throws \Exception
     */
    public function setType($type)
    {
        if($type == self::TYPE_SIMPLE){
            $this->type = self::TYPE_SIMPLE_CLASS;
        }else if($type == self::TYPE_SYMBOL){
            $this->type = self::TYPE_SYMBOL_CLASS;
        }else if($type == self::TYPE_CODE){
            $this->type = self::TYPE_CODE_CLASS;
        }else{
            throw new \Exception("Filter Type Not Exists");
        }

    }





}