<?php

namespace ZfMetal\Restful\Factory\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 *
 * @author
 * @license
 * @link
 */
class ResourceFactory implements FactoryInterface
{

    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $container->get("doctrine.entitymanager.orm_default");

        $moduleOptions = $container->get('ZfMetalRestful.options');
        return new \ZfMetal\Restful\Service\Resource($em,$moduleOptions);
    }


}

