<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Ramsey\Identifier\Snowflake\DiscordSnowflake;
use Ramsey\Identifier\Snowflake\DiscordSnowflakeFactory;

/**
 * Generates Discord Snowflake identifiers for entities.
 * You can set default worker and process IDs using the {@see setDefaults()} method.
 */
final class SnowflakeDiscord extends Snowflake
{
    /** @var int<0, 281474976710655> */
    private static int $defaultWorkerId = 0;

    /** @var null|int<0, 281474976710655> */
    private static ?int $defaultProcessId = null;

    private DiscordSnowflakeFactory $factory;

    /**
     * @param non-empty-string $field The name of the field to store the Snowflake identifier
     * @param bool $nullable Indicates whether the Snowflake identifier can be null
     * @param int<0, 281474976710655>|null $workerId A worker identifier to use when creating Snowflakes
     * @param int<0, 281474976710655>|null $processId A process identifier to use when creating Snowflakes
     */
    public function __construct(
        string $field,
        bool $nullable = false,
        ?int $workerId = null,
        ?int $processId = null,
    ) {
        $workerId ??= self::$defaultWorkerId;
        $processId ??= $this->getProcessId();
        $this->factory = new DiscordSnowflakeFactory($workerId, $processId);
        parent::__construct($field, $nullable);
    }

    /**
     * Set default worker and process IDs for Snowflake generation.
     *
     * @param null|int<0, 281474976710655> $workerId The worker ID to set. Null to use the default (0).
     * @param null|int<0, 281474976710655> $processId The process ID to set. Null to use the current process ID.
     */
    public static function setDefaults(?int $workerId, ?int $processId): void
    {
        if ($workerId !== null && ($workerId < 0 || $workerId > 281474976710655)) {
            throw new \InvalidArgumentException('Worker ID must be between 0 and 281474976710655.');
        }
        if ($processId !== null && ($processId < 0 || $processId > 281474976710655)) {
            throw new \InvalidArgumentException('Process ID must be between 0 and 281474976710655.');
        }

        self::$defaultWorkerId = (int) $workerId;
        self::$defaultProcessId = $processId;
    }

    #[\Override]
    protected function createValue(): DiscordSnowflake
    {
        return $this->factory->create();
    }

    /**
     * Get the current process ID.
     */
    private function getProcessId(): int
    {
        return self::$defaultProcessId === null ? \intval(\getmypid()) : self::$defaultProcessId;
    }
}
