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
     * stdin identifier (used for pipe index).
     */
    const STD_IN = 0;

    /**
     * stdout identifier (used for pipe index).
     */
    const STD_OUT = 1;

    /**
     * stderr identifier (used for pipe index).
     */
    const STD_ERR = 2;

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
     * Handles to stdin, stdout and stderr streams.
     *
     * @var array
     */
    protected $pipes;

    /**
     * Determines if the process is currently open.
     *
     * @var boolean
     */
    protected $isOpen;

    /**
     * Constructs the object, optionally setting the command to be executed.
     *
     * @param string|null $command
     */
    public function __construct($command = null)
    {
        null !== $command && $this->setCommand($command);
    }

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
     * This will replace ALL environment variables for the command, which can include the PATH
     * variable and may cause the command to not even be found, or other undesired effects.
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
        $stdOutContents = stream_get_contents($this->pipes[self::STD_OUT]);
        $stdErrContents = stream_get_contents($this->pipes[self::STD_ERR]);
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
                self::STD_IN => array('pipe', 'r'),
                self::STD_OUT => array('pipe', 'w'),
                self::STD_ERR => array('pipe', 'w')
            ),
            $this->pipes,
            $this->workingDirectory,
            $this->environmentVars
        );

        if (false === $handle) {
            throw new RuntimeException('Failed to open the process using proc_open');
        }

        $this->handle = $handle;
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
        is_resource($this->pipes[self::STD_IN]) && fclose($this->pipes[self::STD_IN]);
        is_resource($this->pipes[self::STD_OUT]) && fclose($this->pipes[self::STD_OUT]);
        is_resource($this->pipes[self::STD_ERR]) && fclose($this->pipes[self::STD_ERR]);

        $status = null;
        if (is_resource($this->handle)) {
            $status = proc_close($this->handle);
        }

        $this->isOpen = false;

        return $status;
    }
}
