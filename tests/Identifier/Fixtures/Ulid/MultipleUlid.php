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
 * @Ulid(field="fooUlid", column="foo_ulid")
 * @Ulid(field="bar")
 */
#[Entity]
#[Ulid]
#[Ulid(field: 'fooUlid', column: 'foo_ulid')]
#[Ulid(field: 'bar')]
final class MultipleUlid
{
    /**
     * @Column(type="ulid", primary=true)
     */
    #[Column(type: 'ulid', primary: true)]
    public UlidInterface $ulid;

    /**
     * @Column(type="ulid", name="foo_ulid")
     */
    #[Column(type: 'ulid', name: 'foo_ulid')]
    public UlidInterface $fooUlid;

    /**
     * @Column(type="ulid")
     */
    #[Column(type: 'ulid')]
    public UlidInterface $bar;
}
