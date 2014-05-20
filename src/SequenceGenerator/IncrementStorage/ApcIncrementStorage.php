<?php

namespace SequenceGenerator\IncrementStorage;

class ApcIncrementStorage implements IncrementStorageInterface
{
    /**
     * Time to live in second
     * Make sur $ttl is at least equal to php max_execution_time
     *
     * @var integer
     */
    protected $ttl;

    public function __construct($ttl = 60)
    {
        $this->ttl = $ttl;
    }

    /**
     * {@inheritDoc}
     */
    public function get($timestamp)
    {
        $key = sprintf('apc_inc_%d', $timestamp);

        if (apc_add($key, 0, $this->ttl)) {
            return 0;
        }

        return apc_inc($key);
    }
}
