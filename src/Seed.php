<?php
declare(strict_types=1);

namespace Real\HalfFlake;

final class Seed implements Shard
{
    /** @var int */
    private $datacenter;

    /** @var int */
    private $node;

    public function __construct(int $datacenter, int $node)
    {
        $this->datacenter = $datacenter;
        $this->node = $node;
    }

    /**
     * @inheritdoc
     */
    public function datacenter(): int
    {
        return $this->datacenter;
    }

    /**
     * @inheritdoc
     */
    public function node(): int
    {
        return $this->node;
    }
}
