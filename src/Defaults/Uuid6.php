<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Defaults;

use Ramsey\Identifier\Service\Nic\Nic;

final class Uuid6
{
    /**
     * @var Nic|int<0, 281474976710655>|non-empty-string|null $node
     */
    private static Nic|int|string|null $node = null;

    private static ?int $clockSeq = null;

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
