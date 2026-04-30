<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\MySQL\Snowflake;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Snowflake\ListenerTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class ListenerTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
