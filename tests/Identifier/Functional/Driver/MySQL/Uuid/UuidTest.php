<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\MySQL\Uuid;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Uuid\UuidTest as CommonClass;

/**
 * @group driver
 * @group driver-mysql
 */
class UuidTest extends CommonClass
{
    public const DRIVER = 'mysql';
}
