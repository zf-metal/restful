<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

/**
 * List of enabled modules for this application.
 *
 * This should be an array of module namespaces used in the application.
 */
return [
    'Zend\Mvc\Plugin\Identity',
    'Zend\Mail',
    'Zend\Cache',
    'Zend\Paginator',
    'Zend\InputFilter',

    'Zend\Mvc\Plugin\FlashMessenger',
    'Zend\Session',
    'Zend\Router',
    'Zend\Mvc\Console',

    'Zend\Filter',
    'Zend\Form',
    'Zend\Validator',
    //DOCTRINE
    'DoctrineModule',
    'DoctrineORMModule',

    'ZfMetal\Commons',
    'ZfMetal\Restful',
    'ZfMetal\Log',
    'ZfMetalTest\Restful',
];
