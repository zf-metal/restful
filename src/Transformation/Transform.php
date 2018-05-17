<?php
/**
 * Created by PhpStorm.
 * User: crist
 * Date: 17/5/2018
 * Time: 00:13
 */

namespace ZfMetal\Restful\Transformation;

use \Doctrine\Common\Annotations\Reader;
use \Doctrine\Common\Annotations\AnnotationReader;
use \Doctrine\Common\Annotations\CachedReader;
use \Doctrine\Common\Cache\ArrayCache;
use \Doctrine\ORM\Mapping\ManyToOne;
use \Doctrine\ORM\Mapping\ManyToMany;
use \Doctrine\ORM\Mapping\OneToOne;
use \Doctrine\ORM\Mapping\OneToMany;

class Transform
{
    /**
     * @var Policy\Interfaces\Policy
     */
    protected $policy;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var \ZfMetal\Restful\Transformation\PolicyResolver
     */
    protected $policyResolver;


    protected $entity;

    /**
     * @var \ReflectionClass
     */
    protected $refClass;

    /**
     * @var array
     */
    protected $properties;

    protected $depth = 1;

    protected $maxDepth;

    const DATE_TYPES = ["date", "time", "datetime", "datetimez"];

    /**
     * Transform constructor.
     *
     * @param $entity
     * @param Policy\Interfaces\Policy $policy
     * @param int $maxDepth
     * @param int $depth
     */
    public function __construct(Policy\Interfaces\Policy $policy = null, $maxDepth = 2, $depth = 1)
    {

        $this->policy = $policy;
        $this->reader = new CachedReader(new AnnotationReader(), new ArrayCache());
        $this->policyResolver = new PolicyResolver();


        $this->depth = $depth;
        $this->maxDepth = $maxDepth;

    }


    protected function getProperties($refClass)
    {

        return $refClass->getProperties(\ReflectionProperty::IS_PUBLIC
            | \ReflectionProperty::IS_PROTECTED
            | \ReflectionProperty::IS_PRIVATE);

    }

    /**
     * @param $entity
     * @param int $depth
     * @return mixed
     */
    public function toArray($entity, $depth = 1)
    {
        $refClass = new \ReflectionClass($entity);

        /** @var \ReflectionProperty $property */
        foreach ($this->getProperties($refClass) as $property) {

            if ($property->isStatic()) {
                continue;
            }

            $propertyName = $property->getName();

            if ($this->_check($propertyName)) {
                continue;
            }

            $propertyPolicy = $this->policyResolver->resolvePropertyPolicyTo(
                $this->policy, $propertyName, $property, $this->reader);

            if ($propertyPolicy instanceof Policy\Interfaces\Skip) {
                continue;
            }

            $getter = $propertyPolicy->getter ?: 'get' . ucfirst($propertyName);
            $value = $entity->$getter();

            $result[$propertyName] = $this->toArrayProperty($property, $propertyName, $value, $propertyPolicy, $depth);
        }
        return $result;
    }

    protected function toArrayProperty(\ReflectionProperty $property, $propertyName, $value, $policy, $depth)
    {
        $result = null;

        if ($column = $this->reader->getPropertyAnnotation($property, 'Doctrine\ORM\Mapping\Column')) {
            $result = $this->scalarTypes($propertyName, $value, $column->type, $policy);
        } else if ($association = $this->getPropertyAssociation($property)) {
            $result = $this->associationTypes($association, $value, $policy, $depth);
        } else {
            $result = $value;
            if (($policy instanceof Policy\Interfaces\Custom) && $policy->format) {
                return call_user_func_array($policy->format, [$result, null]);
            }
        }

        return $result;
    }

    protected function _check($propertyName)
    {
        if ($propertyName[0] === '_' && $propertyName[1] === '_') {
            return true;
        }
        return false;
    }


    protected function scalarTypes($propertyName, $value, $columnType, $policy)
    {

        //Value
        $result = $value;

        //Custom Policy
        if (($policy instanceof Policy\Interfaces\Custom) && $policy->format) {
            return call_user_func_array($policy->format, [$result, $columnType]);
        }


        //Date-Time
        if (in_array($columnType, self::DATE_TYPES)) {
            if ($result !== null) {
                if ($policy instanceof Policy\Interfaces\FormatDateTime) {
                    $result = $result->format($policy->format);
                    if ($result === false) {
                        throw new Exceptions\PolicyException('Wrong DateTime format for field "' . $propertyName . '"');
                    }
                } else if (!$policy instanceof Policy\Interfaces\KeepDateTime) {
                    $result = $result->format('Y-m-d\TH:i:s') . '.000Z';
                }
            }
        }

        if ($columnType == 'simple_array') {
            if ($this->policyResolver->hasOption(PolicyResolver::SIMPLE_ARRAY_FIX)
                && is_array($result)
                && (count($result) === 1)
                && ($result[0] === null)) {
                return [];
            }
        }

        return $result;
    }


    protected function associationTypes($association, $value, $policy, $depth)
    {

        $result = null;
        if ($depth < $this->maxDepth) {
            $isCollection = false;

            if ($association instanceof OneToMany || $association instanceof ManyToMany) {
                $isCollection = true;
            }
            $relEntity = $value;

            if ($isCollection) {
                $collection = $value;
                if ($collection->count()) {

                    if ($policy instanceof Policy\Interfaces\Paginate) { // pagination policy
                        $collection = $this->paginate($policy, $collection);
                    }


                    foreach ($collection as $entity) {
                        $result[] =  $this->toArray($entity, $depth + 1);
                    }

                }

            } else { // single entity
                if ($relEntity) {
                    $result =  $this->toArray($relEntity, $depth + 1);
                }
            }

            if (($policy instanceof Policy\Interfaces\Custom) && $policy->transform) {
                $result = call_user_func_array($policy->transform, [$relEntity, $result]);
            }
        }
        return $result;

    }


    /** @return Annotation|null returns null if its inversed side of bidirectional relation */
    protected
    function getPropertyAssociation(\ReflectionProperty $property)
    {
        $annotations = $this->reader->getPropertyAnnotations($property);

        foreach ($annotations as $an) {
            if (($an instanceof ManyToOne && !$an->inversedBy)
                || ($an instanceof ManyToMany && !$an->inversedBy)
                || ($an instanceof OneToOne && !$an->inversedBy)
                || $an instanceof OneToMany) {
                return $an;
            }
        }
        return null;
    }


    public function toArrays(array $entities)
    {
        $arrays = [];

        foreach ($entities as $entity) {
            $arrays[] = $this->toArray($entity);
        }
        return $arrays;
    }

    /**
     * @param $policy
     * @param $collection
     * @return mixed
     */
    protected function paginate($policy, $collection)
    {
        if ($policy->fromTail) {
            $offset = $collection->count() - $policy->limit - $policy->offset;
            if ($offset < 0) {
                $offset = 0;
            }
            $limit = ($collection->count() > $policy->limit) ? $collection->count() : $policy->limit;
            $collection = $collection->slice($offset, $limit);
        } else {
            $collection = $collection->slice($policy->offset, $policy->limit);
        }
        return $collection;
    }

}