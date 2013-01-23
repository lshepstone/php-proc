<?php

namespace PhpProc\Test\Unit;

use PhpProc\Process;

class ProcessTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAndSetCommand()
    {
        $command = 'php';

        $process = new Process();

        $this->assertNull($process->getCommand());
        $this->assertSame($process, $process->setCommand($command));
        $this->assertSame($command, $process->getCommand());
    }

    public function testGetAndSetWorkingDirectory()
    {
        $workingDirectory = '/home/joey';

        $process = new Process();

        $this->assertNull($process->getWorkingDirectory());
        $this->assertSame($process, $process->setWorkingDirectory($workingDirectory));
        $this->assertSame($workingDirectory, $process->getWorkingDirectory());
    }

    public function testGetAndSetEnvironmentVariables()
    {
        $vars = array(
            'USER' => 'Joey Tribbiani'
        );

        $process = new Process();

        $this->assertNull($process->getEnvironmentVars());
        $this->assertSame($process, $process->setEnvironmentVars($vars));
        $this->assertSame($vars, $process->getEnvironmentVars());
    }

    public function testExecuteReturnsExpectedResult()
    {
        $process = new Process();

        $message = 'Joey likes pizza.';

        $process->setCommand("hp -r \"echo '{$message}';\"");
        $result = $process->execute();

        $this->assertSame(0, $result->getStatus());
        $this->assertSame($message, $result->getStdOutContents());
    }

    public function testExecuteWithValidWorkingDirectorySetReturnsExpectedResult()
    {
        $process = new Process();

        $workingDirectory = __DIR__;

        $process->setCommand("php -r \"echo getcwd();\"");
        $process->setWorkingDirectory($workingDirectory);
        $result = $process->execute();

        $this->assertSame(0, $result->getStatus());
        $this->assertSame($workingDirectory, $result->getStdOutContents());
    }

    public function testExecuteWithInvalidWorkingDirectorySetThrowsException()
    {
        $process = new Process();

        $workingDirectory = '/fake/' . __DIR__;

        $process->setCommand("php -r \"echo getcwd();\"");
        $process->setWorkingDirectory($workingDirectory);

        $this->setExpectedException('\PhpProc\Exception\RuntimeException');

        $process->execute();
    }

    public function testExecuteWithEnvironmentVarsSetReturnsExpectedResult()
    {
        $process = new Process();

        $user = 'Joey Tribbiani';
        $vars = array(
            'USER' => 'Joey Tribbiani'
        );

        $process->setCommand("php -r \"echo getenv('USER');\"");
        $process->setEnvironmentVars($vars);
        $result = $process->execute();

        $this->assertSame(0, $result->getStatus());
        $this->assertSame($user, $result->getStdOutContents());
    }

    public function testExecuteReturnsExpectedExitCode()
    {
        $process = new Process();

        $code = 100;

        $process->setCommand("php -r \"exit({$code});\"");
        $result = $process->execute();

        $this->assertSame($code, $result->getStatus());
    }

    public function testExecuteWithErrorsReturnsExpectedResult()
    {
        $process = new Process();

        $process->setCommand("php -r \"trigger_error('error', E_USER_ERROR);\"");
        $result = $process->execute();

        $this->assertSame(255, $result->getStatus());
        $this->assertTrue($result->hasErrors());
        $this->assertContains("PHP Fatal error", $result->getStdErrContents());
    }
}
