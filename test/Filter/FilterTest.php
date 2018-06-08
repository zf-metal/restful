<?php
namespace ZfMetal\Restful\Test\Filter;

use \ZfMetal\Restful\Filter\Type\AbstractFilter;
use \ZfMetal\Restful\Filter\Type\FilterSymbol;

class FilterTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {

    }

    public function testEqual()
    {
       $filter = new FilterSymbol("foo","=23");
       $this->assertEquals(AbstractFilter::EQUAL,$filter->getOperator());
    }


}