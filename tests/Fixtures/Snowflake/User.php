<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Snowflake;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Snowflake;

/**
 * @Entity
 * @Identifier\SnowflakeGeneric
 */
#[Entity]
#[Identifier\SnowflakeGeneric]
class User
{
    /**
     * @Column(type="snowflake", primary=true)
     */
    #[Column(type: 'snowflake', primary: true)]
    public Snowflake $snowflake;
}
