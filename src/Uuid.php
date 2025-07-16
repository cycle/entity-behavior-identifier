<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Schema\BaseModifier;
use Cycle\ORM\Entity\Behavior\Schema\RegistryModifier;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\Schema\Registry;
use Ramsey\Identifier\Uuid\UntypedUuid;
use Ramsey\Identifier\Uuid\UuidFactory;

abstract class Uuid extends BaseModifier
{
    protected ?string $column = null;
    protected string $field;
    protected bool $nullable = false;

    /**
     * @param non-empty-string $value
     */
    public static function fromString(string $value): UntypedUuid
    {
        return (new UuidFactory())->createFromString($value);
    }

    #[\Override]
    public function compute(Registry $registry): void
    {
        $modifier = new RegistryModifier($registry, $this->role);
        $this->column = $modifier->findColumnName($this->field, $this->column);
        if (\is_string($this->column) && $this->column !== '') {
            $modifier->addUuidColumn(
                $this->column,
                $this->field,
                $this->nullable ? null : GeneratedField::BEFORE_INSERT,
            )->nullable($this->nullable);

            $modifier->setTypecast(
                $registry->getEntity($this->role)->getFields()->get($this->field),
                [self::class, 'fromString'],
            );
        }
    }

    #[\Override]
    public function render(Registry $registry): void
    {
        $modifier = new RegistryModifier($registry, $this->role);
        /** @var non-empty-string column */
        $this->column = $modifier->findColumnName($this->field, $this->column) ?? $this->field;

        $modifier->addUuidColumn(
            $this->column,
            $this->field,
            $this->nullable ? null : GeneratedField::BEFORE_INSERT,
        )->nullable($this->nullable);

        $modifier->setTypecast(
            $registry->getEntity($this->role)->getFields()->get($this->field),
            [self::class, 'fromString'],
        );
    }
}
