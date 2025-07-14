<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Postgres\Ulid;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Ulid\UlidTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class UlidTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
