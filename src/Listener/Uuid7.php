<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Ramsey\Identifier\Uuid\UuidV7;

/**
 * Generates UUIDv7 (time-ordered with random data) identifiers for entities.
 */
final class Uuid7 extends BaseUuid
{
    /**
     * @param non-empty-string $field The name of the field to store the UUID
     * @param bool $nullable Indicates whether the UUID can be null
     */
    public function __construct(
        string $field,
        bool $nullable = false,
    ) {
        parent::__construct($field, $nullable);
    }

    #[\Override]
    protected function createValue(): UuidV7
    {
        return $this->factory->v7();
    }
}
