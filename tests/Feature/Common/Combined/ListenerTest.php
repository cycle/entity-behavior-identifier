<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Feature\Common\Combined;

use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeGeneric as SnowflakeGenericListener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Ulid as UlidListener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid4 as Uuid4Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Combined\MultipleIdentifiers;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Support\IdentifierTestCase;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Ramsey\Identifier\Snowflake as SnowflakeInterface;
use Ramsey\Identifier\Snowflake\GenericSnowflakeFactory;
use Ramsey\Identifier\Ulid as UlidInterface;
use Ramsey\Identifier\Ulid\UlidFactory;
use Ramsey\Identifier\Uuid\UntypedUuid;
use Ramsey\Identifier\Uuid\UuidFactory;

final class ListenerTest extends IdentifierTestCase
{
    #[\Testo\Test]
    public function testAssignManually(): void
    {
        $this->withListeners([
            [Uuid4Listener::class, ['field' => 'uuid']],
            [UlidListener::class, ['field' => 'ulid']],
            [SnowflakeGenericListener::class, ['field' => 'snowflake']],
        ]);

        $identifiers = new MultipleIdentifiers();
        $identifiers->uuid = (new UuidFactory())->v4();
        $identifiers->ulid = (new UlidFactory())->create();
        $identifiers->snowflake = (new GenericSnowflakeFactory(0, 0))->create();
        $uuidBytes = $identifiers->uuid->toBytes();
        $ulidBytes = $identifiers->ulid->toBytes();
        $snowflakeBytes = $identifiers->snowflake->toBytes();

        $this->save($identifiers);

        $select = new Select($this->orm->with(heap: new Heap()), MultipleIdentifiers::class);
        $data = $select->fetchOne();

        \Testo\Assert::same($data->uuid->toBytes(), $uuidBytes);
        \Testo\Assert::same($data->ulid->toBytes(), $ulidBytes);
        \Testo\Assert::same($data->snowflake->toBytes(), $snowflakeBytes);
    }

    #[\Testo\Test]
    public function testWithNullableTrue(): void
    {
        $this->withListeners([
            [Uuid4Listener::class, ['field' => 'uuid']],
            [UlidListener::class, ['field' => 'ulid']],
            [SnowflakeGenericListener::class, ['field' => 'snowflake']],
        ]);

        $identifiers = new MultipleIdentifiers();
        $identifiers->uuid = (new UuidFactory())->v4();
        $identifiers->ulid = (new UlidFactory())->create();
        $identifiers->snowflake = (new GenericSnowflakeFactory(0, 0))->create();

        $this->save($identifiers);

        $select = new Select($this->orm->with(heap: new Heap()), MultipleIdentifiers::class);
        $data = $select->fetchData();

        \Testo\Assert::null($data[0]['uuid_nullable']);
        \Testo\Assert::null($data[0]['ulid_nullable']);
        \Testo\Assert::null($data[0]['snowflake_nullable']);
    }

    #[\Testo\Test]
    public function testCombined(): void
    {
        $this->withListeners([
            [Uuid4Listener::class, ['field' => 'uuid']],
            [UlidListener::class, ['field' => 'ulid']],
            [SnowflakeGenericListener::class, ['field' => 'snowflake']],
        ]);

        $identifiers = new MultipleIdentifiers();
        $this->save($identifiers);

        $select = new Select($this->orm->with(heap: new Heap()), MultipleIdentifiers::class);
        /** @var MultipleIdentifiers $data */
        $data = $select->fetchOne();

        \Testo\Assert::instanceOf($data->uuid, UntypedUuid::class);
        \Testo\Assert::instanceOf($data->ulid, UlidInterface::class);
        \Testo\Assert::instanceOf($data->snowflake, SnowflakeInterface::class);
        \Testo\Assert::null($data->uuidNullable);
        \Testo\Assert::null($data->ulidNullable);
        \Testo\Assert::null($data->snowflakeNullable);
        \Testo\Assert::string($data->uuid->toBytes());
        \Testo\Assert::string($data->uuid->toString());
        \Testo\Assert::string($data->ulid->toBytes());
        \Testo\Assert::string($data->ulid->toString());
        \Testo\Assert::string($data->snowflake->toBytes());
        \Testo\Assert::string($data->snowflake->toString());
    }

    #[\Testo\Test]
    public function testComparison(): void
    {
        $this->withListeners([
            [Uuid4Listener::class, ['field' => 'uuid']],
            [UlidListener::class, ['field' => 'ulid']],
            [SnowflakeGenericListener::class, ['field' => 'snowflake']],
        ]);

        $expectedDate = '2025-06-17 03:24:36.160 +00:00';

        $identifiers = new MultipleIdentifiers();
        $identifiers->uuid = (new UuidFactory())->createFromString('01977bea-d1c0-7154-87bb-6550974155c2');
        $identifiers->ulid = (new UlidFactory())->createFromString('01JXXYNME0E5A8FEV5A2BM2NE2');
        $identifiers->snowflake = (new GenericSnowflakeFactory(0, 0))->createFromInteger(7340580095540599922);

        $this->save($identifiers);

        $select = new Select($this->orm->with(heap: new Heap()), MultipleIdentifiers::class);
        $data = $select->fetchOne();

        \Testo\Assert::same($data->uuid->getDateTime()->format('Y-m-d H:i:s.v P'), $expectedDate);
        \Testo\Assert::same($data->ulid->getDateTime()->format('Y-m-d H:i:s.v P'), $expectedDate);
        \Testo\Assert::same($data->snowflake->getDateTime()->format('Y-m-d H:i:s.v P'), $expectedDate);
        \Testo\Assert::true($data->uuid->equals($data->ulid));
        \Testo\Assert::false($data->uuid->equals($data->snowflake));
    }

    public function withListeners(array $listeners): void
    {
        $snowflakeFactory = new GenericSnowflakeFactory(0, 0);
        $uuidFactory = new UuidFactory();
        $ulidFactory = new UlidFactory();

        $this->withSchema(new Schema([
            MultipleIdentifiers::class => [
                SchemaInterface::ROLE => 'multiple_identifier',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'multiple_identifiers',
                SchemaInterface::PRIMARY_KEY => 'ulid',
                SchemaInterface::COLUMNS => [
                    'uuid',
                    'uuid_nullable',
                    'ulid',
                    'ulid_nullable',
                    'snowflake',
                    'snowflake_nullable',
                ],
                SchemaInterface::LISTENERS => $listeners,
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
                SchemaInterface::TYPECAST => [
                    'uuid' => [$uuidFactory, 'createFromString'],
                    'uuid_nullable' => [$uuidFactory, 'createFromString'],
                    'ulid' => [$ulidFactory, 'createFromString'],
                    'ulid_nullable' => [$ulidFactory, 'createFromString'],
                    'snowflake' => [$snowflakeFactory, 'createFromInteger'],
                    'snowflake_nullable' => [$snowflakeFactory, 'createFromInteger'],
                ],
            ],
        ]));
    }

    #[\Testo\Lifecycle\BeforeTest]
    protected function setUp(): void
    {
        $this->makeTable(
            'multiple_identifiers',
            [
                'uuid' => 'string',
                'uuid_nullable' => 'string,nullable',
                'ulid' => 'string',
                'ulid_nullable' => 'string,nullable',
                'snowflake' => 'snowflake',
                'snowflake_nullable' => 'snowflake,nullable',
            ],
        );
    }
}
