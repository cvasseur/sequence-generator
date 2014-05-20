<?php

namespace SequenceGenerator;

use SequenceGenerator\IncrementStorage\IncrementStorageInterface;
use SequenceGenerator\Timer\TimerInterface;
use SequenceGenerator\Exception\OutOfRangeException;
use SequenceGenerator\Exception\IntSizeException;

class SequenceGenerator
{
    protected $incrementStorage;
    protected $machineId;
    protected $timer;

    /**
     * @param integer                   $machineId
     * @param IncrementStorageInterface $incrementStorage
     * @param TimerInterface            $timer
     */
    public function __construct($machineId, IncrementStorageInterface $incrementStorage, TimerInterface $timer)
    {
        if (PHP_INT_SIZE !== 8) {
            throw new IntSizeException("Php int size must be 8");
        }

        if ($machineId < 0 || $machineId > 1023) {
            throw new OutOfRangeException("Machine id must be >= 0 and <= 1023");
        }

        $this->machineId = $machineId;
        $this->incrementStorage = $incrementStorage;
        $this->timer = $timer;
    }

    public function get()
    {
        $timestamp = $this->timer->get();
        $increment = $this->incrementStorage->get($timestamp);

        $id = $timestamp << 22 | $this->machineId << 12 | $increment;

        return $id;
    }
}
