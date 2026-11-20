<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Snowflake;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;

/**
 * @Entity
 * @Identifier\SnowflakeGeneric(field="generic", epochOffset=1662744255000)
 * @Identifier\SnowflakeDiscord(field="discord")
 * @Identifier\SnowflakeInstagram(field="instagram")
 * @Identifier\SnowflakeMastodon(field="mastodon")
 * @Identifier\SnowflakeTwitter(field="twitter")
 */
#[Entity]
#[Identifier\SnowflakeGeneric(field: 'generic', epochOffset: 1662744255000)]
#[Identifier\SnowflakeDiscord(field: 'discord')]
#[Identifier\SnowflakeInstagram(field: 'instagram')]
#[Identifier\SnowflakeMastodon(field: 'mastodon')]
#[Identifier\SnowflakeTwitter(field: 'twitter')]
class Comment
{
    /**
     * @Column(type="primary")
     */
    #[Column(type: 'primary')]
    public int $id;
}
