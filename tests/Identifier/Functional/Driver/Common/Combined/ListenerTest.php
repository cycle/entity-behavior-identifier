<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Combined;

use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Combined\MultipleIdentifiers;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Traits\TableTrait;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Ulid as UlidListener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid4 as Uuid4Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Ulid;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Ramsey\Identifier\Ulid as UlidInterface;
use Ramsey\Identifier\Ulid\UlidFactory;
use Ramsey\Identifier\Uuid\UntypedUuid;
use Ramsey\Identifier\Uuid\UuidFactory;

abstract class ListenerTest extends BaseTest
{
    use TableTrait;

    public function testAssignManually(): void
    {
        $this->withListeners([
            Uuid4Listener::class,
            UlidListener::class,
        ]);

        $identifiers = new MultipleIdentifiers();
        $identifiers->uuid = (new UuidFactory())->v4();
        $identifiers->ulid = (new UlidFactory())->create();
        $uuidBytes = $identifiers->uuid->toBytes();
        $ulidBytes = $identifiers->ulid->toBytes();

        $this->save($identifiers);

        $select = new Select($this->orm->with(heap: new Heap()), MultipleIdentifiers::class);
        $data = $select->fetchOne();

        $this->assertSame($uuidBytes, $data->uuid->toBytes());
        $this->assertSame($ulidBytes, $data->ulid->toBytes());
    }

    public function testWithNullableTrue(): void
    {
        $this->withListeners([
            Uuid4Listener::class,
            UlidListener::class,
        ]);

        $identifiers = new MultipleIdentifiers();
        $identifiers->uuid = (new UuidFactory())->v4();
        $identifiers->ulid = (new UlidFactory())->create();

        $this->save($identifiers);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchData();

        $this->assertNull($data[0]['uuid_nullable']);
        $this->assertNull($data[0]['ulid_nullable']);
    }

    public function testCombined(): void
    {
        $this->withListeners([
            Uuid4Listener::class,
            UlidListener::class,
        ]);

        $identifiers = new MultipleIdentifiers();
        $this->save($identifiers);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UntypedUuid::class, $data->uuid);
        $this->assertInstanceOf(UlidInterface::class, $data->ulid);
        $this->assertNull($data->uuidNullable);
        $this->assertNull($data->ulidNullable);
        $this->assertIsString($data->uuid->toBytes());
        $this->assertIsString($data->uuid->toString());
        $this->assertIsString($data->ulid->toBytes());
        $this->assertIsString($data->ulid->toString());
    }

    public function testComparison(): void
    {
        $this->withListeners([
            Uuid4Listener::class,
            UlidListener::class,
        ]);

        $expectedDate = '2025-06-17 03:24:36.160 +00:00';

        $identifiers = new MultipleIdentifiers();
        $identifiers->uuid = (new UuidFactory())->createFromString('01977bea-d1c0-7154-87bb-6550974155c2');
        $identifiers->ulid = (new UlidFactory())->createFromString('01JXXYNME0E5A8FEV5A2BM2NE2');

        $this->save($identifiers);

        $select = new Select($this->orm->with(heap: new Heap()), MultipleIdentifiers::class);
        $data = $select->fetchOne();

        $this->assertSame($expectedDate, $data->uuid->getDateTime()->format('Y-m-d H:i:s.v P'));
        $this->assertSame($expectedDate, $data->ulid->getDateTime()->format('Y-m-d H:i:s.v P'));
        $this->assertTrue($data->uuid->equals($data->ulid));
    }

    public function withListeners(array|string $listeners): void
    {
        $this->withSchema(new Schema([
            MultipleIdentifiers::class => [
                SchemaInterface::ROLE => 'multiple_identifier',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'multiple_identifiers',
                SchemaInterface::PRIMARY_KEY => 'ulid',
                SchemaInterface::COLUMNS => ['uuid', 'uuid_nullable', 'ulid', 'ulid_nullable'],
                SchemaInterface::LISTENERS => [$listeners],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
                SchemaInterface::TYPECAST => [
                    'uuid' => [Uuid::class, 'fromString'],
                    'uuid_nullable' => [Uuid::class, 'fromString'],
                    'ulid' => [Ulid::class, 'fromString'],
                    'ulid_nullable' => [Ulid::class, 'fromString'],
                ],
            ],
        ]));
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->makeTable(
            'multiple_identifiers',
            [
                'uuid' => 'string',
                'uuid_nullable' => 'string,nullable',
                'ulid' => 'string',
                'ulid_nullable' => 'string,nullable',
            ],
        );
    }
}
