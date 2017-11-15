<?php

namespace rgen3\watcher;

use rgen3\watcher\commands\Process;

/**
 * Class Watcher
 * @package console\components\watcher
 */
class Watcher
{
    private $restartMode = true;

    /**
     * Second for process status update
     */
    private $processes = [];

    private $closedProcesses = [];

    private $exit = false;

    public function shutdown($signo)
    {
        var_dump($signo);
        foreach ($this->processes as $key => $process)
        {
            $process->updateStatus();
            $process->terminateProcess();
            $process->closeProcess();
            unset($this->processes[$key]);
        }

        $this->exit = true;
    }

    public function exit() 
    {
        return $this->exit;
    }

    public function getClosedProcesses()
    {
        return $this->closedProcesses;
    }

    public function clearClosedProcesses()
    {
        $this->closedProcesses = [];
        return $this;
    }

    public function enableRestartMode()
    {
        $this->restartMode = true;
        return $this;
    }

    public function disableRestartMode()
    {
        $this->restartMode = false;
        return $this;
    }

    /**
     * Pushes process to queue
     *
     * @param Process $process
     */
    public function pushProcess(Process $process)
    {
        $this->processes[] = $process;
    }

    /**
     * Returns queue of processes
     * @return array
     */
    public function getProcesses()
    {
        return $this->processes;
    }

    /**
     * Runs watcher with specified commands
     */
    public function run()
    {
        foreach ($this->processes as $process) {
            $this->startProcess($process);
            $process->setStatus();
        }
    }

    /**
     * Monitors processlist, if a process is down it will be restarted
     */
    public function monitor()
    {
        foreach ($this->processes as $key => $process) {
            $process->updateStatus();
            if (!$process->isRunning())
            {
                $this->closedProcesses[] = $process;
                if ($this->restartMode) {
                    $this->restartProcess($process);
                } else {
                    $this->closeProcess($process);
                    unset($this->processes[$key]);
                }
            }
        }
    }

    private function closeProcess(Process $process)
    {
        $process->beforeStop();
        $process->closeProcess();
        $process->afterStop();
    }

    private function startProcess(Process $process) 
    {
        $process->beforeStart();
        $process->startProcess();
        $process->afterStart();
    }

    /**
     * Restarting the needle process
     * @param Process $process
     */
    private function restartProcess(Process $process)
    {
        echo "Restarting process ", $process->getCommand(), PHP_EOL;
        $this->closeProcess($process);
        $this->startProcess($process);
    }
}