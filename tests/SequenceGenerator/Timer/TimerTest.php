<?php

namespace SequenceGenerator\Timer;

class TimerTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $this->withEpoch(1325376000000);
        $this->withEpoch(0);
        $this->withEpoch(-1325376000000);
        $this->withEpoch(2325376000000);
    }

    protected function withEpoch($epoch)
    {
        $before = (int) floor(microtime(true)*1000);
        $timestamp = (new Timer($epoch))->get();
        $after = (int) floor(microtime(true)*1000);

        $epochMsg = sprintf("Assertion with epoch: %d", $epoch);
        $this->assertInternalType("int", $timestamp, $epochMsg);
        $this->assertGreaterThanOrEqual($before-$epoch, $timestamp, $epochMsg);
        $this->assertLessThanOrEqual($after-$epoch, $timestamp, $epochMsg);
    }
}
