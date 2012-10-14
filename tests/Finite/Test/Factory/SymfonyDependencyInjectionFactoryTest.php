<?php

namespace Finite\Test\Factory;

use Finite\Factory\SymfonyDependencyInjectionFactory;
use Finite\StateMachine;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SymfonyDependencyInjectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PimpleFactory
     */
    protected $object;

    protected function setUp()
    {
        $container = new ContainerBuilder;
        $container
            ->register('state_machine', 'Finite\StateMachine')
            ->setScope('prototype')
            ->addMethodCall('addTransition', array('t12', 's1', 's2'))
            ->addMethodCall('addTransition', array('t23', 's2', 's3'));

        $this->object = new SymfonyDependencyInjectionFactory($container, 'state_machine');
    }

    public function testGet()
    {
        $object = $this->getMock('Finite\StatefulInterface');
        $object->expects($this->once())->method('getFiniteState')->will($this->returnValue('s2'));
        $sm = $this->object->get($object);

        $this->assertInstanceOf('Finite\StateMachine', $sm);
        $this->assertSame('s2', $sm->getCurrentState()->getName());

        $object2 = $this->getMock('Finite\StatefulInterface');
        $object2->expects($this->once())->method('getFiniteState')->will($this->returnValue('s2'));
        $sm2 = $this->object->get($object2);

        $this->assertNotSame($sm, $sm2);
    }

    /**
     * @expectedException Finite\Exception\FactoryException
     */
    public function testNoService()
    {
        new SymfonyDependencyInjectionFactory(new ContainerBuilder, 'state_machine');
    }
}
