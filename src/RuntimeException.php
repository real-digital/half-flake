<?php
declare(strict_types=1);

namespace Real\HalfFlake;

class RuntimeException extends \RuntimeException implements Throwable
{
    # @formatter:off
    public const CODE_DATACENTER             = 1000;
    public const CODE_NODE                   = 1001;
    public const CODE_SEQUENCE_OVERFLOW      = 1002;
    public const CODE_TIMESTAMP_OVERFLOW     = 1003;
    public const CODE_CLOCK_BACKWARD         = 1004;
    # @formatter:on
}
