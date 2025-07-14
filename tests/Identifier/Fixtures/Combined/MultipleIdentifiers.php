<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Combined;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier\Ulid;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid4;
use Ramsey\Identifier\Ulid as UlidInterface;
use Ramsey\Identifier\Uuid;

/**
 * @Entity
 * @Uuid4
 * @Uuid4(field="uuidNullable", column="uuid_nullable", nullable=true)
 * @Ulid(field="ulid")
 * @Ulid(field="ulidNullable", column="ulid_nullable", nullable=true)
 */
#[Entity]
#[Uuid4]
#[Uuid4(field: 'uuidNullable', column: 'uuid_nullable', nullable: true)]
#[Ulid(field: 'ulid')]
#[Uuid4(field: 'ulidNullable', column: 'ulid_nullable', nullable: true)]
class MultipleIdentifiers
{
    /**
     * @Column(type="uuid", primary=true)
     */
    #[Column(type: 'uuid', primary: true)]
    public Uuid $uuid;

    /**
     * @Column(type="uuid", nullable=true)
     */
    #[Column(type: 'uuid')]
    public ?Uuid $uuidNullable = null;

    /**
     * @Column(type="ulid")
     */
    #[Column(type: 'ulid')]
    public UlidInterface $ulid;

    /**
     * @Column(type="ulid", nullable=true)
     */
    #[Column(type: 'ulid')]
    public ?UlidInterface $ulidNullable = null;
}
