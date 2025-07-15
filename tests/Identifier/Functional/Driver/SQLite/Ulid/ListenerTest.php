<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\SQLite\Ulid;

// phpcs:ignore
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Ulid\ListenerTest as CommonClass;

/**
 * @group driver
 * @group driver-sqlite
 */
class ListenerTest extends CommonClass
{
    public const DRIVER = 'sqlite';
}
