<?php
declare(strict_types=1);

namespace Real\HalfFlake;

interface Clock
{
    /**
     * Shift the beginning of the epoch
     * to postpone the Year 2038 problem @link https://en.wikipedia.org/wiki/Year_2038_problem
     *
     * @see Algorithm::BITS_TIMESTAMP
     */
    public const EPOCH_BEGINNING = '2019-06-27 10:53:54';

    /**
     * Timestamp from the beginning of the shifted epoch with a milliseconds precision
     */
    public function epoch(): int;
}
