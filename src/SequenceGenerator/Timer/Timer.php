<?php

namespace SequenceGenerator\Timer;

use SequenceGenerator\Timer\TimerInterface;

class Timer implements TimerInterface
{
    const EPOCH_UNIX = 0;

    protected $epoch;

    /**
     * @param type $epoch
     */
    public function __construct($epoch = self::EPOCH_UNIX)
    {
        $this->epoch = $epoch;
    }

    public function get()
    {
        if (date_default_timezone_get() != 'UTC') {
            date_default_timezone_set('UTC');
        }

        return (int) floor(microtime(true)*1000) - $this->epoch;
    }
}
