<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid4 as Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid as BaseUuid;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Identifier\Uuid\UuidV4;
use Ramsey\Identifier\Uuid\UuidV4Factory;

/**
 * Uses a version 4 (random) UUID
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), NamedArgumentConstructor]
final class Uuid4 extends BaseUuid
{
    /**
     * @param non-empty-string $field Uuid property name
     * @param non-empty-string|null $column Uuid column name
     * @param bool $nullable Indicates whether to generate a new UUID or not
     *
     * @see \Ramsey\Identifier\Uuid\UuidFactory::v4()
     */
    public function __construct(
        string $field = 'uuid',
        ?string $column = null,
        bool $nullable = false,
    ) {
        $this->field = $field;
        $this->column = $column;
        $this->nullable = $nullable;
    }

    /**
     * Create a new UUIDv4 instance from an existing identifier value.
     *
     * @param non-empty-string $identifier The identifier to create the Uuid from
     */
    public static function create(string $identifier): UuidV4
    {
        return (new UuidV4Factory())->createFromString($identifier);
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
        'nullable' => 'bool',
    ])]
    #[\Override]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'nullable' => $this->nullable,
        ];
    }
}
