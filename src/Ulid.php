<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Identifier\Listener\Ulid as Listener;
use Cycle\ORM\Entity\Behavior\Schema\BaseModifier;
use Cycle\ORM\Entity\Behavior\Schema\RegistryModifier;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Identifier\Ulid\MaxUlid;
use Ramsey\Identifier\Ulid\NilUlid;
use Ramsey\Identifier\Ulid\Ulid as UlidIdentifier;
use Ramsey\Identifier\Ulid\UlidFactory;

/**
 * Uses a universally unique lexicographically sortable identifier (ULID)
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), NamedArgumentConstructor]
final class Ulid extends BaseModifier
{
    /**
     * @param non-empty-string $field Ulid property name
     * @param string|null $column Ulid column name
     * @param bool $nullable Indicates whether to generate a new Ulid or not
     */
    public function __construct(
        private string $field = 'ulid',
        private ?string $column = null,
        private bool $nullable = false,
    ) {}

    #[\Override]
    public function compute(Registry $registry): void
    {
        $modifier = new RegistryModifier($registry, $this->role);
        $this->column = $modifier->findColumnName($this->field, $this->column);
        if (\is_string($this->column) && $this->column !== '') {
            $modifier->addUlidColumn(
                $this->column,
                $this->field,
                $this->nullable ? null : GeneratedField::BEFORE_INSERT,
            )->nullable($this->nullable);

            $modifier->setTypecast(
                $registry->getEntity($this->role)->getFields()->get($this->field),
                $this->getTypecast(),
            );
        }
    }

    #[\Override]
    public function render(Registry $registry): void
    {
        $modifier = new RegistryModifier($registry, $this->role);
        /** @var non-empty-string column */
        $this->column = $modifier->findColumnName($this->field, $this->column) ?? $this->field;

        $modifier->addUlidColumn(
            $this->column,
            $this->field,
            $this->nullable ? null : GeneratedField::BEFORE_INSERT,
        )->nullable($this->nullable);

        $modifier->setTypecast(
            $registry->getEntity($this->role)->getFields()->get($this->field),
            $this->getTypecast(),
        );
    }

    /**
     * Create a new Ulid instance from an existing identifier value.
     *
     * @param non-empty-string $identifier The identifier to create the Ulid from
     *
     * @see UlidFactory::create()
     */
    public static function create(string $identifier): MaxUlid|NilUlid|UlidIdentifier
    {
        return (new UlidFactory())->createFromString($identifier);
    }

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
