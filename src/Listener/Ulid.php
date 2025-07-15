<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Ramsey\Identifier\Ulid\UlidFactory;

final class Ulid
{
    public function __construct(
        private string $field = 'ulid',
        private bool $nullable = false,
    ) {}

    #[Listen(OnCreate::class)]
    public function __invoke(OnCreate $event): void
    {
        if ($this->nullable || isset($event->state->getData()[$this->field])) {
            return;
        }

        $identifier = (new UlidFactory())->create();

        $event->state->register($this->field, $identifier);
    }
}
