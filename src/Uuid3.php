<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid3 as Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid as BaseUuid;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Identifier\Uuid;
use Ramsey\Identifier\Uuid\NamespaceId;
use Ramsey\Identifier\Uuid\UuidV3;
use Ramsey\Identifier\Uuid\UuidV3Factory;

/**
 * Uses a version 3 (name-based) UUID based on the MD5 hash of a
 * namespace ID and a name
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), NamedArgumentConstructor]
final class Uuid3 extends BaseUuid
{
    /**
     * @param non-empty-string $field Uuid property name
     * @param non-empty-string|null $column Uuid column name
     * @param NamespaceId|Uuid|string|null $namespace The UUID namespace to use when creating this version 3 identifier
     * @param non-empty-string|null $name The name used to create the version 3 identifier in the given namespace
     * @param bool $nullable Indicates whether to generate a new UUID or not
     *
     * @see \Ramsey\Identifier\Uuid\UuidFactory::v3()
     */
    public function __construct(
        string $field = 'uuid',
        ?string $column = null,
        private readonly NamespaceId|Uuid|string|null $namespace = null,
        private readonly ?string $name = null,
        bool $nullable = false,
    ) {
        $this->field = $field;
        $this->column = $column;
        $this->nullable = $nullable;
    }

    /**
     * Create a new UUIDv3 instance from an existing identifier value.
     *
     * @param non-empty-string $identifier The identifier to create the Uuid from
     */
    public static function create(string $identifier): UuidV3
    {
        return (new UuidV3Factory())->createFromString($identifier);
    }

    #[\Override]
    protected function getTypecast(): array
    {
        return [self::class, 'create'];
    }

    #[\Override]
    protected function getListenerClass(): string
    {
        return Listener::class;
    }

    #[ArrayShape([
        'field' => 'string',
        'namespace' => 'NamespaceId|Uuid|string|null',
        'name' => 'string|null',
        'nullable' => 'bool',
    ])]
    #[\Override]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'namespace' => $this->namespace instanceof NamespaceId ? $this->namespace->value : $this->namespace,
            'name' => $this->name,
            'nullable' => $this->nullable,
        ];
    }
}
