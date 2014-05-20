<?php

namespace SequenceGenerator\IncrementStorage;

use SequenceGenerator\IncrementStorage\FileIncrementStorage;

class FileIncrementStorageTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $pattern = '/dev/shm/test_file_sequence_%s.cache';
        $incrementStorage = new FileIncrementStorage($pattern);

        $incrementStorage->garbageCollector(true);
        $this->assertEquals(0, $incrementStorage->get(12000));
        $this->assertEquals(0, $incrementStorage->get(54321));
        $this->assertEquals(1, $incrementStorage->get(12000));
        $this->assertEquals(2, $incrementStorage->get(12000));
        $this->assertEquals(0, $incrementStorage->get(12001));
        $this->assertEquals(0, $incrementStorage->get(12002));
        $this->assertEquals(1, $incrementStorage->get(12002));
        $this->assertEquals(2, $incrementStorage->get(12002));
        $this->assertEquals(3, $incrementStorage->get(12002));

        $incrementStorage->garbageCollector(true);
        $this->assertEquals(0, $incrementStorage->get(12000));

        $incrementStorage->garbageCollector(true);
    }

    /**
     * @expectedException SequenceGenerator\Exception\BadDirectoryException
     */
    public function testDir()
    {
        $pattern = '/wrong_dir/file_%s.cache';
        new FileIncrementStorage($pattern);
    }
}
