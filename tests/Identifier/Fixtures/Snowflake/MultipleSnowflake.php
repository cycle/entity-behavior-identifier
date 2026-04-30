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
 * @Identifier\SnowflakeDiscord(field="discord")
 * @Identifier\SnowflakeInstagram(field="instagram")
 * @Identifier\SnowflakeMastodon(field="mastodon")
 * @Identifier\SnowflakeTwitter(field="twitter")
 */
#[Entity]
#[Identifier\SnowflakeGeneric]
#[Identifier\SnowflakeDiscord(field: 'discord')]
#[Identifier\SnowflakeInstagram(field: 'instagram')]
#[Identifier\SnowflakeMastodon(field: 'mastodon')]
#[Identifier\SnowflakeTwitter(field: 'twitter')]
class MultipleSnowflake
{
    /**
     * @Column(type="snowflake", primary=true)
     */
    #[Column(type: 'snowflake', primary: true)]
    public Snowflake $snowflake;

    /**
     * @Column(type="snowflake")
     */
    #[Column(type: 'snowflake')]
    public Snowflake $discord;

    /**
     * @Column(type="snowflake")
     */
    #[Column(type: 'snowflake')]
    public Snowflake $instagram;

    /**
     * @Column(type="snowflake")
     */
    #[Column(type: 'snowflake')]
    public Snowflake $mastodon;

    /**
     * @Column(type="snowflake")
     */
    #[Column(type: 'snowflake')]
    public Snowflake $twitter;
}
