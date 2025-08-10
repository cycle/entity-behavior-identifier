<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Identifier\Snowflake as BaseSnowflake;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeTwitter as Listener;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Ramsey\Identifier\Snowflake\TwitterSnowflakeFactory;
use Ramsey\Identifier\SnowflakeFactory;

/**
 * A Snowflake identifier for use with the X (formerly Twitter) social media platform
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), NamedArgumentConstructor]
final class SnowflakeTwitter extends BaseSnowflake
{
    /**
     * @param non-empty-string $field Snowflake property name
     * @param non-empty-string|null $column Snowflake column name
     * @param int<0, 1023>|null $machineId A machine identifier to use when creating Snowflakes
     * @param bool $nullable Indicates whether to generate a new Snowflake or not
     *
     * @see \Ramsey\Identifier\Snowflake\TwitterSnowflakeFactory::create()
     */
    public function __construct(
        string $field,
        ?string $column = null,
        private readonly ?int $machineId = null,
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
     *     machineId: null|int<0, 1023>,
     *     nullable: bool
     * }
     */
    #[\Override]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'machineId' => $this->machineId,
            'nullable' => $this->nullable,
        ];
    }

    #[\Override]
    protected function snowflakeFactory(): SnowflakeFactory
    {
        return new TwitterSnowflakeFactory($this->machineId);
    }
}
