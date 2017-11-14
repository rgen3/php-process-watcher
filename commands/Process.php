<?php

namespace rgen3\watcher\commands;

/**
 * Interface Process
 * @package console\components\watcher\commands
 */
interface Process {

    /**
     * An indexed array where the key represents the descriptor number
     * and the value represents how PHP will pass that descriptor to the child process.
     * 0 is stdin,
     * 1 is stdout,
     * 2 is stderr.
     *
     * Each element can be:
     * An array describing the pipe to pass to the process.
     * The first element is the descriptor type and the second element is an option for the given type.
     * Valid types are pipe (the second element is either
     * r to pass the read end of the pipe to the process,
     * or w to pass the write end)
     * and file (the second element is a filename).
     * A stream resource representing a real file descriptor (e.g. opened file, a socket, STDIN).
     *
     * The file descriptor numbers are not limited to 0, 1 and 2 -
     * you may specify any valid file descriptor number and it will be passed to the child process.
     * This allows your script to interoperate with other scripts that run as "co-processes".
     * In particular, this is useful for passing passphrases to programs like PGP,
     * GPG and openssl in a more secure manner.
     * It is also useful for reading status information provided
     * by those programs on auxiliary file descriptors.
     *
     * for more info @see http://php.net/manual/en/function.proc-open.php
     *
     * @return array
     */
    public function getDescriptor() : array;

    /**
     * Specifies the command that have to be executed,
     * note that you should use full path to the programm and file to be executed.
     *
     * For example:
     *
     * php yii migrate/up
     *
     * have to be used as
     *
     * /path/to/php/interpreter/php /path/to/yii/file command_to_be_executed
     *
     * @return string
     */
    public function getCmd() : string;

    public function setArguments(array $arguments);

    /**
     * Actions on process start
     * @return mixed
     */
    public function startProcess();

    /**
     * Actions on process stop
     */
    public function closeProcess();

    /**
     * Actions on before process start
     */
    public function beforeStart();

    /**
     * Actions on after process start
     */
    public function afterStart();

    /**
     * Actions on before process stop
     */
    public function beforeStop();

    /**
     * Actions on after process stop
     */
    public function afterStop();

    /**
     * Get process info
     *
     * For more information
     * @see http://php.net/manual/en/function.proc-get-status.php
     *
     * @return array
     */
    public function getStatus() : array;

}