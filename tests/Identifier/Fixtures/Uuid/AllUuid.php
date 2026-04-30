<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Uuid;

/**
 * @Entity
 * @Identifier\Uuid1(field="uuid1", column="uuid1")
 * @Identifier\Uuid2(field="uuid2", column="uuid2")
 * @Identifier\Uuid3(field="uuid3", column="uuid3", namespace="Url", name="https://cycle-orm.dev")
 * @Identifier\Uuid4(field="uuid4", column="uuid4")
 * @Identifier\Uuid5(field="uuid5", column="uuid5", namespace="Url", name="https://cycle-orm.dev")
 * @Identifier\Uuid6(field="uuid6", column="uuid6")
 * @Identifier\Uuid7(field="uuid7", column="uuid7")
 */
#[Entity]
#[Identifier\Uuid1(field: 'uuid1', column: 'uuid1')]
#[Identifier\Uuid2(field: 'uuid2', column: 'uuid2')]
#[Identifier\Uuid3(field: 'uuid3', column: 'uuid3', namespace: 'Url', name: 'https://cycle-orm.dev')]
#[Identifier\Uuid4(field: 'uuid4', column: 'uuid4')]
#[Identifier\Uuid5(field: 'uuid5', column: 'uuid5', namespace: 'Url', name: 'https://cycle-orm.dev')]
#[Identifier\Uuid6(field: 'uuid6', column: 'uuid6')]
#[Identifier\Uuid7(field: 'uuid7', column: 'uuid7')]
class AllUuid
{
    /**
     * @Column(type="primary")
     */
    #[Column(type: 'primary')]
    public int|string $id;

    /**
     * @Column(type="uuid", nullable=true)
     */
    #[Column(type: 'uuid', nullable: true)]
    public ?Uuid $uuid1 = null;

    /**
     * @Column(type="uuid", nullable=true)
     */
    #[Column(type: 'uuid', nullable: true)]
    public ?Uuid $uuid2 = null;

    /**
     * @Column(type="uuid", nullable=true)
     */
    #[Column(type: 'uuid', nullable: true)]
    public ?Uuid $uuid3 = null;

    /**
     * @Column(type="uuid", nullable=true)
     */
    #[Column(type: 'uuid', nullable: true)]
    public ?Uuid $uuid4 = null;

    /**
     * @Column(type="uuid", nullable=true)
     */
    #[Column(type: 'uuid', nullable: true)]
    public ?Uuid $uuid5 = null;

    /**
     * @Column(type="uuid", nullable=true)
     */
    #[Column(type: 'uuid', nullable: true)]
    public ?Uuid $uuid6 = null;

    /**
     * @Column(type="uuid", nullable=true)
     */
    #[Column(type: 'uuid', nullable: true)]
    public ?Uuid $uuid7 = null;
}
