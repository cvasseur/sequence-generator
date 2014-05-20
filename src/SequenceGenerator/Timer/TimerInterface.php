<?php

namespace SequenceGenerator\Timer;

interface TimerInterface
{
    /**
     * return the current timestamp in ms
     */
    public function get();
}
