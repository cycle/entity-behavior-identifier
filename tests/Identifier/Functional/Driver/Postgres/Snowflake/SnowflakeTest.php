<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Postgres\Snowflake;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Snowflake\SnowflakeTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class SnowflakeTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
