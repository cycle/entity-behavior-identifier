<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Ramsey\Identifier\Uuid\UuidV1;

/**
 * Generates UUIDv1 identifiers for entities.
 * You can set default node and clock sequence using the {@see setDefaults()} method.
 */
final class Uuid1 extends BaseUuid
{
    /** @var int<0, 281474976710655>|non-empty-string|null */
    private static int|string|null $defaultNode = null;

    private static ?int $defaultClockSeq = null;

    /**
     * @param non-empty-string $field The name of the field to store the UUID
     * @param bool $nullable Indicates whether the UUID can be null
     * @param int<0, 281474976710655>|non-empty-string|null $node A 48-bit integer or hexadecimal string representing the hardware address
     * @param int|null $clockSeq A number used to help avoid duplicates that could arise when the clock is set backwards in time
     */
    public function __construct(
        string $field,
        bool $nullable = false,
        private readonly int|string|null $node = null,
        private readonly ?int $clockSeq = null,
    ) {
        parent::__construct($field, $nullable);
    }

    /**
     * Set default node and clock sequence for UUIDv1 generation.
     *
     * @param int<0, 281474976710655>|non-empty-string|null $node The node to set
     * @param int|null $clockSeq The clock sequence to set
     */
    public static function setDefaults(int|string|null $node, ?int $clockSeq): void
    {
        self::$defaultNode = $node;
        self::$defaultClockSeq = $clockSeq;
    }

    #[\Override]
    protected function createValue(): UuidV1
    {
        $node = $this->node ?? self::$defaultNode;
        $clockSeq = $this->clockSeq ?? self::$defaultClockSeq;
        return $this->factory->v1($node, $clockSeq);
    }
}
