<?php
declare(strict_types=1);

namespace Real\HalfFlake\Tests;

use PHPUnit\Framework\TestCase;
use Real\HalfFlake;

class GeneratorTest extends TestCase
{
    public function testGeneratorInterfaceIsInherited(): void
    {
        $shard = $this->createConfiguredMock(HalfFlake\Shard::class, [
            'datacenter' => 1,
            'node' => 1,
        ]);

        $clock = $this->createMock(HalfFlake\Clock::class);
        $generator = new HalfFlake\Generator($shard, $clock);

        self::assertInstanceOf(HalfFlake\Algorithm::class, $generator);
        self::assertInstanceOf(HalfFlake\Algorithm::class, $generator);
    }

    public function invalidSeedProvider(): array
    {
        return [
            [0, 0, 1000],
            [0, 1, 1000],
            [33, 1, 1000],
            [1, 0, 1001],
            [1, 65, 1001],
        ];
    }

    /**
     * @dataProvider invalidSeedProvider
     */
    public function testInvalidShardThrowsException(int $datacenter, int $node, int $exceptionCode): void
    {
        $clock = $this->createMock(HalfFlake\Clock::class);

        $shard = $this->createConfiguredMock(HalfFlake\Shard::class, [
            'datacenter' => $datacenter,
            'node' => $node,
        ]);

        $this->expectException(HalfFlake\RuntimeException::class);
        $this->expectExceptionCode($exceptionCode);

        new HalfFlake\Generator($shard, $clock);
    }

    public function testSequenceIsIncrementing(): void
    {
        $shard = $this->createConfiguredMock(HalfFlake\Shard::class, [
            'datacenter' => 1,
            'node' => 1,
        ]);

        $clock = $this->createMock(HalfFlake\Clock::class);
        $generator = new HalfFlake\Generator($shard, $clock);

        // half of the range because we call nextId twice
        $iteration = (1 << HalfFlake\Algorithm::BITS_SEQUENCE) / 2;

        while ($iteration > 0) {
            --$iteration;
            $clock->method('epoch')->will($this->returnValue($iteration));

            self::assertNotSame(
                $generator->nextId(),
                $generator->nextId(),
                sprintf('Error on iteration #%d', $iteration)
            );
        }

        // next id must trigger sequence overflow
        $this->expectException(HalfFlake\RuntimeException::class);
        $this->expectExceptionCode(1002);

        $generator->nextId();
    }

    public function equalShardIdentifierProvider(): array
    {
        return [
            [1, 2],
            [2, 3],
            [3, 4],
            [1, 5],
            [3, 6],
        ];
    }

    /**
     * @dataProvider equalShardIdentifierProvider
     */
    public function testDifferentIdDatacenterWithSameIdNodeGeneratesDifferentIds(
        int $idDatacenter1,
        int $idDatacenter2
    ): void {
        $shard1 = $this->createConfiguredMock(HalfFlake\Shard::class, [
            'datacenter' => $idDatacenter1,
            'node' => 2,
        ]);

        $shard2 = $this->createConfiguredMock(HalfFlake\Shard::class, [
            'datacenter' => $idDatacenter2,
            'node' => 2,
        ]);

        $clock = $this->createConfiguredMock(HalfFlake\Clock::class, ['epoch' => 1]);

        $generator1 = new HalfFlake\Generator($shard1, $clock);
        $generator2 = new HalfFlake\Generator($shard2, $clock);

        self::assertNotSame($generator1->nextId(), $generator2->nextId());
    }

    /**
     * @dataProvider equalShardIdentifierProvider
     */
    public function testDifferentIdNodeWithinOneDatacenterGeneratesDifferentIds(int $idNode1, int $idNode2): void
    {
        $shard1 = $this->createConfiguredMock(HalfFlake\Shard::class, [
            'datacenter' => 2,
            'node' => $idNode1,
        ]);

        $shard2 = $this->createConfiguredMock(HalfFlake\Shard::class, [
            'datacenter' => 2,
            'node' => $idNode2,
        ]);

        $clock = $this->createConfiguredMock(HalfFlake\Clock::class, ['epoch' => 1]);

        $generator1 = new HalfFlake\Generator($shard1, $clock);
        $generator2 = new HalfFlake\Generator($shard2, $clock);

        self::assertNotSame($generator1->nextId(), $generator2->nextId());
    }

    public function timestampProvider(): array
    {
        return [
            [-1],
            [4398046511104], //max decimal exactly 42 bits long
        ];
    }

    /**
     * @dataProvider timestampProvider
     */
    public function testTimestampOverflow(int $timestamp): void
    {
        $shard = $this->createConfiguredMock(HalfFlake\Shard::class, [
            'datacenter' => 1,
            'node' => 1,
        ]);

        $clock = $this->createConfiguredMock(HalfFlake\Clock::class, ['epoch' => $timestamp]);

        $generator = new HalfFlake\Generator($shard, $clock);

        $this->expectException(HalfFlake\RuntimeException::class);
        $this->expectExceptionCode(1003);

        $generator->nextId();
    }

    public function testClockMovedBackwards(): void
    {
        $shard = $this->createConfiguredMock(HalfFlake\Shard::class, [
            'datacenter' => 1,
            'node' => 1,
        ]);

        $clock = $this->createMock(HalfFlake\Clock::class);
        $clock->expects($this->any())
            ->method('epoch')
            ->will($this->onConsecutiveCalls(42, 41));

        $generator = new HalfFlake\Generator($shard, $clock);

        $generator->nextId();

        $this->expectException(HalfFlake\RuntimeException::class);
        $this->expectExceptionCode(1004);

        $generator->nextId();
    }
}
