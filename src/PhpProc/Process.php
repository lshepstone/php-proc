<?php

namespace PhpProc;

use \PhpProc\Exception\RuntimeException;

/**
 * Process
 *
 * Simple wrapper for the proc_* methods, making it easy to execute a command.
 */
class Process
{
    /**
     * The command to be executed.
     *
     * @var
     */
    protected $command;

    /**
     * The working directory context for the command.
     *
     * @var
     */
    protected $workingDirectory;

    /**
     * Array of environment variables to me made available to the command.
     *
     * @var array
     */
    protected $environmentVars;

    /**
     * Resource handle for the process.
     *
     * @var resource
     */
    protected $handle;

    /**
     * Resource handle stdin.
     *
     * @var resource
     */
    protected $stdInHandle;

    /**
     * Resource handle stdout.
     *
     * @var resource
     */
    protected $stdOutHandle;

    /**
     * Resource handle for stderr.
     *
     * @var resource
     */
    protected $stdErrHandle;

    /**
     * Determines if the process is currently open.
     *
     * @var boolean
     */
    protected $isOpen;

    /**
     * Implicitly closes any open pipes.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Sets the command to be executed.
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Sets the command to be executed.
     *
     * @param string $command Command to be executed
     *
     * @return Process
     */
    public function setCommand($command)
    {
        $this->command = (string) $command;

        return $this;
    }

    /**
     * Gets the path of the working directory for the command.
     *
     * @return string
     */
    public function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }

    /**
     * Sets the path of the working directory for the command.
     *
     * @param string $path Directory path
     *
     * @return Process
     */
    public function setWorkingDirectory($path)
    {
        $this->workingDirectory = (string) $path;

        return $this;
    }

    /**
     * Gets the array of environment variables to be made available to the command.
     *
     * return array
     */
    public function getEnvironmentVars()
    {
        return $this->environmentVars;
    }

    /**
     * Sets the array of environment variables to be made available to the command.
     *
     * @param array $vars
     *
     * @return Process
     */
    public function setEnvironmentVars(array $vars)
    {
        $this->environmentVars = $vars;

        return $this;
    }

    /**
     * Executes the command and returns the result.
     *
     * @return Result
     */
    public function execute()
    {
        $this->open();
        $stdOutContents = stream_get_contents($this->stdOutHandle);
        $stdErrContents = stream_get_contents($this->stdErrHandle);
        $status = $this->close();

        return new Result($status, $stdOutContents, $stdErrContents);
    }

    /**
     * Opens a new process using proc_open.
     *
     * @throws Exception\RuntimeException
     *
     * @return Process
     */
    private function open()
    {
        if ($this->isOpen) {
            throw new RuntimeException('Process is already open');
        }

        if (false == $this->command) {
            throw new RuntimeException('A command must be specified');
        }

        if (null !== $this->workingDirectory) {
            $workingDirectory = realpath($this->workingDirectory);
            if (false === $workingDirectory) {
                throw new RuntimeException("Invalid working directory path '{$this->workingDirectory}'");
            }
        }

        $handle = proc_open(
            $this->command,
            array(
                0 => array('pipe', 'r'),
                1 => array('pipe', 'w'),
                2 => array('pipe', 'w')
            ),
            $pipes,
            $this->workingDirectory,
            $this->environmentVars
        );

        if (false === $handle) {
            throw new RuntimeException('Failed to open the process using proc_open');
        }

        $this->handle = $handle;
        $this->stdInHandle = $pipes[0];
        $this->stdOutHandle = $pipes[1];
        $this->stdErrHandle = $pipes[2];

        $this->isOpen = true;

        return $this;
    }

    /**
     * Closes the process and all open pipes.
     *
     * @return int|null Exit status code of the command that was executed
     */
    private function close()
    {
        if (is_resource($this->stdInHandle)) {
            fclose($this->stdInHandle);
        }

        if (is_resource($this->stdOutHandle)) {
            fclose($this->stdOutHandle);
        }

        if (is_resource($this->stdErrHandle)) {
            fclose($this->stdErrHandle);
        }

        $status = null;
        if (is_resource($this->handle)) {
            $status = proc_close($this->handle);
        }

        $this->isOpen = false;

        return $status;
    }
}
