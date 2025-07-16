<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Identifier\Snowflake as BaseSnowflake;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeInstagram as Listener;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Identifier\Snowflake\InstagramSnowflakeFactory;
use Ramsey\Identifier\SnowflakeFactory;

/**
 * A Snowflake identifier for use with the Instagram photo and video sharing social media platform
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), NamedArgumentConstructor]
final class SnowflakeInstagram extends BaseSnowflake
{
    /**
     * @param non-empty-string $field Snowflake property name
     * @param string|null $column Snowflake column name
     * @param int $shardId A shard identifier to use when creating Snowflakes
     * @param bool $nullable Indicates whether to generate a new Snowflake or not
     *
     * @see \Ramsey\Identifier\Snowflake\DiscordSnowflakeFactory::create()
     */
    public function __construct(
        string $field = 'snowflake',
        ?string $column = null,
        private int $shardId = 0,
        bool $nullable = false,
    ) {
        $this->field = $field;
        $this->column = $column;
        $this->nullable = $nullable;
    }

    #[\Override]
    protected function getListenerClass(): string
    {
        return Listener::class;
    }

    #[ArrayShape([
        'field' => 'string',
        'shardId' => 'int',
        'nullable' => 'bool',
    ])]
    #[\Override]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'shardId' => $this->shardId,
            'nullable' => $this->nullable,
        ];
    }

    #[\Override]
    protected function snowflakeFactory(): SnowflakeFactory
    {
        return new InstagramSnowflakeFactory($this->shardId);
    }
}
