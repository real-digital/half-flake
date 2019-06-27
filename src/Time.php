<?php
declare(strict_types=1);

namespace Real\HalfFlake;

final class Time extends \DateTimeImmutable implements Clock
{
    /**
     * Current timestamp with a microseconds precision
     */
    private function metronome(): float
    {
        return microtime(true);
    }

    public function now(): self
    {
        $time = intval($this->metronome());

        return new self("@$time");
    }

    /**
     * @inheritdoc
     */
    public function epoch(): int
    {
        $epochOffset = strtotime(self::EPOCH_BEGINNING) * 1000;

        $timestamp = intval($this->metronome() * 1000);

        return $timestamp - $epochOffset;
    }
}
