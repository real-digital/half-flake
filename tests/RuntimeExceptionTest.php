<?php
declare(strict_types=1);

namespace Real\HalfFlake\Tests;

use PHPUnit\Framework\TestCase;
use Real\HalfFlake\RuntimeException;
use Real\HalfFlake\Throwable;

class RuntimeExceptionTest extends TestCase
{
    public function testExceptionInheritance(): void
    {
        $exception = new RuntimeException('message');
        self::assertInstanceOf(RuntimeException::class, $exception);
        self::assertInstanceOf(Throwable::class, $exception);
    }

    public function testExceptionCodes(): void
    {
        self::assertSame(1000, RuntimeException::CODE_DATACENTER);
        self::assertSame(1001, RuntimeException::CODE_NODE);
        self::assertSame(1002, RuntimeException::CODE_SEQUENCE_OVERFLOW);
        self::assertSame(1003, RuntimeException::CODE_TIMESTAMP_OVERFLOW);
        self::assertSame(1004, RuntimeException::CODE_CLOCK_BACKWARD);
    }
}
