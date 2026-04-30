<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\SQLite\Combined;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Combined\CombinedTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlite
 */
class CombinedTest extends CommonClass
{
    public const DRIVER = 'sqlite';
}
