<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Snowflake;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;

/**
 * @Entity
 * @Identifier\SnowflakeGeneric(field="customSnowflake", column="custom_snowflake")
 */
#[Entity]
#[Identifier\SnowflakeGeneric(field: 'customSnowflake', column: 'custom_snowflake')]
class Post
{
    /**
     * @Column(type="primary")
     */
    #[Column(type: 'primary')]
    public int $id;
}
