<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier\Ulid;
use Ramsey\Identifier\Ulid as UlidInterface;

/**
 * @Entity
 * @Ulid
 * @Ulid(field="notDefinedUlid", column="not_defined_ulid", nullable=true)
 */
#[Entity]
#[Ulid]
#[Ulid(field: 'notDefinedUlid', column: 'not_defined_ulid', nullable: true)]
final class NullableUlid
{
    /**
     * @Column(type="ulid", primary=true)
     */
    #[Column(type: 'ulid', primary: true)]
    public UlidInterface $ulid;
}
