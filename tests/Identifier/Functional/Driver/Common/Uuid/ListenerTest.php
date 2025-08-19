<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Uuid;

use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid\User;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Traits\TableTrait;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid1 as Uuid1Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid2 as Uuid2Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid3 as Uuid3Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid4 as Uuid4Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid5 as Uuid5Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid6 as Uuid6Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid7 as Uuid7Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Ramsey\Identifier\Uuid\DceDomain;
use Ramsey\Identifier\Uuid\NamespaceId;
use Ramsey\Identifier\Uuid\UntypedUuid;
use Ramsey\Identifier\Uuid\UuidFactory;

abstract class ListenerTest extends BaseTest
{
    use TableTrait;

    public static function nullableTrueDataProvider(): \Traversable
    {
        yield [
            [
                Uuid1Listener::class,
                [
                    'nullable' => true,
                    'field' => 'optional_uuid',
                    'node' => '00000fffffff',
                    'clockSeq' => 0xffff,
                ],
            ],
        ];
        yield [
            [
                Uuid2Listener::class,
                [
                    'nullable' => true,
                    'field' => 'optional_uuid',
                    'localDomain' => DceDomain::Person,
                    'localIdentifier' => 12345678,
                ],
            ],
        ];
        yield [
            [
                Uuid3Listener::class,
                [
                    'nullable' => true,
                    'field' => 'optional_uuid',
                    'namespace' => NamespaceId::Url,
                    'name' => 'https://example.com/foo',
                ],
            ],
        ];
        yield [
            [
                Uuid4Listener::class,
                [
                    'nullable' => true,
                    'field' => 'optional_uuid',
                ],
            ],
        ];
        yield [
            [
                Uuid5Listener::class,
                [
                    'nullable' => true,
                    'field' => 'optional_uuid',
                    'namespace' => NamespaceId::Url,
                    'name' => 'https://example.com/foo',
                ],
            ],
        ];
        yield [
            [
                Uuid6Listener::class,
                [
                    'nullable' => true,
                    'field' => 'optional_uuid',
                    'node' => '00000fffffff',
                    'clockSeq' => 0x1669,
                ],
            ],
        ];
        yield [
            [
                Uuid7Listener::class,
                [
                    'nullable' => true,
                    'field' => 'optional_uuid',
                ],
            ],
        ];
    }

    public function testAssignManually(): void
    {
        $this->withListeners([
            Uuid4Listener::class,
            [
                'field' => 'uuid',
            ],
        ]);

        $user = new User();
        $user->uuid = (new UuidFactory())->v4();
        $bytes = $user->uuid->toBytes();

        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertSame($bytes, $data->uuid->toBytes());
    }

    /**
     * @dataProvider nullableTrueDataProvider
     */
    public function testWithNullableTrue(array $listener): void
    {
        $this->withListeners($listener);

        $user = new User();
        $user->uuid = (new UuidFactory())->v4();

        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchData();

        $this->assertNull($data[0]['optional_uuid']);
    }

    public function testUuid1(): void
    {
        $this->withListeners([
            Uuid1Listener::class,
            [
                'field' => 'uuid',
                'node' => '00000fffffff',
                'clockSeq' => 0xffff,
            ],
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UntypedUuid::class, $data->uuid);
        $this->assertSame(1, $data->uuid->getVersion()->value);
        $this->assertIsString($data->uuid->toString());
    }

    public function testUuid2(): void
    {
        $this->withListeners([
            Uuid2Listener::class,
            [
                'field' => 'uuid',
                'localDomain' => DceDomain::Person,
                'localIdentifier' => 12345678,
            ],
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UntypedUuid::class, $data->uuid);
        $this->assertSame(2, $data->uuid->getVersion()->value);
        $this->assertIsString($data->uuid->toString());
    }

    public function testUuid3(): void
    {
        $this->withListeners([
            Uuid3Listener::class,
            [
                'field' => 'uuid',
                'namespace' => NamespaceId::Url,
                'name' => 'https://example.com/foo',
            ],
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UntypedUuid::class, $data->uuid);
        $this->assertSame(3, $data->uuid->getVersion()->value);
        $this->assertIsString($data->uuid->toString());
    }

    public function testUuid4(): void
    {
        $this->withListeners([
            Uuid4Listener::class,
            [
                'field' => 'uuid',
            ],
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UntypedUuid::class, $data->uuid);
        $this->assertSame(4, $data->uuid->getVersion()->value);
        $this->assertIsString($data->uuid->toString());
    }

    public function testUuid5(): void
    {
        $this->withListeners([
            Uuid5Listener::class,
            [
                'field' => 'uuid',
                'namespace' => NamespaceId::Url,
                'name' => 'https://example.com/foo',
            ],
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UntypedUuid::class, $data->uuid);
        $this->assertSame(5, $data->uuid->getVersion()->value);
        $this->assertIsString($data->uuid->toString());
    }

    public function testUuid6(): void
    {
        $this->withListeners([
            Uuid6Listener::class,
            [
                'field' => 'uuid',
                'node' => '00000fffffff',
                'clockSeq' => 0x1669,
            ],
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UntypedUuid::class, $data->uuid);
        $this->assertSame(6, $data->uuid->getVersion()->value);
        $this->assertIsString($data->uuid->toString());
    }

    public function testUuid7(): void
    {
        $this->withListeners([
            Uuid7Listener::class,
            [
                'field' => 'uuid',
            ],
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UntypedUuid::class, $data->uuid);
        $this->assertSame(7, $data->uuid->getVersion()->value);
        $this->assertIsString($data->uuid->toString());
    }

    public function withListeners(array|string $listeners): void
    {
        $this->withSchema(new Schema([
            User::class => [
                SchemaInterface::ROLE => 'user',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'users',
                SchemaInterface::PRIMARY_KEY => 'uuid',
                SchemaInterface::COLUMNS => ['uuid', 'optional_uuid'],
                SchemaInterface::LISTENERS => [$listeners],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
                SchemaInterface::TYPECAST => [
                    'uuid' => [Uuid::class, 'fromString'],
                    'optional_uuid' => [Uuid::class, 'fromString'],
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
                'uuid' => 'string',
                'optional_uuid' => 'string,nullable',
            ],
        );
    }
}
