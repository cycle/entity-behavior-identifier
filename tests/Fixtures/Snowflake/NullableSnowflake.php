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
 * @Identifier\SnowflakeGeneric(field="notDefinedSnowflake", column="not_defined_snowflake", nullable=true)
 */
#[Entity]
#[Identifier\SnowflakeGeneric]
#[Identifier\SnowflakeGeneric(field: 'notDefinedSnowflake', column: 'not_defined_snowflake', nullable: true)]
final class NullableSnowflake
{
    /**
     * @Column(type="snowflake", primary=true)
     */
    #[Column(type: 'snowflake', primary: true)]
    public Snowflake $snowflake;
}
