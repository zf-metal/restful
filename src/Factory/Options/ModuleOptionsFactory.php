<?php

namespace ZfMetal\Restful\Factory\Options;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * ModuleOptionsFactory
 *
 *
 *
 * @author
 * @license
 * @link
 */
class ModuleOptionsFactory implements FactoryInterface
{

    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
         return new \ZfMetal\Restful\Options\ModuleOptions(isset($config['zf-metal-restful.options']) ? $config['zf-metal-restful.options'] : array());
    }


}

