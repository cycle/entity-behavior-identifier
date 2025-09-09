<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Identifier\Snowflake as BaseSnowflake;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeGeneric as Listener;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Ramsey\Identifier\Snowflake\Epoch;
use Ramsey\Identifier\Snowflake\GenericSnowflake;
use Ramsey\Identifier\Snowflake\GenericSnowflakeFactory;

/**
 * A distributed ID generation system developed by Twitter that produces
 * 64-bit unique, sortable identifiers
 *
 * Use {@see Listener::setDefaults()} to set default node and epoch offset.
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), NamedArgumentConstructor]
final class SnowflakeGeneric extends BaseSnowflake
{
    /**
     * @param non-empty-string $field Snowflake property name
     * @param non-empty-string|null $column Snowflake column name
     * @param int<0, 1023>|null $node A node identifier to use when creating Snowflakes
     * @param Epoch|int|null $epochOffset The offset from the Unix Epoch in milliseconds
     * @param bool $nullable Indicates whether to generate a new Snowflake or not
     */
    public function __construct(
        string $field = 'snowflake',
        ?string $column = null,
        private readonly ?int $node = null,
        private readonly Epoch|int|null $epochOffset = null,
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
     * @param int $epochOffset The offset from the Unix Epoch in milliseconds
     *
     * @see GenericSnowflakeFactory::create()
     */
    public static function create(
        int|string $identifier,
        int $epochOffset,
    ): GenericSnowflake {
        return new GenericSnowflake($identifier, $epochOffset);
    }

    #[\Override]
    protected function getTypecast(): array
    {
        $epochOffset = $this->epochOffset ?? Listener::getEpochOffset();
        $epochOffset instanceof Epoch and $epochOffset = $epochOffset->value;

        return [self::class, 'create', [$epochOffset]];
    }

    #[\Override]
    protected function getListenerClass(): string
    {
        return Listener::class;
    }

    /**
     * @return array{
     *     field: non-empty-string,
     *     node: null|int<0, 1023>,
     *     epochOffset: Epoch|int|null,
     *     nullable: bool
     * }
     */
    #[\Override]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'node' => $this->node,
            'epochOffset' => $this->epochOffset,
            'nullable' => $this->nullable,
        ];
    }
}
