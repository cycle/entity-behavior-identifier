<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Ulid;

/**
 * @Entity
 * @Identifier\Ulid
 * @Identifier\Ulid(field="fooUlid", column="foo_ulid")
 * @Identifier\Ulid(field="bar")
 */
#[Entity]
#[Identifier\Ulid]
#[Identifier\Ulid(field: 'fooUlid', column: 'foo_ulid')]
#[Identifier\Ulid(field: 'bar')]
final class MultipleUlid
{
    /**
     * @Column(type="ulid", primary=true)
     */
    #[Column(type: 'ulid', primary: true)]
    public Ulid $ulid;

    /**
     * @Column(type="ulid", name="foo_ulid")
     */
    #[Column(type: 'ulid', name: 'foo_ulid')]
    public Ulid $fooUlid;

    /**
     * @Column(type="ulid")
     */
    #[Column(type: 'ulid')]
    public Ulid $bar;
}
