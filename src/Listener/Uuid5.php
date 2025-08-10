<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Cycle\ORM\Entity\Behavior\Identifier\Listener\BaseUuid as Base;
use Ramsey\Identifier\Uuid;
use Ramsey\Identifier\Uuid\NamespaceId;
use Ramsey\Identifier\Uuid\UuidV5Factory;

/**
 * Generates UUIDv5 (name-based with SHA-1 hashing) identifiers for entities.
 */
final class Uuid5 extends Base
{
    private UuidV5Factory $factory;

    /**
     * @param non-empty-string $field The name of the field to store the UUID
     * @param NamespaceId|BaseUuid|string $namespace The namespace UUID
     * @param string $name The name to hash
     * @param bool $nullable Indicates whether the UUID can be null
     */
    public function __construct(
        string $field,
        private readonly NamespaceId|Uuid|string $namespace,
        private readonly string $name,
        bool $nullable = false,
    ) {
        $this->factory = new UuidV5Factory();
        parent::__construct($field, $nullable);
    }

    #[\Override]
    protected function createValue(): \Ramsey\Identifier\Uuid
    {
        return $this->factory->create($this->namespace, $this->name);
    }
}
