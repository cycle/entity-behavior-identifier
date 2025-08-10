<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid6 as Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid as BaseUuid;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Identifier\Service\Nic\Nic;

/**
 * Uses a version 6 (ordered-time) UUID from a host ID, sequence number,
 * and the current time
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), NamedArgumentConstructor]
final class Uuid6 extends BaseUuid
{
    /**
     * @var Nic|int<0, 281474976710655>|non-empty-string|null $node
     */
    private Nic|int|string|null $node = null;

    private ?int $clockSeq = null;

    /**
     * @param non-empty-string $field Uuid property name
     * @param non-empty-string|null $column Uuid column name
     * @param Nic|int<0, 281474976710655>|non-empty-string|null $node A 48-bit integer or hexadecimal string
     *      representing the hardware address of the machine where this identifier was generated
     * @param int|null $clockSeq A number used to help avoid duplicates that could arise when the clock is set
     *      backwards in time or the node ID changes; we take the modulo of this integer divided by 16,384, giving it an
     *      effective range of 0-16383 (i.e., 14 bits)
     * @param bool $nullable Indicates whether to generate a new UUID or not
     *
     * @see \Ramsey\Identifier\Uuid\UuidFactory::v6()
     */
    public function __construct(
        string $field = 'uuid',
        ?string $column = null,
        Nic|int|string|null $node = null,
        ?int $clockSeq = null,
        bool $nullable = false,
    ) {
        $this->field = $field;
        $this->column = $column;
        $this->nullable = $nullable;
        $this->node = $node;
        $this->clockSeq = $clockSeq;
    }

    #[\Override]
    protected function getListenerClass(): string
    {
        return Listener::class;
    }

    #[ArrayShape([
        'field' => 'string',
        'node' => 'int|string|null',
        'clockSeq' => 'int|null',
        'nullable' => 'bool',
    ])]
    #[\Override]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'node' => $this->node instanceof Nic ? $this->node->address() : $this->node,
            'clockSeq' => $this->clockSeq,
            'nullable' => $this->nullable,
        ];
    }
}
