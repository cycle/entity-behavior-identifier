<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Combined;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Snowflake;
use Ramsey\Identifier\Ulid;
use Ramsey\Identifier\Uuid;

/**
 * @Entity
 * @Identifier\Uuid4
 * @Identifier\Uuid4(field="uuidNullable", column="uuid_nullable", nullable=true)
 * @Identifier\Ulid(field="ulid")
 * @Identifier\Ulid(field="ulidNullable", column="ulid_nullable", nullable=true)
 * @Identifier\SnowflakeGeneric(field="snowflake")
 * @Identifier\SnowflakeGeneric(field="snowflakeNullable", column="snowflake_nullable", nullable=true)
 */
#[Entity]
#[Identifier\Uuid4]
#[Identifier\Uuid4(field: 'uuidNullable', column: 'uuid_nullable', nullable: true)]
#[Identifier\Ulid(field: 'ulid')]
#[Identifier\Ulid(field: 'ulidNullable', column: 'ulid_nullable', nullable: true)]
#[Identifier\SnowflakeGeneric(field: 'snowflake')]
#[Identifier\SnowflakeGeneric(field: 'snowflakeNullable', column: 'snowflake_nullable', nullable: true)]
class MultipleIdentifiers
{
    /**
     * @Column(type="uuid", primary=true)
     */
    #[Column(type: 'uuid', primary: true)]
    public Uuid $uuid;

    /**
     * @Column(type="uuid", nullable=true)
     */
    #[Column(type: 'uuid')]
    public ?Uuid $uuidNullable = null;

    /**
     * @Column(type="ulid")
     */
    #[Column(type: 'ulid')]
    public Ulid $ulid;

    /**
     * @Column(type="ulid", nullable=true)
     */
    #[Column(type: 'ulid')]
    public ?Ulid $ulidNullable = null;

    /**
     * @Column(type="snowflake")
     */
    #[Column(type: 'snowflake')]
    public Snowflake $snowflake;

    /**
     * @Column(type="snowflake", nullable=true)
     */
    #[Column(type: 'snowflake')]
    public ?Snowflake $snowflakeNullable = null;
}
