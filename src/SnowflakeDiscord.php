<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Identifier\Snowflake as BaseSnowflake;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeDiscord as Listener;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Ramsey\Identifier\Snowflake\DiscordSnowflakeFactory;
use Ramsey\Identifier\SnowflakeFactory;

/**
 * A Snowflake identifier for use with the Discord voice, text, and streaming video platform
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), NamedArgumentConstructor]
final class SnowflakeDiscord extends BaseSnowflake
{
    /**
     * @param non-empty-string $field Snowflake property name
     * @param non-empty-string|null $column Snowflake column name
     * @param int<0, 281474976710655>|null $workerId A worker identifier to use when creating Snowflakes
     * @param int<0, 281474976710655>|null $processId A process identifier to use when creating Snowflakes
     * @param bool $nullable Indicates whether to generate a new Snowflake or not
     *
     * @see \Ramsey\Identifier\Snowflake\DiscordSnowflakeFactory::create()
     */
    public function __construct(
        string $field,
        ?string $column = null,
        private readonly ?int $workerId = null,
        private readonly ?int $processId = null,
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

    /**
     * @return array{
     *     field: non-empty-string,
     *     workerId: null|int<0, 281474976710655>,
     *     processId: null|int<0, 281474976710655>,
     *     nullable: bool
     * }
     */
    #[\Override]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'workerId' => $this->workerId,
            'processId' => $this->processId,
            'nullable' => $this->nullable,
        ];
    }

    #[\Override]
    protected function snowflakeFactory(): SnowflakeFactory
    {
        return new DiscordSnowflakeFactory($this->workerId, $this->processId);
    }
}
