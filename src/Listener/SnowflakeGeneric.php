<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Ramsey\Identifier\Snowflake;
use Ramsey\Identifier\Snowflake\Epoch;
use Ramsey\Identifier\Snowflake\GenericSnowflakeFactory;

/**
 * Generates generic Snowflake identifiers for entities.
 * You can set default node and epoch offset using the {@see setDefaults()} method.
 */
final class SnowflakeGeneric extends \Cycle\ORM\Entity\Behavior\Identifier\Listener\Snowflake
{
    /** @var int<0, 1023> */
    private static int $node = 0;

    private static Epoch|int $epochOffset = 0;

    private GenericSnowflakeFactory $factory;

    /**
     * @param non-empty-string $field The name of the field to store the Snowflake identifier
     * @param bool $nullable Indicates whether the Snowflake identifier can be null
     * @param int<0, 1023>|null $node A node identifier to use when creating Snowflakes
     * @param Epoch|int|null $epochOffset The offset from the Unix Epoch in milliseconds
     */
    public function __construct(
        string $field,
        bool $nullable = false,
        ?int $node = null,
        Epoch|int|null $epochOffset = null,
    ) {
        $node ??= self::$node;
        $epochOffset ??= self::$epochOffset;
        $this->factory = new GenericSnowflakeFactory($node, $epochOffset);
        parent::__construct($field, $nullable);
    }

    /**
     * Set default node and epoch offset for Snowflake generation.
     *
     * @param null|int<0, 1023> $node The node ID to set. Null to use the default (0).
     * @param Epoch|int|null $epochOffset The epoch offset to set. Null to use the default (0).
     */
    public static function setDefaults(?int $node, Epoch|int|null $epochOffset): void
    {
        if ($node !== null && ($node < 0 || $node > 1023)) {
            throw new \InvalidArgumentException('Node ID must be between 0 and 1023.');
        }

        self::$node = (int) $node;
        if ($epochOffset !== null) {
            self::$epochOffset = $epochOffset;
        }
    }

    #[\Override]
    protected function createValue(): Snowflake
    {
        return $this->factory->create();
    }
}
