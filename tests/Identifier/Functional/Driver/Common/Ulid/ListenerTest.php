<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Ulid;

use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid\AllUlid;
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

    public function testNullable(): void
    {
        $this->withListeners([
            UlidListener::class,
            [
                'field' => 'ulid',
                'nullable' => true,
            ],
        ]);

        $entity = new AllUlid();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllUlid::class);
        $data = $select->fetchOne();

        $this->assertNull($data->ulid);
    }

    public function testAssignManually(): void
    {
        $this->withListeners();

        $entity = new AllUlid();
        $entity->ulid = (new UlidFactory())->create();

        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllUlid::class);
        $data = $select->fetchOne();

        $this->assertSame($entity->ulid->toString(), $data->ulid->toString());
    }

    public function testUlid(): void
    {
        $this->withListeners([
            UlidListener::class,
            [
                'field' => 'ulid',
            ],
        ]);

        $entity = new AllUlid();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllUlid::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(UlidInterface::class, $data->ulid);
        $this->assertIsString($data->ulid->toString());
        $this->assertSame(26, \strlen($data->ulid->toString()));
    }

    public function withListeners(array|string|null $listeners = null): void
    {
        $this->withSchema(new Schema([
            AllUlid::class => [
                SchemaInterface::ROLE => 'all_ulid',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'all_ulids',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::COLUMNS => ['id', 'ulid'],
                SchemaInterface::LISTENERS => $listeners ? [$listeners] : [],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
                SchemaInterface::TYPECAST => [
                    'ulid' => [Ulid::class, 'create'],
                ],
            ],
        ]));
    }

    #[\Override]
    public function setUp(): void
    {
        parent::setUp();

        $this->makeTable(
            'all_ulids',
            [
                'id' => 'integer',
                'ulid' => 'ulid,nullable',
            ],
        );
    }
}
