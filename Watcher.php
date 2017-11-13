<?php

namespace rgen3\watcher;

use rgen3\watcher\commands\Process;

/**
 * Class Watcher
 * @package console\components\watcher
 */
class Watcher
{
    /**
     * Second for process status update
     */
    private $processes = [];

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
            $process->beforeStart();
            $process->startProcess();
            $process->afterStart();
            $process->setStatus();
        }
    }

    /**
     * Monitors processlist, if a process is down it will be restarted
     */
    public function monitor()
    {
        foreach ($this->processes as $process) {
            $process->updateStatus();
            if (!$process->isRunning())
            {
                $this->restartProcess($process);
            }
        }
    }

    /**
     * Restarting the needle process
     * @param Process $process
     */
    private function restartProcess(Process $process)
    {
        echo "Restarting process ", $process->getCommand(), PHP_EOL;
        $process->beforeStop();
        $process->closeProcess();
        $process->afterStop();
        $process->beforeStart();
        $process->startProcess();
        $process->afterStart();
    }
}