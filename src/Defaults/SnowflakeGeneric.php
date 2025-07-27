<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Defaults;

use Ramsey\Identifier\Snowflake\Epoch;

final class SnowflakeGeneric
{
    private static int $node = 0;
    private static Epoch|int $epochOffset = 0;

    public static function getNode(): int
    {
        return self::$node;
    }

    public static function setNode(int $node): void
    {
        self::$node = $node;
    }

    public static function getEpochOffset(): Epoch|int
    {
        return self::$epochOffset;
    }

    public static function setEpochOffset(Epoch|int $epochOffset): void
    {
        self::$epochOffset = $epochOffset;
    }
}
