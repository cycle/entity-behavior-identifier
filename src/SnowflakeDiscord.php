<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Identifier\Snowflake as BaseSnowflake;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeDiscord as Listener;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ArrayShape;
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
     * @param string|null $column Snowflake column name
     * @param int $workerId A worker identifier to use when creating Snowflakes
     * @param int $processId A process identifier to use when creating Snowflakes
     * @param bool $nullable Indicates whether to generate a new Snowflake or not
     *
     * @see \Ramsey\Identifier\Snowflake\DiscordSnowflakeFactory::create()
     */
    public function __construct(
        string $field = 'snowflake',
        ?string $column = null,
        private int $workerId = 0,
        private int $processId = 0,
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
        'workerId' => 'int',
        'processId' => 'int',
        'nullable' => 'bool',
    ])]
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
