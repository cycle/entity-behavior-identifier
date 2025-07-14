<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Ramsey\Identifier\Uuid\DceDomain;
use Ramsey\Identifier\Uuid\UuidFactory;

final class Uuid2
{
    /**
     * @param int<0, 4294967295>| null $localIdentifier $localIdentifier
     * @param int<0, 281474976710655>|non-empty-string|null $node
     */
    public function __construct(
        private string $field = 'uuid',
        private DceDomain|int $localDomain = 0,
        private ?int $localIdentifier = null,
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

        $this->localDomain = \is_int($this->localDomain) ? DceDomain::from($this->localDomain) : $this->localDomain;

        $identifier = (new UuidFactory())->v2(
            $this->localDomain,
            $this->localIdentifier,
            $this->node,
            $this->clockSeq,
        );

        $event->state->register($this->field, $identifier);
    }
}
