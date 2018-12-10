<?php

namespace ZfMetal\Restful\Factory\Filter;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 *
 * @author
 * @license
 * @link
 */
class FilterManagerFactory implements FactoryInterface
{

    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $container->get("doctrine.entitymanager.orm_default");

        return new \ZfMetal\Restful\Filter\FilterManager($em);
    }


}

