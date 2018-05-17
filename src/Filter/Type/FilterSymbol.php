<?php

namespace ZfMetal\Restful\Filter\Type;

use ZfMetal\Restful\Filter\FilterInterface;


/**
 * Description of Filter
 *
 * @author Cristian Incarnato <cristian.cdi@gmail.com>
 */
class FilterSymbol extends AbstractFilter implements FilterInterface
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
        $this->fieldName = $inputFilterKey;
        $this->inputFilterValue = trim((string)$inputFilterValue);
        $this->prepare();
    }

    protected function equal()
    {
        if (substr($this->inputFilterValue, 0, 2) == '==') {
            $this->operator = self::EQUAL;
            $this->value = substr($this->inputFilterValue, 2);
            $this->r = true;
        } elseif (substr($this->inputFilterValue, 0, 1) == '=') {
            $this->operator = self::EQUAL;
            $this->value = substr($this->inputFilterValue, 1);
            $this->r = true;
        }
        return $this->r;
    }

    protected function greater()
    {
        if (substr($this->inputFilterValue, 0, 2) == '>=') {
            $this->operator = self::GREATER_EQUAL;
            $this->value = substr($this->inputFilterValue, 2);
            $this->r = true;
        } else if (substr($this->inputFilterValue, 0, 1) == '>') {
            $this->operator = self::GREATER;
            $this->value = substr($this->inputFilterValue, 1);
            $this->r = true;
        }
        return $this->r;
    }

    protected function less()
    {

        if (substr($this->inputFilterValue, 0, 2) == '<=') {
            $this->operator = self::LESS_EQUAL;
            $this->value = substr($this->inputFilterValue, 2);
            $this->r = true;
        } elseif (substr($this->inputFilterValue, 0, 1) == '<') {
            $this->operator = self::LESS;
            $this->value = substr($this->inputFilterValue, 1);
            $this->r = true;
        }
        return $this->r;
    }

    protected function between()
    {
        if (strpos($this->inputFilterValue, '<>') !== false) {
            $this->operator = self::BETWEEN;
            $this->value = explode('<>', $this->inputFilterValue);
            $this->r = true;
        }
        return $this->r;
    }

    public function prepare()
    {
        if (!$this->equal()) {
            if (!$this->greater()) {
                if ((!$this->less())) {
                    if(!$this->between()){
                    //TODO like, in, not like
                    //Default Equal
                    $this->operator = self::EQUAL;
                    $this->value =  $this->inputFilterValue;
                    }
                }
            }
        }

    }

    /**
     * Prepare FILTER
     *
     * @param type $var description
     * @return type
     */
    private function prepareOld()
    {

        if ($this->getColumn() instanceof \ZfMetal\Datagrid\Column\RelationalColumn) {
            $operator = self::EQUAL;
            $value = $this->inputFilterValue;
            $this->setRelational(true);
        } else if (substr($this->inputFilterValue, 0, 2) == '=(') {
            $operator = self::IN;
            $value = substr($this->inputFilterValue, 2);
            if (substr($value, -1) == ')') {
                $value = substr($value, 0, -1);
            }
        } elseif (substr($this->inputFilterValue, 0, 3) == '!=(') {
            $operator = self::NOT_IN;
            $value = substr($this->inputFilterValue, 3);
            if (substr($value, -1) == ')') {
                $value = substr($value, 0, -1);
            }
        } elseif (substr($this->inputFilterValue, 0, 2) == '!=' || substr($this->inputFilterValue, 0, 2) == '<>') {
            $operator = self::NOT_EQUAL;
            $value = substr($this->inputFilterValue, 2);
        } elseif (substr($this->inputFilterValue, 0, 2) == '!~' || substr($this->inputFilterValue, 0, 1) == '!') {
            // NOT LIKE or NOT EQUAL
            if (substr($this->inputFilterValue, 0, 2) == '!~') {
                $value = trim(substr($this->inputFilterValue, 2));
            } else {
                $value = trim(substr($this->inputFilterValue, 1));
            }
            if (substr($this->inputFilterValue, 0, 2) == '!~' || (substr($value, 0, 1) == '%' || substr($value, -1) == '%' || substr($value, 0, 1) == '*' || substr($value, -1) == '*')) {
                // NOT LIKE
                if ((substr($value, 0, 1) == '*' && substr($value, -1) == '*') || (substr($value, 0, 1) == '%' && substr($value, -1) == '%')) {
                    $operator = self::NOT_LIKE;
                    $value = substr($value, 1);
                    $value = substr($value, 0, -1);
                } elseif (substr($value, 0, 1) == '*' || substr($value, 0, 1) == '%') {
                    $operator = self::NOT_LIKE_LEFT;
                    $value = substr($value, 1);
                } elseif (substr($value, -1) == '*' || substr($value, -1) == '%') {
                    $operator = self::NOT_LIKE_RIGHT;
                    $value = substr($value, 0, -1);
                } else {
                    $operator = self::NOT_LIKE;
                }
            } else {
                // NOT EQUAL
                $operator = self::NOT_EQUAL;
            }
        } elseif (substr($this->inputFilterValue, 0, 1) == '~' || substr($this->inputFilterValue, 0, 1) == '%' || substr($this->inputFilterValue, -1) == '%' || substr($this->inputFilterValue, 0, 1) == '*' || substr($this->inputFilterValue, -1) == '*') {
            // LIKE
            if (substr($this->inputFilterValue, 0, 1) == '~') {
                $value = substr($this->inputFilterValue, 1);
            }
            $value = trim($value);
            if ((substr($value, 0, 1) == '*' && substr($value, -1) == '*') || (substr($value, 0, 1) == '%' && substr($value, -1) == '%')) {
                $operator = self::LIKE;
                $value = substr($value, 1);
                $value = substr($value, 0, -1);
            } elseif (substr($value, 0, 1) == '*' || substr($value, 0, 1) == '%') {
                $operator = self::LIKE_LEFT;
                $value = substr($value, 1);
            } elseif (substr($value, -1) == '*' || substr($value, -1) == '%') {
                $operator = self::LIKE_RIGHT;
                $value = substr($value, 0, -1);
            } else {
                $operator = self::LIKE;
            }

        }

    }




}
