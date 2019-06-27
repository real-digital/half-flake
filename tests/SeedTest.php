<?php
declare(strict_types=1);

namespace Real\HalfFlake\Tests;

use PHPUnit\Framework\TestCase;
use Real\HalfFlake;

class SeedTest extends TestCase
{
    /** @var HalfFlake\Seed */
    private $seed;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed = new HalfFlake\Seed(2, 3);
    }

    public function testDatacenter(): void
    {
        self::assertSame(2, $this->seed->datacenter());
    }

    public function testNode(): void
    {
        self::assertSame(3, $this->seed->node());
    }
}
