<?php
declare(strict_types=1);

namespace Real\HalfFlake;

/**
 * This Interface defines Twitter's Snowflake algorithm basics terms
 *
 * @link https://github.com/twitter/snowflake/blob/snowflake-2010/README.mkd
 */
interface Algorithm
{
    /** BigInt capacity */
    public const BITS_TOTAL = 64;

    /**
     * Since PHP does not support unsigned integers,
     * take care of implementations written on languages other than PHP having native unsigned integers support
     */
    public const BITS_SIGN = 1;

    /**
     * Timestamp with a milliseconds precision (41 bits) with an additional bit (+1 bit)
     * to postpone the Year 2038 problem @link https://en.wikipedia.org/wiki/Year_2038_problem
     */
    public const BITS_TIMESTAMP = 42;

    /** Decimal max 32 */
    public const BITS_DATACENTER = 5;

    /** Decimal max 64 */
    public const BITS_NODE = 6;

    /** Decimal max 1024 IDs per shard per millisecond */
    public const BITS_SEQUENCE = 10;

    /**
     * Generate next identity
     */
    public function nextId(): string;
}
