<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Ramsey\Identifier\Snowflake\TwitterSnowflake;
use Ramsey\Identifier\Snowflake\TwitterSnowflakeFactory;

/**
 * Generates Twitter Snowflake identifiers for entities.
 * You can set default machine ID using the {@see setDefaults()} method.
 */
final class SnowflakeTwitter extends Snowflake
{
    /** @var int<0, 1023> */
    private static int $machineId = 0;

    private TwitterSnowflakeFactory $factory;

    /**
     * @param non-empty-string $field The name of the field to store the Snowflake identifier
     * @param bool $nullable Indicates whether the Snowflake identifier can be null
     * @param int<0, 1023>|null $machineId A machine identifier to use when creating Snowflakes
     */
    public function __construct(
        string $field,
        bool $nullable = false,
        ?int $machineId = null,
    ) {
        $machineId ??= self::$machineId;
        $this->factory = new TwitterSnowflakeFactory($machineId);
        parent::__construct($field, $nullable);
    }

    /**
     * Set default machine ID for Snowflake generation.
     *
     * @param null|int<0, 1023> $machineId The machine ID to set. Null to use the default (0).
     */
    public static function setDefaults(?int $machineId): void
    {
        if ($machineId !== null && ($machineId < 0 || $machineId > 1023)) {
            throw new \InvalidArgumentException('Machine ID must be between 0 and 1023.');
        }

        self::$machineId = (int) $machineId;
    }

    #[\Override]
    protected function createValue(): TwitterSnowflake
    {
        return $this->factory->create();
    }
}
