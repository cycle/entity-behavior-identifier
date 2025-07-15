<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier\Ulid;
use Ramsey\Identifier\Ulid as UlidInterface;

/**
 * @Entity
 * @Ulid
 */
#[Entity]
#[Ulid]
class User
{
    /**
     * @Column(type="ulid", primary=true)
     */
    #[Column(type: 'ulid', primary: true)]
    public UlidInterface $ulid;
}
