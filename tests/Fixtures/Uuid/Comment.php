<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;

/**
 * @Entity
 * @Identifier\Uuid2(field="uuid2")
 * @Identifier\Uuid3(field="uuid3", namespace="6ba7b811-9dad-11d1-80b4-00c04fd430c8", name="foo")
 * @Identifier\Uuid5(field="uuid5", namespace="6ba7b811-9dad-11d1-80b4-00c04fd430c8", name="foo")
 * @Identifier\Uuid6(field="uuid6")
 */
#[Entity]
#[Identifier\Uuid2(field: 'uuid2')]
#[Identifier\Uuid3(field: 'uuid3', namespace: '6ba7b811-9dad-11d1-80b4-00c04fd430c8', name: 'foo')]
#[Identifier\Uuid5(field: 'uuid5', namespace: '6ba7b811-9dad-11d1-80b4-00c04fd430c8', name: 'foo')]
#[Identifier\Uuid6(field: 'uuid6')]
class Comment
{
    /**
     * @Column(type="primary")
     */
    #[Column(type: 'primary')]
    public int $id;
}
