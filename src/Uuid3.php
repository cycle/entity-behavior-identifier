<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid3 as Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid as BaseUuid;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Identifier\Uuid;
use Ramsey\Identifier\Uuid\NamespaceId;

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
     * @param NamespaceId|Uuid|string $namespace The UUID namespace to use when creating this version 3 identifier
     * @param non-empty-string $name The name used to create the version 3 identifier in the given namespace
     * @param non-empty-string $field Uuid property name
     * @param non-empty-string|null $column Uuid column name
     * @param bool $nullable Indicates whether to generate a new UUID or not
     *
     * @see \Ramsey\Identifier\Uuid\UuidFactory::v3()
     */
    public function __construct(
        private NamespaceId|Uuid|string $namespace,
        private string $name,
        string $field = 'uuid',
        ?string $column = null,
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
        'namespace' => 'NamespaceId|Uuid|string',
        'name' => 'string',
        'nullable' => 'bool',
    ])]
    #[\Override]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'namespace' => $this->namespace,
            'name' => $this->name,
            'nullable' => $this->nullable,
        ];
    }
}
