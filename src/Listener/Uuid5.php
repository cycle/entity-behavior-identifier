<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Ramsey\Identifier\Uuid;
use Ramsey\Identifier\Uuid\NamespaceId;
use Ramsey\Identifier\Uuid\UuidFactory;

final class Uuid5
{
    public function __construct(
        private NamespaceId|Uuid|string $namespace,
        private string $name,
        private string $field = 'uuid',
        private bool $nullable = false,
    ) {}

    #[Listen(OnCreate::class)]
    public function __invoke(OnCreate $event): void
    {
        if ($this->nullable || isset($event->state->getData()[$this->field])) {
            return;
        }

        $identifier = (new UuidFactory())->v5(
            $this->namespace,
            $this->name,
        );

        $event->state->register($this->field, $identifier);
    }
}
