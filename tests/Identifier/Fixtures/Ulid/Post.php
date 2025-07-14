<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier\Ulid;

/**
 * @Entity
 * @Ulid(field="customUlid", column="custom_ulid")
 */
#[Entity]
#[Ulid(field: 'customUlid', column: 'custom_ulid')]
class Post
{
    /**
     * @Column(type="primary")
     */
    #[Column(type: 'primary')]
    public int $id;
}
