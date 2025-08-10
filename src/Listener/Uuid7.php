<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Ramsey\Identifier\Uuid\UuidV7Factory;

/**
 * Generates UUIDv7 (time-ordered with random data) identifiers for entities.
 */
final class Uuid7 extends BaseUuid
{
    private UuidV7Factory $factory;

    /**
     * @param non-empty-string $field The name of the field to store the UUID
     * @param bool $nullable Indicates whether the UUID can be null
     */
    public function __construct(
        string $field,
        bool $nullable = false,
    ) {
        $this->factory = new UuidV7Factory();
        parent::__construct($field, $nullable);
    }

    #[\Override]
    protected function createValue(): \Ramsey\Identifier\Uuid
    {
        return $this->factory->create();
    }
}
