<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Ramsey\Identifier\Service\Nic\Nic;
use Ramsey\Identifier\Uuid\UuidFactory;

final class Uuid6
{
    /**
     * @param int<0, 281474976710655>|non-empty-string|null $node
     */
    public function __construct(
        private string $field = 'uuid',
        private int|string|null $node = null,
        private ?int $clockSeq = null,
        private bool $nullable = false,
    ) {}

    #[Listen(OnCreate::class)]
    public function __invoke(OnCreate $event): void
    {
        if ($this->nullable || isset($event->state->getData()[$this->field])) {
            return;
        }

        $this->node = $this->node instanceof Nic ? $this->node->address() : $this->node;

        $identifier = (new UuidFactory())->v6(
            $this->node,
            $this->clockSeq,
        );

        $event->state->register($this->field, $identifier);
    }
}
