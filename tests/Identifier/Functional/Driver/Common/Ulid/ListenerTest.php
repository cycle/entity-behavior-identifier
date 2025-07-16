<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Ulid;

use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid\User;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Traits\TableTrait;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Ulid as UlidListener;
use Cycle\ORM\Entity\Behavior\Identifier\Ulid;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Ramsey\Identifier\Ulid as UlidInterface;
use Ramsey\Identifier\Ulid\UlidFactory;

abstract class ListenerTest extends BaseTest
{
    use TableTrait;

    public function testAssignManually(): void
    {
        $this->withListeners(UlidListener::class);

        $user = new User();
        $user->ulid = (new UlidFactory())->create();
        $bytes = $user->ulid->toBytes();

        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertSame($bytes, $data->ulid->toBytes());
    }

    public function testWithNullableTrue(): void
    {
        $this->withListeners([
            UlidListener::class,
            [
                'field' => 'foo_ulid',
                'nullable' => true,
            ],
        ]);

        $user = new User();
        $user->ulid = (new UlidFactory())->create();

        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchData();

        $this->assertNull($data[0]['foo_ulid']);
    }

    public function testUlid(): void
    {
        $this->withListeners(UlidListener::class);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UlidInterface::class, $data->ulid);
        $this->assertIsString($data->ulid->toBytes());
        $this->assertIsString($data->ulid->toString());
    }

    public function withListeners(array|string $listeners): void
    {
        $this->withSchema(new Schema([
            User::class => [
                SchemaInterface::ROLE => 'user',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'users',
                SchemaInterface::PRIMARY_KEY => 'ulid',
                SchemaInterface::COLUMNS => ['ulid', 'foo_ulid'],
                SchemaInterface::LISTENERS => [$listeners],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
                SchemaInterface::TYPECAST => [
                    'ulid' => [Ulid::class, 'fromString'],
                    'foo_ulid' => [Ulid::class, 'fromString'],
                ],
            ],
        ]));
    }

    #[\Override]
    public function setUp(): void
    {
        parent::setUp();

        $this->makeTable(
            'users',
            [
                'ulid' => 'string',
                'foo_ulid' => 'string,nullable',
            ],
        );
    }
}
