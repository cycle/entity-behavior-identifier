<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;

/**
 * @Entity
 * @Identifier\Uuid4(field="customUuid", column="custom_uuid")
 */
#[Entity]
#[Identifier\Uuid4(field: 'customUuid', column: 'custom_uuid')]
class Post
{
    /**
     * @Column(type="primary")
     */
    #[Column(type: 'primary')]
    public int $id;
}
