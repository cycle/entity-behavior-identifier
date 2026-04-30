<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Schema\BaseModifier;
use Cycle\ORM\Entity\Behavior\Schema\RegistryModifier;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\Schema\Registry;

abstract class Snowflake extends BaseModifier
{
    /** @var non-empty-string|null */
    protected ?string $column = null;

    /** @var non-empty-string */
    protected string $field;

    protected bool $nullable = false;

    #[\Override]
    public function compute(Registry $registry): void
    {
        $modifier = new RegistryModifier($registry, $this->role);
        /** @var non-empty-string column */
        $this->column = $modifier->findColumnName($this->field, $this->column);
        if (\is_string($this->column) && $this->column !== '') {
            $modifier->addSnowflakeColumn(
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

        $modifier->addSnowflakeColumn(
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
     * @return array{0: class-string, 1: non-empty-string, 2?: non-empty-array}
     */
    abstract protected function getTypecast(): array;
}
