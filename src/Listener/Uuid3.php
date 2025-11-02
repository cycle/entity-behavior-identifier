<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Cycle\ORM\Entity\Behavior\Identifier\Listener\BaseUuid as Base;
use Ramsey\Identifier\Uuid;
use Ramsey\Identifier\Uuid\NamespaceId;
use Ramsey\Identifier\Uuid\UuidV3;

/**
 * Generates UUIDv3 (name-based with MD5 hashing) identifiers for entities.
 */
final class Uuid3 extends Base
{
    private static NamespaceId|Uuid|string|null $defaultNamespace = null;
    private static ?string $defaultName = null;

    /**
     * @param non-empty-string $field The name of the field to store the UUID
     * @param bool $nullable Indicates whether the UUID can be null
     * @param NamespaceId|Uuid|string|null $namespace The namespace UUID
     * @param string|null $name The name to hash
     */
    public function __construct(
        string $field,
        bool $nullable = false,
        private readonly NamespaceId|Uuid|string|null $namespace = null,
        private readonly ?string $name = null,
    ) {
        parent::__construct($field, $nullable);
    }

    /**
     * Set default values for UUIDv3 generation.
     *
     * @param NamespaceId|Uuid|string|null $namespace
     * @param string|null $name
     */
    public static function setDefaults(
        NamespaceId|Uuid|string|null $namespace,
        ?string $name,
    ): void {
        self::$defaultNamespace = $namespace;
        self::$defaultName = $name;
    }

    #[\Override]
    protected function createValue(): UuidV3
    {
        $namespace = $this->namespace ?? self::$defaultNamespace;
        $name = $this->name ?? self::$defaultName;

        if ($namespace === null) {
            throw new \InvalidArgumentException('Namespace must be specified.');
        }

        if ($name === null) {
            throw new \InvalidArgumentException('Name must be specified.');
        }

        return $this->factory->v3($namespace, $name);
    }
}
