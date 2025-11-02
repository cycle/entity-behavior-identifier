<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Uuid;

use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid\AllUuid;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Traits\TableTrait;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid1 as Uuid1Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid2 as Uuid2Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid3 as Uuid3Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid4 as Uuid4Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid5 as Uuid5Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid6 as Uuid6Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid7 as Uuid7Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid1;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid2;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid3;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid4;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid5;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid6;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid7;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Ramsey\Identifier\Uuid\DceDomain;
use Ramsey\Identifier\Uuid\NamespaceId;
use Ramsey\Identifier\Uuid\UuidFactory;
use Ramsey\Identifier\Uuid\UuidV1;
use Ramsey\Identifier\Uuid\UuidV2;
use Ramsey\Identifier\Uuid\UuidV3;
use Ramsey\Identifier\Uuid\UuidV4;
use Ramsey\Identifier\Uuid\UuidV5;
use Ramsey\Identifier\Uuid\UuidV6;
use Ramsey\Identifier\Uuid\UuidV7;

abstract class ListenerTest extends BaseTest
{
    use TableTrait;

    private UuidFactory $factory;

    public function testNullable(): void
    {
        $this->withListeners([
            Uuid1Listener::class,
            [
                'field' => 'uuid1',
                'nullable' => true,
            ],
            Uuid2Listener::class,
            [
                'field' => 'uuid2',
                'nullable' => true,
            ],
            Uuid3Listener::class,
            [
                'field' => 'uuid3',
                'nullable' => true,
            ],
            Uuid4Listener::class,
            [
                'field' => 'uuid4',
                'nullable' => true,
            ],
            Uuid5Listener::class,
            [
                'field' => 'uuid5',
                'nullable' => true,
            ],
            Uuid6Listener::class,
            [
                'field' => 'uuid6',
                'nullable' => true,
            ],
            Uuid7Listener::class,
            [
                'field' => 'uuid7',
                'nullable' => true,
            ],
        ]);

        $entity = new AllUuid();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllUuid::class);
        $data = $select->fetchOne();

        $this->assertNull($data->uuid1);
        $this->assertNull($data->uuid2);
        $this->assertNull($data->uuid3);
        $this->assertNull($data->uuid4);
        $this->assertNull($data->uuid5);
        $this->assertNull($data->uuid6);
        $this->assertNull($data->uuid7);
    }

    public function testAssignManually(): void
    {
        $this->withListeners();

        $entity = new AllUuid();
        $entity->uuid1 = $this->factory->v1();
        $entity->uuid2 = $this->factory->v2();
        $entity->uuid3 = $this->factory->v3(NamespaceId::Url, 'https://cycle-orm.dev');
        $entity->uuid4 = $this->factory->v4();
        $entity->uuid5 = $this->factory->v5(NamespaceId::Url, 'https://cycle-orm.dev');
        $entity->uuid6 = $this->factory->v6();
        $entity->uuid7 = $this->factory->v7();

        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllUuid::class);
        $data = $select->fetchOne();

        $this->assertSame($entity->uuid1->toString(), $data->uuid1->toString());
        $this->assertSame($entity->uuid2->toString(), $data->uuid2->toString());
        $this->assertSame($entity->uuid3->toString(), $data->uuid3->toString());
        $this->assertSame($entity->uuid4->toString(), $data->uuid4->toString());
        $this->assertSame($entity->uuid5->toString(), $data->uuid5->toString());
        $this->assertSame($entity->uuid6->toString(), $data->uuid6->toString());
        $this->assertSame($entity->uuid7->toString(), $data->uuid7->toString());
    }

    public function testUuid1(): void
    {
        $this->withListeners([
            Uuid1Listener::class,
            [
                'field' => 'uuid1',
                'node' => '00000fffffff',
                'clockSeq' => 0xffff,
            ],
        ]);

        $entity = new AllUuid();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllUuid::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UuidV1::class, $data->uuid1);
        $this->assertSame(1, $data->uuid1->getVersion()->value);
        $this->assertIsString($data->uuid1->toString());
    }

    public function testUuid2(): void
    {
        $this->withListeners([
            Uuid2Listener::class,
            [
                'field' => 'uuid2',
                'localDomain' => DceDomain::Person,
                'localIdentifier' => 12345678,
            ],
        ]);

        $entity = new AllUuid();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllUuid::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UuidV2::class, $data->uuid2);
        $this->assertSame(2, $data->uuid2->getVersion()->value);
        $this->assertIsString($data->uuid2->toString());
    }

    public function testUuid3(): void
    {
        $this->withListeners([
            Uuid3Listener::class,
            [
                'field' => 'uuid3',
                'namespace' => NamespaceId::Url,
                'name' => 'https://example.com/foo',
            ],
        ]);

        $entity = new AllUuid();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllUuid::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UuidV3::class, $data->uuid3);
        $this->assertSame(3, $data->uuid3->getVersion()->value);
        $this->assertIsString($data->uuid3->toString());
    }

    public function testUuid3ThrowsExceptionWhenNamespaceNotSpecified(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->withListeners([
            Uuid3Listener::class,
            [
                'field' => 'uuid3',
                'name' => 'https://example.com/foo',
            ],
        ]);

        $entity = new AllUuid();
        $this->save($entity);
    }

    public function testUuid3ThrowsExceptionWhenNameNotSpecified(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->withListeners([
            Uuid3Listener::class,
            [
                'field' => 'uuid3',
                'namespace' => NamespaceId::Url,
            ],
        ]);

        $entity = new AllUuid();
        $this->save($entity);
    }

    public function testUuid4(): void
    {
        $this->withListeners([
            Uuid4Listener::class,
            [
                'field' => 'uuid4',
            ],
        ]);

        $entity = new AllUuid();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllUuid::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UuidV4::class, $data->uuid4);
        $this->assertSame(4, $data->uuid4->getVersion()->value);
        $this->assertIsString($data->uuid4->toString());
    }

    public function testUuid5(): void
    {
        $this->withListeners([
            Uuid5Listener::class,
            [
                'field' => 'uuid5',
                'namespace' => NamespaceId::Url,
                'name' => 'https://example.com/foo',
            ],
        ]);

        $entity = new AllUuid();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllUuid::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UuidV5::class, $data->uuid5);
        $this->assertSame(5, $data->uuid5->getVersion()->value);
        $this->assertIsString($data->uuid5->toString());
    }

    public function testUuid5ThrowsExceptionWhenNamespaceNotSpecified(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->withListeners([
            Uuid5Listener::class,
            [
                'field' => 'uuid5',
                'name' => 'https://example.com/foo',
            ],
        ]);

        $entity = new AllUuid();
        $this->save($entity);
    }

    public function testUuid5ThrowsExceptionWhenNameNotSpecified(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->withListeners([
            Uuid5Listener::class,
            [
                'field' => 'uuid5',
                'namespace' => NamespaceId::Url,
            ],
        ]);

        $entity = new AllUuid();
        $this->save($entity);
    }

    public function testUuid6(): void
    {
        $this->withListeners([
            Uuid6Listener::class,
            [
                'field' => 'uuid6',
                'node' => '00000fffffff',
                'clockSeq' => 0xffff,
            ],
        ]);

        $entity = new AllUuid();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllUuid::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UuidV6::class, $data->uuid6);
        $this->assertSame(6, $data->uuid6->getVersion()->value);
        $this->assertIsString($data->uuid6->toString());
    }

    public function testUuid7(): void
    {
        $this->withListeners([
            Uuid7Listener::class,
            [
                'field' => 'uuid7',
            ],
        ]);

        $entity = new AllUuid();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllUuid::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UuidV7::class, $data->uuid7);
        $this->assertSame(7, $data->uuid7->getVersion()->value);
        $this->assertIsString($data->uuid7->toString());
    }

    public function withListeners(array|string|null $listeners = null): void
    {
        $this->withSchema(new Schema([
            AllUuid::class => [
                SchemaInterface::ROLE => 'all_uuid',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'all_uuids',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::COLUMNS => ['id', 'uuid1', 'uuid2', 'uuid3', 'uuid4', 'uuid5', 'uuid6', 'uuid7'],
                SchemaInterface::LISTENERS => $listeners ? [$listeners] : [],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
                SchemaInterface::TYPECAST => [
                    'uuid1' => [Uuid1::class, 'create'],
                    'uuid2' => [Uuid2::class, 'create'],
                    'uuid3' => [Uuid3::class, 'create'],
                    'uuid4' => [Uuid4::class, 'create'],
                    'uuid5' => [Uuid5::class, 'create'],
                    'uuid6' => [Uuid6::class, 'create'],
                    'uuid7' => [Uuid7::class, 'create'],
                ],
            ],
        ]));
    }

    #[\Override]
    public function setUp(): void
    {
        parent::setUp();

        Uuid3Listener::setDefaults(null, null);
        Uuid5Listener::setDefaults(null, null);

        $this->factory = new UuidFactory();

        $this->makeTable(
            'all_uuids',
            [
                'id' => 'integer',
                'uuid1' => 'uuid,nullable',
                'uuid2' => 'uuid,nullable',
                'uuid3' => 'uuid,nullable',
                'uuid4' => 'uuid,nullable',
                'uuid5' => 'uuid,nullable',
                'uuid6' => 'uuid,nullable',
                'uuid7' => 'uuid,nullable',
            ],
        );
    }
}
