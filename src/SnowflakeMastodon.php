<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Identifier\Snowflake as BaseSnowflake;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeMastodon as Listener;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Ramsey\Identifier\Snowflake\MastodonSnowflake;
use Ramsey\Identifier\Snowflake\MastodonSnowflakeFactory;

/**
 * A Snowflake identifier for use with the Mastodon open source platform for decentralized social networking
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), NamedArgumentConstructor]
final class SnowflakeMastodon extends BaseSnowflake
{
    /**
     * @param non-empty-string $field Snowflake property name
     * @param non-empty-string|null $column Snowflake column name
     * @param non-empty-string|null $tableName Database table name ensuring different tables derive separate sequence bases
     * @param bool $nullable Indicates whether to generate a new Snowflake or not
     */
    public function __construct(
        string $field = 'snowflake',
        ?string $column = null,
        private readonly ?string $tableName = null,
        bool $nullable = false,
    ) {
        $this->field = $field;
        $this->column = $column;
        $this->nullable = $nullable;
    }

    /**
     * Identifier factory method from an existing identifier value.
     *
     * @param int<0, max>|numeric-string $identifier The identifier to create the Snowflake from
     *
     * @see MastodonSnowflakeFactory::create()
     */
    public static function create(
        int|string $identifier,
    ): MastodonSnowflake {
        return new MastodonSnowflake($identifier);
    }

    #[\Override]
    protected function getTypecast(): array
    {
        return [self::class, 'create'];
    }

    #[\Override]
    protected function getListenerClass(): string
    {
        return Listener::class;
    }

    /**
     * @return array{
     *     field: non-empty-string,
     *     tableName: non-empty-string|null,
     *     nullable: bool
     * }
     */
    #[\Override]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'tableName' => $this->tableName,
            'nullable' => $this->nullable,
        ];
    }
}
