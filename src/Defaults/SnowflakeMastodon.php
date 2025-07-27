<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Defaults;

final class SnowflakeMastodon
{
    /**
     * @var non-empty-string|null
     */
    private static ?string $tableName = null;

    /**
     * @return non-empty-string|null
     */
    public static function getTableName(): ?string
    {
        return self::$tableName;
    }

    /**
     * @param non-empty-string|null $tableName
     */
    public static function setTableName(?string $tableName): void
    {
        self::$tableName = $tableName;
    }
}
