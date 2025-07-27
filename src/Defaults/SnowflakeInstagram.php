<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Defaults;

final class SnowflakeInstagram
{
    private static int $shardId = 0;

    public static function getShardId(): int
    {
        return self::$shardId;
    }

    public static function setShardId(int $shardId): void
    {
        self::$shardId = $shardId;
    }
}
