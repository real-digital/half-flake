<?php
declare(strict_types=1);

namespace Real\HalfFlake;

interface Shard
{
    /**
     * An unique datacenter identifier
     *
     * @see Algorithm::BITS_DATACENTER
     */
    public function datacenter(): int;

    /**
     * An unique node identifier within a certain datacenter
     *
     * @see Algorithm::BITS_NODE
     */
    public function node(): int;
}
