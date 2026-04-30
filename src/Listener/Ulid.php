<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Ramsey\Identifier\Ulid\UlidFactory;

/**
 * Generates ULID identifiers for entities.
 */
final class Ulid
{
    private UlidFactory $factory;

    /**
     * @param non-empty-string $field The name of the field to store the ULID
     * @param bool $nullable Indicates whether the ULID can be null
     */
    public function __construct(
        private readonly string $field,
        private readonly bool $nullable = false,
    ) {
        $this->factory = new UlidFactory();
    }

    #[Listen(OnCreate::class)]
    public function __invoke(OnCreate $event): void
    {
        if ($this->nullable || isset($event->state->getData()[$this->field])) {
            return;
        }

        $event->state->register($this->field, $this->factory->create());
    }
}
