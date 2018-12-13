<?php

namespace ZfMetalTest\Restful\DataFixture;

/**
 * Created by PhpStorm.
 * User: crist
 * Date: 1/6/2018
 * Time: 12:21
 */
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\ORM\EntityManager;

use ZfMetalTest\Restful\Entity\Foo;

class FooLoader extends AbstractFixture implements FixtureInterface
{

    const ENTITY = Foo::class;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ArrayCollection
     */
    protected $users;

    /**
     * @return mixed
     */
    public function getEm()
    {
        return $this->em;
    }


    protected function findByTitle($title)
    {
        return $this->getEm()->getRepository($this::ENTITY)->findOneByTitle($title);
    }



    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $this->em = $manager;

        $this->createFoo(1, "Test Title DataFixture");
           $manager->flush();


    }


    public function createFoo($id, $title)
    {

        $foo = $this->findByTitle($title);
        if (!$foo) {
            $foo = new Foo();
            $foo->setId($id);
            $foo->setTitle($title);
        }


        $this->getEm()->persist($foo);

        //Add reference for relations
        $this->addReference($title, $foo);

    }

}