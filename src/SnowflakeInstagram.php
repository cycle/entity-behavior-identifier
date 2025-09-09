<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Identifier\Snowflake as BaseSnowflake;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeInstagram as Listener;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Ramsey\Identifier\Snowflake\InstagramSnowflake;
use Ramsey\Identifier\Snowflake\InstagramSnowflakeFactory;

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
     * @param non-empty-string|null $column Snowflake column name
     * @param int<0, 1023>|null $shardId A shard identifier to use when creating Snowflakes
     * @param bool $nullable Indicates whether to generate a new Snowflake or not
     */
    public function __construct(
        string $field = 'snowflake',
        ?string $column = null,
        private readonly ?int $shardId = null,
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
     * @see InstagramSnowflakeFactory::create()
     */
    public static function create(
        int|string $identifier,
    ): InstagramSnowflake {
        return new InstagramSnowflake($identifier);
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
     *     shardId: null|int<0, 1023>,
     *     nullable: bool
     * }
     */
    #[\Override]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'shardId' => $this->shardId,
            'nullable' => $this->nullable,
        ];
    }
}
