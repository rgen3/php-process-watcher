<?php

namespace rgen3\watcher\commands;

/**
 * Class AbstarctProcess
 * @package console\components\watcher\commands
 */
abstract class AbstractProcess implements Process
{
    /**
     * Resource returned by proc_open
     *
     * @var resource
     */
    private $resource;

    /**
     * Current process status
     *
     * @var array
     */
    private $status;

    /**
     * Will be set to an indexed array of file pointers
     * that correspond to PHP's end of any pipes that are created.
     *
     * @var array
     */
    private $pipes;

    /**
     * Common implementation
     * You can override this method in child class
     *
     * For more information see:
     * @see Process::getDescriptor()
     */
    public function getDescriptor(): array
    {
        return [
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "a")
        ];
    }

    /**
     * @see Process::beforeStart()
     */
    public function beforeStart()
    {}

    /**
     * @see Process::afterStart()
     */
    public function afterStart()
    {}

    /**
     * @see Process::beforeStop()
     */
    public function beforeStop()
    {}

    /**
     * @see Process::afterStop()
     */
    public function afterStop()
    {}

    /**
     * Get process info
     *
     * For more information
     * @see http://php.net/manual/en/function.proc-get-status.php
     *
     * @return array
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * Get process info
     *
     * For more information
     * @see http://php.net/manual/en/function.proc-get-status.php
     *
     * @return array
     */
    public function setStatus()
    {
        $this->status = proc_get_status($this->resource);
        return $this;
    }

    /**
     * Updates the process status
     */
    public function updateStatus()
    {
        $this->setStatus();
    }

    /**
     * The command string that was passed to proc_open().
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->status['command'];
    }

    /**
     * Process id
     *
     * @return int
     */
    public function getPid()
    {
        return $this->status['pid'];
    }

    /**
     * TRUE if the process is still running, FALSE if it has terminated.
     *
     * @return bool
     */
    public function isRunning()
    {
        return $this->status['running'];
    }

    /**
     * TRUE if the child process has been stopped by a signal. Always set to FALSE on Windows.
     *
     * @return bool
     */
    public function isStopped()
    {
        return $this->status['stopped'];
    }

    /**
     * The exit code returned by the process (which is only meaningful if running is FALSE).
     * Only first call of this function return real value, next calls return -1.
     *
     * @return integer
     */
    public function getExitCode()
    {
        return $this->status['exitcode'];
    }

    /**
     * The number of the signal
     * that caused the child process to terminate its execution
     * (only meaningful if signaled is TRUE).
     *
     * @return integer
     */
    public function getTermSig()
    {
        return $this->status['termsig'];
    }

    /**
     * The number of the signal that caused the child process to stop its execution
     * (only meaningful if stopped is TRUE).
     *
     * @return integer
     */
    public function getStopSig()
    {
        return $this->status['stopsig'];
    }

    /**
     * Common implementation of start process interface method
     *
     * You can override this method in process realization,
     * if you want to proceed custom actions on process start
     *
     * @return $this
     */
    public function startProcess()
    {
        echo "Start process", PHP_EOL;
        $descriptor = $this->getDescriptor();
        $this->resource = proc_open($this->getCmd(), $descriptor, $this->pipes);
        $this->setStatus();
        posix_setpgid($this->getPid(), $this->getPid());
        return $this;
    }

    /**
     * Common implementation of stop process interface method
     *
     * You can override this method in process realization,
     * if you want to proceed custom actions on process stop
     *
     * @return $this
     */
    public function closeProcess()
    {
        foreach ($this->pipes as &$pipe) {
            if(is_resource($pipe)) {
                fclose($pipe);
            }
        }
        posix_kill(-$this->getPid(), 9);
        proc_close($this->resource);
        return $this;
    }

    public function terminateProcess($sig = 15) 
    {
        foreach ($this->pipes as &$pipe) {
            if(is_resource($pipe)) {
                fclose($pipe);
            }
        }
        proc_terminate($this->resource, $sig);
    }

    /**
     * Pretty debug info
     *
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'command' => $this->getCommand(),
            'descriptor' => $this->getDescriptor(),
            'statusInfo' => $this->status,
        ];
    }
}