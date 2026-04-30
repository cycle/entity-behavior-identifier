<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Uuid;

/**
 * @Entity
 * @Identifier\Uuid1
 * @Identifier\Uuid1(field="otherUuid", column="other_uuid")
 * @Identifier\Uuid7(field="uuid7")
 * @Identifier\Uuid7(field="otherUuid7", column="other_uuid7")
 */
#[Entity]
#[Identifier\Uuid1]
#[Identifier\Uuid1(field: 'otherUuid', column: 'other_uuid')]
#[Identifier\Uuid7(field: 'uuid7')]
#[Identifier\Uuid7(field: 'otherUuid7', column: 'other_uuid7')]
final class MultipleUuid
{
    /**
     * @Column(type="uuid", primary=true)
     */
    #[Column(type: 'uuid', primary: true)]
    public Uuid $uuid;

    /**
     * @Column(type="uuid", name="other_uuid")
     */
    #[Column(type: 'uuid', name: 'other_uuid')]
    public Uuid $otherUuid;

    /**
     * @Column(type="uuid")
     */
    #[Column(type: 'uuid')]
    public Uuid $uuid7;

    /**
     * @Column(type="uuid", name="other_uuid7")
     */
    #[Column(type: 'uuid', name: 'other_uuid7')]
    public Uuid $otherUuid7;
}
