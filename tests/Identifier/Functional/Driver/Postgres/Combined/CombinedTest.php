<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Postgres\Combined;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Combined\CombinedTest as CommonClass;

/**
 * @group driver
 * @group driver-postgres
 */
class CombinedTest extends CommonClass
{
    public const DRIVER = 'postgres';
}
