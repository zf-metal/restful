<?php
/**
 * Created by PhpStorm.
 * User: crist
 * Date: 15/5/2018
 * Time: 19:27
 */

namespace ZfMetal\Restful\Filter;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use ZfMetal\Restful\Filter\Type\FilterSimple;

class DoctrineQueryBuilderFilter
{

    const LOGICAL_OPERATOR_AND = "and";
    const LOGICAL_OPERATOR_OR = "or";


    /**
     * QueryBuilder
     *
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $qb;

    /**
     * @var Filters
     */
    protected $filters;



    /**
     * logicalOperator
     *
     * @var string
     */
    protected $logicalOperator = self::LOGICAL_OPERATOR_AND;

    /**
     * DoctrineQueryBuilderFilter constructor.
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param Filters $filters
     * @param string $logicalOperator
     */
    public function __construct(QueryBuilder $qb, Filters $filters, $logicalOperator = self::LOGICAL_OPERATOR_AND) {
        $this->filters = $filters;
        $this->qb = $qb;
        if ($logicalOperator) {
            $this->setLogicalOperator($logicalOperator);
        }
    }

    function getLogicalOperator() {
        return $this->logicalOperator;
    }

    function setLogicalOperator($logicalOperator) {
        if ($logicalOperator != self::LOGICAL_OPERATOR_AND and $logicalOperator != self::LOGICAL_OPERATOR_OR) {
            throw new Exception("Logical Operator must be 'and' or 'or'");
        } else {
            $this->logicalOperator = $logicalOperator;
        }
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQb()
    {
        return $this->qb;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     */
    public function setQb($qb)
    {
        $this->qb = $qb;
    }

    /**
     * @return Filters
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param Filters $filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    public function applyFilters(){
        foreach ($this->getFilters() as $key => $filter) {
            $this->applyFilter($filter, $key);
        }
        return $this->getQb();
    }


    public function applyFilter(\ZfMetal\Restful\Filter\FilterInterface $filter, $key)
    {

        $ra = $this->qb->getRootAliases()[0];
        $colname = $filter->getFieldName();


        $colString = $ra . "." . $colname;

        //toreview for more filters in the same column
        $valueParameterName = ":" . $colname . $key;

        $value = $filter->getValue();


        $expr = new Expr();

        switch ($filter->getOperator()) {
            case FilterSimple::LIKE:
                $where = $expr->like($colString, $valueParameterName);
                $this->qb->setParameter($valueParameterName, '%' . $value . '%');
                break;
            case FilterSimple::LIKE_LEFT:
                $where = $expr->like($colString, $valueParameterName);
                $this->qb->setParameter($valueParameterName, '%' . $value);
                break;
            case FilterSimple::LIKE_RIGHT:
                $where = $expr->like($colString, $valueParameterName);
                $this->qb->setParameter($valueParameterName, $value . '%');
                break;
            case FilterSimple::NOT_LIKE:
                $where = $expr->notLike($colString, $valueParameterName);
                $this->qb->setParameter($valueParameterName, '%' . $value . '%');
                break;
            case FilterSimple::NOT_LIKE_LEFT:
                $where = $expr->notLike($colString, $valueParameterName);
                $this->qb->setParameter($valueParameterName, '%' . $value);
                break;
            case FilterSimple::NOT_LIKE_RIGHT:
                $where = $expr->notLike($colString, $valueParameterName);
                $this->qb->setParameter($valueParameterName, $value . '%');
                break;
            case FilterSimple::EQUAL:
                $where = $expr->eq($colString, $valueParameterName);
                $this->qb->setParameter($valueParameterName, $value);
                break;
            case FilterSimple::NOT_EQUAL:
                $where = $expr->neq($colString, $valueParameterName);
                $this->qb->setParameter($valueParameterName, $value);
                break;
            case FilterSimple::GREATER_EQUAL:
                $where = $expr->gte($colString, $valueParameterName);
                $this->qb->setParameter($valueParameterName, $value);
                break;
            case FilterSimple::GREATER:
                $where = $expr->gt($colString, $valueParameterName);
                $this->qb->setParameter($valueParameterName, $value);
                break;
            case FilterSimple::LESS_EQUAL:
                $where = $expr->lte($colString, $valueParameterName);
                $this->qb->setParameter($valueParameterName, $value);
                break;
            case FilterSimple::LESS:
                $where = $expr->lt($colString, $valueParameterName);
                $this->qb->setParameter($valueParameterName, $value);
                break;
            case FilterSimple::BETWEEN:
                $minParameterName = ':' . str_replace('.', '', $colString . '0');
                $maxParameterName = ':' . str_replace('.', '', $colString . '1');
                $where = $expr->between($colString, $minParameterName, $maxParameterName);
                $this->qb->setParameter($minParameterName, $value[0]);
                $this->qb->setParameter($maxParameterName, $value[1]);
                break;
            default:
                throw new \InvalidArgumentException('This operator is currently not supported: ' . $filter->getOperator());
                break;
        }

        if (!empty($where)) {
            if ($this->logicalOperator == self::LOGICAL_OPERATOR_AND) {
                $this->qb->andWhere($where);
            } else if ($this->logicalOperator == self::LOGICAL_OPERATOR_OR) {
                $this->qb->orWhere($where);
            }
        }
        return $this->qb;
    }


}