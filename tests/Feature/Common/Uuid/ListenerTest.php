<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Feature\Common\Uuid;

use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid1 as Uuid1Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid2 as Uuid2Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid3 as Uuid3Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid4 as Uuid4Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid5 as Uuid5Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid6 as Uuid6Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid7 as Uuid7Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid\AllUuid;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Support\IdentifierTestCase;
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
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Expect;
use Testo\Lifecycle\AfterTest;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class ListenerTest extends IdentifierTestCase
{
    private UuidFactory $factory;

    public static function uuidGenerationDataProvider(): array
    {
        return [
            'uuid1' => [
                'listeners' => [
                    Uuid1Listener::class,
                    [
                        'field' => 'uuid1',
                        'node' => '00000fffffff',
                        'clockSeq' => 0xffff,
                    ],
                ],
                'field' => 'uuid1',
                'expectedClass' => UuidV1::class,
                'expectedVersion' => 1,
            ],
            'uuid2' => [
                'listeners' => [
                    Uuid2Listener::class,
                    [
                        'field' => 'uuid2',
                        'localDomain' => DceDomain::Person,
                        'localIdentifier' => 12345678,
                        'node' => '3c1239b4f540',
                    ],
                ],
                'field' => 'uuid2',
                'expectedClass' => UuidV2::class,
                'expectedVersion' => 2,
            ],
            'uuid3' => [
                'listeners' => [
                    Uuid3Listener::class,
                    [
                        'field' => 'uuid3',
                        'namespace' => NamespaceId::Url,
                        'name' => 'https://example.com/foo',
                    ],
                ],
                'field' => 'uuid3',
                'expectedClass' => UuidV3::class,
                'expectedVersion' => 3,
            ],
            'uuid4' => [
                'listeners' => [
                    Uuid4Listener::class,
                    [
                        'field' => 'uuid4',
                    ],
                ],
                'field' => 'uuid4',
                'expectedClass' => UuidV4::class,
                'expectedVersion' => 4,
            ],
            'uuid5' => [
                'listeners' => [
                    Uuid5Listener::class,
                    [
                        'field' => 'uuid5',
                        'namespace' => NamespaceId::Url,
                        'name' => 'https://example.com/foo',
                    ],
                ],
                'field' => 'uuid5',
                'expectedClass' => UuidV5::class,
                'expectedVersion' => 5,
            ],
            'uuid6' => [
                'listeners' => [
                    Uuid6Listener::class,
                    [
                        'field' => 'uuid6',
                        'node' => '00000fffffff',
                        'clockSeq' => 0xffff,
                    ],
                ],
                'field' => 'uuid6',
                'expectedClass' => UuidV6::class,
                'expectedVersion' => 6,
            ],
            'uuid7' => [
                'listeners' => [
                    Uuid7Listener::class,
                    [
                        'field' => 'uuid7',
                    ],
                ],
                'field' => 'uuid7',
                'expectedClass' => UuidV7::class,
                'expectedVersion' => 7,
            ],
        ];
    }

    public static function uuidExceptionDataProvider(): array
    {
        return [
            'uuid3-undefined-namespace' => [
                'listeners' => [
                    Uuid3Listener::class,
                    [
                        'field' => 'uuid3',
                        'name' => 'https://example.com/foo',
                    ],
                ],
                'expectedException' => \InvalidArgumentException::class,
            ],
            'uuid3-undefined-name' => [
                'listeners' => [
                    Uuid3Listener::class,
                    [
                        'field' => 'uuid3',
                        'namespace' => NamespaceId::Url,
                    ],
                ],
                'expectedException' => \InvalidArgumentException::class,
            ],
            'uuid5-undefined-namespace' => [
                'listeners' => [
                    Uuid5Listener::class,
                    [
                        'field' => 'uuid5',
                        'name' => 'https://example.com/foo',
                    ],
                ],
                'expectedException' => \InvalidArgumentException::class,
            ],
            'uuid5-undefined-name' => [
                'listeners' => [
                    Uuid5Listener::class,
                    [
                        'field' => 'uuid5',
                        'namespace' => NamespaceId::Url,
                    ],
                ],
                'expectedException' => \InvalidArgumentException::class,
            ],
        ];
    }

    #[Test]
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

        Assert::null($data->uuid1);
        Assert::null($data->uuid2);
        Assert::null($data->uuid3);
        Assert::null($data->uuid4);
        Assert::null($data->uuid5);
        Assert::null($data->uuid6);
        Assert::null($data->uuid7);
    }

    #[Test]
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

        Assert::same($data->uuid1->toString(), $entity->uuid1->toString());
        Assert::same($data->uuid2->toString(), $entity->uuid2->toString());
        Assert::same($data->uuid3->toString(), $entity->uuid3->toString());
        Assert::same($data->uuid4->toString(), $entity->uuid4->toString());
        Assert::same($data->uuid5->toString(), $entity->uuid5->toString());
        Assert::same($data->uuid6->toString(), $entity->uuid6->toString());
        Assert::same($data->uuid7->toString(), $entity->uuid7->toString());
    }

    #[Test]
    #[DataProvider('uuidGenerationDataProvider')]
    public function testUuidGeneration(
        array $listeners,
        string $field,
        string $expectedClass,
        int $expectedVersion,
    ): void {
        $this->withListeners($listeners);

        $entity = new AllUuid();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllUuid::class);
        $data = $select->fetchOne();

        $uuid = $data->$field;
        Assert::instanceOf($uuid, $expectedClass);
        Assert::same($uuid->getVersion()->value, $expectedVersion);
        Assert::string($uuid->toString());
    }

    #[Test]
    #[DataProvider('uuidExceptionDataProvider')]
    public function testUuidException(
        array $listeners,
        string $expectedException,
    ): void {
        Expect::exception($expectedException);

        $this->withListeners($listeners);

        $entity = new AllUuid();
        $this->save($entity);
    }

    public static function argumentsOverDefaultsDataProvider(): iterable
    {
        $factory = new UuidFactory();

        yield 'uuid1' => [
            [Uuid1Listener::class, ['field' => 'uuid1', 'node' => '01000fffffff', 'clockSeq' => 0x0fff]],
            '/-8fff-01000fffffff$/',
        ];
        yield 'uuid2' => [
            [
                Uuid2Listener::class,
                [
                    'field' => 'uuid2',
                    'localDomain' => DceDomain::Person,
                    'localIdentifier' => 12345678,
                    'node' => '3d1239b4f540',
                    'clockSeq' => 5,
                ],
            ],
            '/^00bc614e-.{4}-2.{3}-8500-3d1239b4f540$/',
        ];
        yield 'uuid3' => [
            [Uuid3Listener::class, ['field' => 'uuid3', 'namespace' => NamespaceId::Url, 'name' => 'foo']],
            '/^' . $factory->v3(NamespaceId::Url, 'foo')->toString() . '$/',
        ];
        yield 'uuid5' => [
            [Uuid5Listener::class, ['field' => 'uuid5', 'namespace' => NamespaceId::Url, 'name' => 'foo']],
            '/^' . $factory->v5(NamespaceId::Url, 'foo')->toString() . '$/',
        ];
        yield 'uuid6' => [
            [Uuid6Listener::class, ['field' => 'uuid6', 'node' => '01000fffffff', 'clockSeq' => 0x0fff]],
            '/-8fff-01000fffffff$/',
        ];
    }

    #[Test]
    #[DataProvider('argumentsOverDefaultsDataProvider')]
    public function testListenerArgumentsOverrideDefaults(array $listener, string $expected): void
    {
        Uuid1Listener::setDefaults('111111111111', 1);
        Uuid2Listener::setDefaults(DceDomain::Group, 1, '111111111111', 1);
        Uuid3Listener::setDefaults(NamespaceId::Dns, 'bar');
        Uuid5Listener::setDefaults(NamespaceId::Dns, 'bar');
        Uuid6Listener::setDefaults('111111111111', 1);
        $this->withListeners($listener);

        $this->save(new AllUuid());

        $field = $listener[1]['field'];
        $data = (new Select($this->orm->with(heap: new Heap()), AllUuid::class))->fetchOne();
        Assert::string($data->$field->toString())->matchesRegex($expected);
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

    #[BeforeTest]
    protected function setUp(): void
    {
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

    /**
     * The listener defaults are static: reset them after each test too, so they never leak into other classes.
     */
    #[BeforeTest]
    #[AfterTest]
    protected function resetDefaults(): void
    {
        Uuid1Listener::setDefaults(null, null);
        Uuid2Listener::setDefaults(DceDomain::Person, null, null, null);
        Uuid3Listener::setDefaults(null, null);
        Uuid5Listener::setDefaults(null, null);
        Uuid6Listener::setDefaults(null, null);
    }
}
