<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Ulid;

/**
 * @Entity
 * @Identifier\Ulid(field="ulid", column="ulid")]
 */
#[Entity]
#[Identifier\Ulid(field: 'ulid', column: 'ulid')]
class AllUlid
{
    /**
     * @Column(type="primary")
     */
    #[Column(type: 'primary')]
    public int|string $id;

    /**
     * @Column(type="ulid", nullable=true)
     */
    #[Column(type: 'ulid', nullable: true)]
    public ?Ulid $ulid = null;
}
