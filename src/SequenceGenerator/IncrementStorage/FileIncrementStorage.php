<?php

namespace SequenceGenerator\IncrementStorage;

use SequenceGenerator\Exception\BadDirectoryException;

class FileIncrementStorage implements IncrementStorageInterface
{
    /**
     * Time to live in second
     * Make sur $ttl is at least equal to php max_execution_time
     *
     * @var integer
     */
    protected $ttl;

    protected $pattern;
    protected $autoGarbageCollector;

    /**
     * @param string  $pattern
     * @param boolean $autoGarbageCollector
     */
    public function __construct($pattern = null, $ttl = 60, $autoGarbageCollector = false)
    {
        $this->pattern = $pattern === null
            ? '/dev/shm/file_sequence_%s.cache'
            : $pattern;
        $this->autoGarbageCollector = $autoGarbageCollector;
        $this->ttl = $ttl;

        $dirname = dirname($this->pattern);
        if (!is_dir($dirname) || !is_writable($dirname)) {
            throw new BadDirectoryException(sprintf(
                "Directory '%s' doesn't exists or is not writable",
                $dirname
            ));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get($timestamp)
    {
        $next = false;

        $second = intval($timestamp/1000);
        $file = sprintf($this->pattern, $second);
        $fp = fopen($file, "c+");
        $nbDigits = 4;
        if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock
            $pos = ($nbDigits+1)*($timestamp-($second*1000));

            fseek($fp, $pos);
            $val = fgets($fp, $nbDigits+1);

            $next = $val === false ? 0 : intval(trim($val))+1;
            fseek($fp, $pos);
            fputs($fp, sprintf("%0".$nbDigits."d", $next), $nbDigits);

            fflush($fp);            // flush output before releasing the lock
            flock($fp, LOCK_UN);    // release the lock
        }
        fclose($fp);

        // call garbageCollector maximum 1 time per ms
        if ($next === 0 && $this->autoGarbageCollector) {
            $this->garbageCollector();
        }

        return $next;
    }

    /**
     * The garbageCollector method uses the defined pattern to locate cache
     * files and clean all or only files with last modification > $ttl seconds
     *
     * @param boolean $all
     */
    public function garbageCollector($all = false)
    {
        $a = explode("%s", $this->pattern);
        foreach (glob(sprintf($this->pattern, '*')) as $filename) {
            $timestamp = intval(str_replace($a, "", $filename));
            if (true === $all || $timestamp < (time() - $this->ttl)) {
               @unlink($filename);
            }
        }
    }
}
