<?php

namespace PhpProc\Test\Unit;

use PhpProc\Result;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function createResult($status = null, $stdOutContents = null, $stdErrContents = null)
    {
        return new Result(
            $status ?: 0,
            $stdOutContents === null ? 'stdout contents' : $stdOutContents,
            $stdErrContents === null ? 'stderr contents' : $stdErrContents
        );
    }

    public function testConstructWithAndGetStatus()
    {
        $status = 1024;

        $result = $this->createResult($status);

        $this->assertSame($status, $result->getStatus());
    }

    public function testConstructWithAndGetStdOutContents()
    {
        $stdOutContents = 'standard out contents';

        $result = $this->createResult(null, $stdOutContents);

        $this->assertSame($stdOutContents, $result->getStdOutContents());
    }

    public function testConstructWithAndGetStdErrContents()
    {
        $stdErrContents = 'standard err contents';

        $result = $this->createResult(null, null, $stdErrContents);

        $this->assertSame($stdErrContents, $result->getStdErrContents());
    }

    public function testHasErrorsIsTrueWithStdErrContentsSet()
    {
        $stdErrContents = 'standard err contents';

        $result = $this->createResult(null, null, $stdErrContents);

        $this->assertTrue($result->hasErrors());
    }

    public function testHasErrorsIsFalseWithStdErrContentsEmpty()
    {
        $result = $this->createResult(null, null, '');

        $this->assertFalse($result->hasErrors());
    }
}
