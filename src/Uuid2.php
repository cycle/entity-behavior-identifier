<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier;

use Cycle\ORM\Entity\Behavior\Identifier\Defaults\Uuid2 as Defaults;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid2 as Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid as BaseUuid;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Identifier\Service\Nic\Nic;
use Ramsey\Identifier\Uuid\DceDomain;

/**
 * Uses a version 2 (DCE Security) UUID from a local domain, local
 * identifier, host ID, clock sequence, and the current time
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE), NamedArgumentConstructor]
final class Uuid2 extends BaseUuid
{
    private DceDomain|int $localDomain;
    private ?int $localIdentifier;

    /**
     * @var Nic|int<0, 281474976710655>|non-empty-string|null $node
     */
    private Nic|int|string|null $node;

    private ?int $clockSeq;

    /**
     * @param non-empty-string $field Uuid property name
     * @param non-empty-string|null $column Uuid column name
     * @param DceDomain|int|null $localDomain The local domain to which the local identifier belongs; this defaults to "Person"
     *      and if $localIdentifier is not provided, the factory will attempt to get a suitable local ID for the domain
     *      (e.g., the UID or GID of the user running the script).
     * @param int<0, 4294967295> | null $localIdentifier A 32-bit local identifier belonging to the local domain
     *      specified in `$localDomain`; if no identifier is provided, the factory will attempt to get a suitable local
     *      ID for the domain (e.g., the UID or GID of the user running the script).
     * @param Nic|int<0, 281474976710655>|non-empty-string|null $node A 48-bit integer or hexadecimal string
     *      representing the hardware address of the machine where this identifier was generated.
     * @param int|null $clockSeq A number used to help avoid duplicates that could arise when the clock is set
     *      backwards in time or the node ID changes; we take the modulo of this integer divided by 16,384, giving it an
     *      effective range of 0-16383 (i.e., 14 bits).
     * @param bool $nullable Indicates whether to generate a new UUID or not
     *
     * @see \Ramsey\Identifier\Uuid\UuidFactory::v2()
     */
    public function __construct(
        string $field = 'uuid',
        ?string $column = null,
        DceDomain|int|null $localDomain = null,
        ?int $localIdentifier = null,
        Nic|int|string|null $node = null,
        ?int $clockSeq = null,
        bool $nullable = false,
    ) {
        $this->field = $field;
        $this->column = $column;
        $this->nullable = $nullable;
        $this->localDomain = $localDomain === null ? Defaults::getLocalDomain() : $localDomain;
        $this->localIdentifier = $localIdentifier === null ? Defaults::getLocalIdentifier() : $localIdentifier;
        $this->node = $node === null ? Defaults::getNode() : $node;
        $this->clockSeq = $clockSeq === null ? Defaults::getClockSeq() : $clockSeq;
    }

    #[\Override]
    protected function getListenerClass(): string
    {
        return Listener::class;
    }

    #[ArrayShape([
        'field' => 'string',
        'localDomain' => 'int',
        'localIdentifier' => 'int|null',
        'node' => 'int|string|null',
        'clockSeq' => 'int|null',
        'nullable' => 'bool',
    ])]
    #[\Override]
    protected function getListenerArgs(): array
    {
        return [
            'field' => $this->field,
            'localDomain' => \is_int($this->localDomain) ? DceDomain::from($this->localDomain) : $this->localDomain,
            'localIdentifier' => $this->localIdentifier,
            'node' => $this->node instanceof Nic ? $this->node->address() : $this->node,
            'clockSeq' => $this->clockSeq,
            'nullable' => $this->nullable,
        ];
    }
}
