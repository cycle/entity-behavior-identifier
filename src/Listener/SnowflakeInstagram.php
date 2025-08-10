<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Ramsey\Identifier\Snowflake\InstagramSnowflake;
use Ramsey\Identifier\Snowflake\InstagramSnowflakeFactory;

/**
 * Generates Instagram Snowflake identifiers for entities.
 * You can set default shard ID using the {@see setDefaults()} method.
 */
final class SnowflakeInstagram extends Snowflake
{
    /** @var int<0, 1023> */
    private static int $shardId = 0;

    private InstagramSnowflakeFactory $factory;

    /**
     * @param non-empty-string $field The name of the field to store the Snowflake identifier
     * @param bool $nullable Indicates whether the Snowflake identifier can be null
     * @param int<0, 1023>|null $shardId A shard identifier to use when creating Snowflakes
     */
    public function __construct(
        string $field,
        bool $nullable = false,
        ?int $shardId = null,
    ) {
        $shardId ??= self::$shardId;
        $this->factory = new InstagramSnowflakeFactory($shardId);
        parent::__construct($field, $nullable);
    }

    /**
     * Set default shard ID for Snowflake generation.
     *
     * @param null|int<0, 1023> $shardId The shard ID to set. Null to use the default (0).
     */
    public static function setDefaults(?int $shardId): void
    {
        if ($shardId !== null && ($shardId < 0 || $shardId > 1023)) {
            throw new \InvalidArgumentException('Shard ID must be between 0 and 1023.');
        }

        self::$shardId = (int) $shardId;
    }

    #[\Override]
    protected function createValue(): InstagramSnowflake
    {
        return $this->factory->create();
    }
}
