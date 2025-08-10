<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Ramsey\Identifier\Uuid\UuidV4Factory;

/**
 * Generates UUIDv4 (random) identifiers for entities.
 */
final class Uuid4 extends BaseUuid
{
    private UuidV4Factory $factory;

    /**
     * @param non-empty-string $field The name of the field to store the UUID
     * @param bool $nullable Indicates whether the UUID can be null
     */
    public function __construct(
        string $field,
        bool $nullable = false,
    ) {
        $this->factory = new UuidV4Factory();
        parent::__construct($field, $nullable);
    }

    #[\Override]
    protected function createValue(): \Ramsey\Identifier\Uuid
    {
        return $this->factory->create();
    }
}
