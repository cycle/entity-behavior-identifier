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
 * @Identifier\Uuid1(field="notDefinedUuid", column="not_defined_uuid", nullable=true)
 */
#[Entity]
#[Identifier\Uuid1]
#[Identifier\Uuid1(field: 'notDefinedUuid', column: 'not_defined_uuid', nullable: true)]
final class NullableUuid
{
    /**
     * @Column(type="uuid", primary=true)
     */
    #[Column(type: 'uuid', primary: true)]
    public Uuid $uuid;
}
