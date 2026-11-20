<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Snowflake;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Snowflake;

/**
 * @Entity
 * @Identifier\SnowflakeGeneric(field="generic", column="generic")]
 * @Identifier\SnowflakeDiscord(field="discord", column="discord")]
 * @Identifier\SnowflakeInstagram(field="instagram", column="instagram")]
 * @Identifier\SnowflakeMastodon (field="mastodon", column="mastodon")]
 * @Identifier\SnowflakeTwitter (field="twitter", column="twitter")]
 */
#[Entity]
#[Identifier\SnowflakeGeneric(field: 'generic', column: 'generic')]
#[Identifier\SnowflakeDiscord(field: 'discord', column: 'discord')]
#[Identifier\SnowflakeInstagram(field: 'instagram', column: 'instagram')]
#[Identifier\SnowflakeMastodon(field: 'mastodon', column: 'mastodon')]
#[Identifier\SnowflakeTwitter(field: 'twitter', column: 'twitter')]
class AllSnowflake
{
    /**
     * @Column(type="primary")
     */
    #[Column(type: 'primary')]
    public int|string $id;

    /**
     * @Column(type="snowflake", nullable=true)
     */
    #[Column(type: 'snowflake', nullable: true)]
    public ?Snowflake $generic = null;

    /**
     * @Column(type="snowflake", nullable=true)
     */
    #[Column(type: 'snowflake', nullable: true)]
    public ?Snowflake $discord = null;

    /**
     * @Column(type="snowflake", nullable=true)
     */
    #[Column(type: 'snowflake', nullable: true)]
    public ?Snowflake $instagram = null;

    /**
     * @Column(type="snowflake", nullable=true)
     */
    #[Column(type: 'snowflake', nullable: true)]
    public ?Snowflake $mastodon = null;

    /**
     * @Column(type="snowflake", nullable=true)
     */
    #[Column(type: 'snowflake', nullable: true)]
    public ?Snowflake $twitter = null;
}
