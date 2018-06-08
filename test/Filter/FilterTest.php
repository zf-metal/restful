<?php

namespace ZfMetal\Restful\Test\Filter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use ZfMetal\Restful\Filter\DoctrineQueryBuilderFilter;
use ZfMetal\Restful\Filter\Filters;
use \ZfMetal\Restful\Filter\Type\AbstractFilter;
use \ZfMetal\Restful\Filter\Type\FilterSymbol;
use ZfMetalTest\Restful\Entity\Foo;

class FilterTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {

    }

    public function testEqual()
    {
        $filter = new FilterSymbol("foo", "=23");
        $this->assertEquals(AbstractFilter::EQUAL, $filter->getOperator());
    }


    public function testIsNotNull()
    {
        $filter = new FilterSymbol("foo", "isNotNull");
        $this->assertEquals(AbstractFilter::IS_NOT_NULL, $filter->getOperator());
    }

    public function testIsNull()
    {

        $mockedEm = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository'])
            ->getMock();


        $filter = new FilterSymbol("bar", "isNull");
        $this->assertEquals(AbstractFilter::IS_NULL, $filter->getOperator());

        $filter2 = new FilterSymbol("fuu", "isNotNull");
        $this->assertEquals(AbstractFilter::IS_NOT_NULL, $filter2->getOperator());

        $filters = new Filters();
        $filters->addFilter($filter);
        $filters->addFilter($filter2);

        $qb = new QueryBuilder($mockedEm);
        $qb->select('u')->from(Foo::class, 'u');

        $dqbf = new DoctrineQueryBuilderFilter($qb, $filters);
        $dqbf->applyFilters();

        $dqlWhere = $dqbf->getQb()->getDQLPart("where");
        $parts = $dqlWhere->getParts();
        $this->assertCount(2, $parts);

        $this->assertEquals("u.bar IS NULL",$parts[0]);
        $this->assertEquals("u.fuu IS NOT NULL",$parts[1]);
    }


}