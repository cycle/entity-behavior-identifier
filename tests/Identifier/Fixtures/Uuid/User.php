<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid1;
use Ramsey\Identifier\Uuid;

/**
 * @Entity
 * @Uuid1
 */
#[Entity]
#[Uuid1]
class User
{
    /**
     * @Column(type="uuid", primary=true)
     */
    #[Column(type: 'uuid', primary: true)]
    public Uuid $uuid;
}
