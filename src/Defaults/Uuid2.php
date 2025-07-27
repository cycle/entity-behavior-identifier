<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Defaults;

use Ramsey\Identifier\Service\Nic\Nic;
use Ramsey\Identifier\Uuid\DceDomain;

final class Uuid2
{
    private static DceDomain|int $localDomain = 0;
    private static ?int $localIdentifier = null;

    /**
     * @var Nic|int<0, 281474976710655>|non-empty-string|null $node
     */
    private static Nic|int|string|null $node = null;

    private static ?int $clockSeq = null;

    public static function getLocalDomain(): DceDomain|int
    {
        return self::$localDomain;
    }

    public static function setLocalDomain(DceDomain|int $localDomain): void
    {
        self::$localDomain = $localDomain;
    }

    public static function getLocalIdentifier(): ?int
    {
        return self::$localIdentifier;
    }

    public static function setLocalIdentifier(?int $localIdentifier): void
    {
        self::$localIdentifier = $localIdentifier;
    }

    /**
     * @return Nic|int<0, 281474976710655>|non-empty-string|null
     */
    public static function getNode(): Nic|int|string|null
    {
        return self::$node;
    }

    /**
     * @param Nic|int<0, 281474976710655>|non-empty-string|null $node
     */
    public static function setNode(Nic|int|string|null $node): void
    {
        self::$node = $node;
    }

    public static function getClockSeq(): ?int
    {
        return self::$clockSeq;
    }

    public static function setClockSeq(?int $clockSeq): void
    {
        self::$clockSeq = $clockSeq;
    }
}
