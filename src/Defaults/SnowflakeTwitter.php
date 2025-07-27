<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Defaults;

final class SnowflakeTwitter
{
    private static int $machineId = 0;

    public static function getMachineId(): int
    {
        return self::$machineId;
    }

    public static function setMachineId(int $machineId): void
    {
        self::$machineId = $machineId;
    }
}
