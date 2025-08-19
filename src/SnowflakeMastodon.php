<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\Database\DatabaseInterface;
use Cycle\ORM\Entity\Behavior\Identifier\Snowflake as BaseSnowflake;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeMastodon as Listener;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
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
     *
     * @see \Ramsey\Identifier\Snowflake\MastodonSnowflakeFactory::create()
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

    #[\Override]
    public static function fromInteger(
        int|string $identifier,
        DatabaseInterface $database,
        array $arguments,
    ): \Ramsey\Identifier\Snowflake {
        return (new MastodonSnowflakeFactory(
            $arguments['tableName'],
        ))->createFromInteger($identifier);
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

    #[\Override]
    protected function getTypecastArgs(): array
    {
        return [
            'tableName' => $this->tableName,
        ];
    }
}
