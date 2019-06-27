<?php
declare(strict_types=1);

namespace Real\HalfFlake;

final class Generator implements Algorithm
{
    /**
     * Since PHP does not support unsigned integers and negative IDs are something weird,
     * we are going to limit the IDs capacity within range [1; PHP_INT_MAX]
     *
     * @see PHP_INT_MAX
     * @see Algorithm::BITS_SIGN
     */
    private const SIGN = 0;

    private const SHIFT_SIGN = self::BITS_TOTAL - self::BITS_SIGN;
    private const SHIFT_TIMESTAMP = self::SHIFT_SIGN - self::BITS_TIMESTAMP;
    private const SHIFT_DATACENTER = self::SHIFT_TIMESTAMP - self::BITS_DATACENTER;
    private const SHIFT_NODE = self::SHIFT_DATACENTER - self::BITS_NODE;
    private const SHIFT_SEQUENCE = self::SHIFT_NODE - self::BITS_SEQUENCE;

    /** @var int */
    private $datacenter;

    /** @var int */
    private $node;

    /** @var Clock */
    private $clock;

    /** @var int */
    private $lastTimestamp = 0;

    /** @var int */
    private $sequence = 0;

    public function __construct(Shard $shard, Clock $clock)
    {
        $datacenter = $shard->datacenter();

        if ($datacenter < 1 || $datacenter > (1 << Algorithm::BITS_DATACENTER)) {
            throw new RuntimeException('Invalid datacenter', RuntimeException::CODE_DATACENTER);
        }

        $node = $shard->node();

        if ($node < 1 || $node > (1 << Algorithm::BITS_NODE)) {
            throw new RuntimeException('Invalid node', RuntimeException::CODE_NODE);
        }

        $this->datacenter = $datacenter - 1;
        $this->node = $node - 1;

        $this->clock = $clock;
    }

    /**
     * @return int shifted timestamp with a milliseconds precision
     * @throws RuntimeException
     */
    private function timestamp(): int
    {
        $timestamp = $this->clock->epoch();

        if ($timestamp < 0 || $timestamp >= (1 << self::BITS_TIMESTAMP)) {
            throw new RuntimeException('Timestamp overflow!', RuntimeException::CODE_TIMESTAMP_OVERFLOW);
        }

        if ($timestamp < $this->lastTimestamp) {
            throw new RuntimeException('Clock moved backwards!', RuntimeException::CODE_CLOCK_BACKWARD);
        }

        return $timestamp;
    }

    /**
     * @return int the next sequence number within the same millisecond
     * @throws RuntimeException
     */
    private function sequence(int $timestamp): int
    {
        if ($timestamp === $this->lastTimestamp) { //increment
            ++$this->sequence;

            if ($this->sequence >= (1 << self::BITS_SEQUENCE)) {
                throw new RuntimeException('Generating too fast, slow down!', RuntimeException::CODE_SEQUENCE_OVERFLOW);
            }
        } else { //reset
            $this->sequence = 0;
        }

        $this->lastTimestamp = $timestamp;

        return $this->sequence;
    }

    private function generate(): int
    {
        $timestamp = $this->timestamp();
        $sequence = $this->sequence($timestamp);

        $id = self::SIGN << self::SHIFT_SIGN;
        $id |= $timestamp << self::SHIFT_TIMESTAMP;
        $id |= $this->datacenter << self::SHIFT_DATACENTER;
        $id |= $this->node << self::SHIFT_NODE;
        $id |= $sequence << self::SHIFT_SEQUENCE;

        return $id;
    }

    /**
     * @inheritdoc
     */
    public function nextId(): string
    {
        return (string)$this->generate();
    }
}
