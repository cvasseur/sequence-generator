<?php

namespace SequenceGenerator\IncrementStorage;

interface IncrementStorageInterface
{
    /**
     * get the next increment value for one timestamp
     * timestamp is number for ms elapsed since epoch
     *
     * @param  int $timestamp
     * @return int
     */
    public function get($timestamp);
}
