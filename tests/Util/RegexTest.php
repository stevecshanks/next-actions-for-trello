<?php

namespace App\Tests\Util;

use App\Util\Regex;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    public function testFromStringThrowsExceptionForInvalidPattern()
    {
        $this->expectException(InvalidArgumentException::class);

        Regex::fromString('invalid pattern string');
    }

    public function testMatchReturnsEmptyArrayOnNoMatch()
    {
        $regex = Regex::fromString('/foo/');

        $this->assertSame([], $regex->match('bar'));
    }

    public function testMatchReturnsCorrectResultWhenMatchFound()
    {
        $regex = Regex::fromString('/[0-9]+/');

        $this->assertSame(['1234'], $regex->match('ab1234cdef'));
    }
}
