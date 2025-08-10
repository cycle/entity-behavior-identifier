<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Ramsey\Identifier\Uuid\DceDomain;
use Ramsey\Identifier\Uuid\UuidV2Factory;

/**
 * Generates UUIDv2 (DCE Security) identifiers for entities.
 * You can set default values using the {@see setDefaults()} method.
 */
final class Uuid2 extends BaseUuid
{
    private static DceDomain|int $defaultLocalDomain = DceDomain::Person;

    /** @var int<0, 4294967295>|null */
    private static ?int $defaultLocalIdentifier = null;

    /** @var int<0, 281474976710655>|non-empty-string|null */
    private static int|string|null $defaultNode = null;

    private static ?int $defaultClockSeq = null;
    private UuidV2Factory $factory;

    /**
     * @param non-empty-string $field The name of the field to store the UUID
     * @param bool $nullable Indicates whether the UUID can be null
     * @param DceDomain|int|null $localDomain The local domain to which the local identifier belongs
     * @param int<0, 4294967295>|null $localIdentifier A 32-bit local identifier belonging to the local domain
     * @param int<0, 281474976710655>|non-empty-string|null $node A 48-bit integer or hexadecimal string representing the hardware address
     * @param int|null $clockSeq A number used to help avoid duplicates that could arise when the clock is set backwards in time
     */
    public function __construct(
        string $field,
        bool $nullable = false,
        private readonly DceDomain|int|null $localDomain = null,
        private readonly ?int $localIdentifier = null,
        private readonly int|string|null $node = null,
        private readonly ?int $clockSeq = null,
    ) {
        $this->factory = new UuidV2Factory();
        parent::__construct($field, $nullable);
    }

    /**
     * Set default values for UUIDv2 generation.
     *
     * @param DceDomain|int|null $localDomain The local domain
     * @param int<0, 4294967295>|null $localIdentifier The local identifier
     * @param int<0, 281474976710655>|non-empty-string|null $node The node
     * @param int|null $clockSeq The clock sequence
     */
    public static function setDefaults(
        DceDomain|int|null $localDomain,
        ?int $localIdentifier,
        int|string|null $node,
        ?int $clockSeq,
    ): void {
        if ($localDomain !== null) {
            self::$defaultLocalDomain = $localDomain;
        }
        self::$defaultLocalIdentifier = $localIdentifier;
        self::$defaultNode = $node;
        self::$defaultClockSeq = $clockSeq;
    }

    #[\Override]
    protected function createValue(): \Ramsey\Identifier\Uuid
    {
        $localDomain = $this->localDomain ?? self::$defaultLocalDomain;
        $localIdentifier = $this->localIdentifier ?? self::$defaultLocalIdentifier;
        $node = $this->node ?? self::$defaultNode;
        $clockSeq = $this->clockSeq ?? self::$defaultClockSeq;

        $localDomain = \is_int($localDomain) ? DceDomain::from($localDomain) : $localDomain;

        return $this->factory->create($localDomain, $localIdentifier, $node, $clockSeq);
    }
}
