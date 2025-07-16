<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Identifier\Snowflake as BaseSnowflake;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeGeneric as Listener;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Identifier\Snowflake\Epoch;
use Ramsey\Identifier\Snowflake\GenericSnowflakeFactory;
use Ramsey\Identifier\SnowflakeFactory;

/**
 * A distributed ID generation system developed by Twitter that produces
 * 64-bit unique, sortable identifiers
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
     * @param string|null $column Snowflake column name
     * @param int $node A node identifier to use when creating Snowflakes
     * @param Epoch | int $epochOffset The offset from the Unix Epoch in milliseconds
     * @param bool $nullable Indicates whether to generate a new Snowflake or not
     *
     * @see \Ramsey\Identifier\Snowflake\GenericSnowflakeFactory::create()
     */
    public function __construct(
        string $field = 'snowflake',
        ?string $column = null,
        private int $node = 0,
        private Epoch|int $epochOffset = 0,
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
        'node' => 'Epoch|int',
        'epochOffset' => 'int',
        'nullable' => 'bool',
    ])]
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

    #[\Override]
    protected function snowflakeFactory(): SnowflakeFactory
    {
        return new GenericSnowflakeFactory($this->node, $this->epochOffset);
    }
}
