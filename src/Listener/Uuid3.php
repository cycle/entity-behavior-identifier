<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Cycle\ORM\Entity\Behavior\Identifier\Listener\BaseUuid as Base;
use Ramsey\Identifier\Uuid;
use Ramsey\Identifier\Uuid\NamespaceId;
use Ramsey\Identifier\Uuid\UuidFactory;

/**
 * Generates UUIDv3 (name-based with MD5 hashing) identifiers for entities.
 */
final class Uuid3 extends Base
{
    private readonly UuidFactory $factory;

    /**
     * @param non-empty-string $field The name of the field to store the UUID
     * @param NamespaceId|Uuid|string $namespace The namespace UUID
     * @param string $name The name to hash
     * @param bool $nullable Indicates whether the UUID can be null
     */
    public function __construct(
        string $field,
        private readonly NamespaceId|Uuid|string $namespace,
        private readonly string $name,
        bool $nullable = false,
    ) {
        $this->factory = new UuidFactory();
        parent::__construct($field, $nullable);
    }

    #[\Override]
    protected function createValue(): \Ramsey\Identifier\Uuid
    {
        return $this->factory->v3($this->namespace, $this->name);
    }
}
