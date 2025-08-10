<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;

abstract class BaseUuid
{
    /**
     * @param non-empty-string $field The name of the field to store the UUID
     * @param bool $nullable Indicates whether the UUID can be null
     */
    public function __construct(
        private readonly string $field,
        private readonly bool $nullable = false,
    ) {}

    #[Listen(OnCreate::class)]
    public function __invoke(OnCreate $event): void
    {
        if ($this->nullable || isset($event->state->getData()[$this->field])) {
            return;
        }

        $event->state->register($this->field, $this->createValue());
    }

    abstract protected function createValue(): \Ramsey\Identifier\Uuid;
}
