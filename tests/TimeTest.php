<?php
declare(strict_types=1);

namespace Real\HalfFlake\Tests;

use PHPUnit\Framework\TestCase;
use Real\HalfFlake;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TimeTest extends TestCase
{
    private const NOW = 330;

    /**
     * We just overloading built-in function in a origin namespace to mock the clock
     *
     * @see setUP()
     * @link http://php.net/manual/en/language.namespaces.fallback.php
     * @link https://github.com/symfony/phpunit-bridge/blob/master/ClockMock.php
     */
    private const CLOCK_MOCK = <<< EO_CLOCK_MOCK
        namespace Real\HalfFlake;
        
        function microtime(\$asFloat = false)
        {
            return %d;
        }
EO_CLOCK_MOCK;

    public function setUp(): void
    {
        parent::setUp();

        eval(sprintf(self::CLOCK_MOCK, self::NOW));
    }

    public function testNow(): void
    {
        $time = new HalfFlake\Time();

        self::assertSame(self::NOW, $time->now()->getTimestamp());
    }

    public function testEpoch(): void
    {
        $epochOffset = strtotime(HalfFlake\Time::EPOCH_BEGINNING) * 1000;

        $time = new HalfFlake\Time();

        self::assertSame(self::NOW * 1000, $epochOffset + $time->epoch());
    }
}
