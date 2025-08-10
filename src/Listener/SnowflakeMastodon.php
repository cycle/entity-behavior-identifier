<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Ramsey\Identifier\Snowflake\MastodonSnowflake;
use Ramsey\Identifier\Snowflake\MastodonSnowflakeFactory;

/**
 * Generates Mastodon Snowflake identifiers for entities.
 * You can set default table name using the {@see setDefaults()} method.
 */
final class SnowflakeMastodon extends Snowflake
{
    /** @var non-empty-string|null */
    private static ?string $tableName = null;

    private MastodonSnowflakeFactory $factory;

    /**
     * @param non-empty-string $field The name of the field to store the Snowflake identifier
     * @param bool $nullable Indicates whether the Snowflake identifier can be null
     * @param non-empty-string|null $tableName Database table name ensuring different tables derive separate sequence bases
     */
    public function __construct(
        string $field,
        bool $nullable = false,
        ?string $tableName = null,
    ) {
        $tableName ??= self::$tableName;
        $this->factory = new MastodonSnowflakeFactory($tableName);
        parent::__construct($field, $nullable);
    }

    /**
     * Set default table name for Snowflake generation.
     *
     * @param non-empty-string|null $tableName The table name to set. Null to use the default (null).
     */
    public static function setDefaults(?string $tableName): void
    {
        self::$tableName = $tableName;
    }

    #[\Override]
    protected function createValue(): MastodonSnowflake
    {
        return $this->factory->create();
    }
}
