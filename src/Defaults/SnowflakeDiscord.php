<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Defaults;

final class SnowflakeDiscord
{
    private static int $workerId = 0;
    private static int $processId = 0;

    public static function getWorkerId(): int
    {
        return self::$workerId;
    }

    public static function setWorkerId(int $workerId): void
    {
        self::$workerId = $workerId;
    }

    public static function getProcessId(): int
    {
        return self::$processId;
    }

    public static function setProcessId(int $processId): void
    {
        self::$processId = $processId;
    }
}
