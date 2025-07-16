<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\SQLServer\Snowflake;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Snowflake\ListenerTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class ListenerTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
