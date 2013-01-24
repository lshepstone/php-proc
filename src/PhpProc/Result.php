<?php

namespace PhpProc;

/**
 * Result
 *
 * Represents the result of execute() on a Process instance.
 */
class Result
{
    /**
     * Exit status code.
     *
     * @var
     */
    private $status;

    /**
     * Contents of the stdout stream.
     *
     * @var
     */
    private $stdOutContents;

    /**
     * Contents of the stderr stream.
     *
     * @var
     */
    private $stdErrContents;

    /**
     * Constructs a new Result object.
     *
     * @param integer $status Result status code
     * @param string $stdOutContents Contents of stdout
     * @param string $stdErrContents Contents of stderr
     * @param string null $errorFilePath Error file path
     */
    public function __construct($status, $stdOutContents, $stdErrContents)
    {
        $this->status = (integer) $status;

        $this->stdOutContents = (string) $stdOutContents;
        $this->stdErrContents = (string) $stdErrContents;
    }

    /**
     * The result status (process exit status code).
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * The contents of the stdout stream.
     *
     * @return string
     */
    public function getStdOutContents()
    {
        return $this->stdOutContents;
    }

    /**
     * The contents of the stderr stream.
     *
     * @return string
     */
    public function getStdErrContents()
    {
        return $this->stdErrContents;
    }

    /**
     * Determines if the result describes errors or exited with a non-zero status code, or not.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return ($this->stdErrContents || 0 !== $this->status) ? true : false;
    }
}
