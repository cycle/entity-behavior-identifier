<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\SQLServer\Ulid;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Ulid\UlidTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlserver
 */
class UlidTest extends CommonClass
{
    public const DRIVER = 'sqlserver';
}
