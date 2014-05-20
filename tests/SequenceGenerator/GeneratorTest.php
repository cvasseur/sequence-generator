<?php

namespace SequenceGenerator;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $prophet;

    protected function setUp()
    {
        $this->prophet = new \Prophecy\Prophet;
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();
    }

    protected function getTimer($willReturn = 1337000000)
    {
        $timer = $this->prophet->prophesize('SequenceGenerator\Timer\TimerInterface');
        $timer->get()->willReturn($willReturn);

        return $timer;
    }

    protected function getIncrement($willReturn = 0)
    {
        $incrementStorage  = $this->prophet->prophesize('SequenceGenerator\IncrementStorage\IncrementStorageInterface');
        $incrementStorage->get(\Prophecy\Argument::type('int'))->willReturn($willReturn);

        return $incrementStorage;
    }

    protected function getSequenceGenerator($timer, $machineId, $increment)
    {
        return new SequenceGenerator($machineId, $increment->reveal(), $timer->reveal());
    }

    public function testGet()
    {
        $incrementStorage = $this->getIncrement(0);

        $timer1 = $this->getTimer(1337000000);
        $generator1 = $this->getSequenceGenerator($timer1, 1, $incrementStorage);
        $this->assertEquals(5607784448004096, $generator1->get());

        $timer2 = $this->getTimer(1337000001);
        $generator2 = $this->getSequenceGenerator($timer2, 1, $incrementStorage);
        $this->assertEquals(5607784452198400, $generator2->get());
    }

    public function testMachineId()
    {
        $increment = $this->getIncrement();
        $timer = $this->getTimer();

        $generator1 = $this->getSequenceGenerator($timer, 0, $increment);
        $id1 = $generator1->get();

        $generator2 = $this->getSequenceGenerator($timer, 1, $increment);
        $id2 = $generator2->get();

        $this->assertEquals(4096, $id2-$id1);
    }

    public function testMachineIdTooLow()
    {
        $increment = $this->getIncrement();
        $timer = $this->getTimer();

        $this->setExpectedException('Exception');
        $this->getSequenceGenerator($timer, -1, $increment);
    }

    public function testMachineIdTooHigh()
    {
        $increment = $this->getIncrement();
        $timer = $this->getTimer();

        $this->setExpectedException('Exception');
        $this->getSequenceGenerator($timer, 1024, $increment);
    }
}
