<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Snowflake;

use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Snowflake\User;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Traits\TableTrait;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeDiscord as SnowflakeDiscordListener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeGeneric as SnowflakeGenericListener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeInstagram as SnowflakeInstagramListener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeMastodon as SnowflakeMastodonListener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeTwitter as SnowflakeTwitterListener;
use Cycle\ORM\Heap\Heap;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select;
use Ramsey\Identifier\Snowflake\DiscordSnowflakeFactory;
use Ramsey\Identifier\Snowflake\GenericSnowflakeFactory;
use Ramsey\Identifier\Snowflake\InstagramSnowflakeFactory;
use Ramsey\Identifier\Snowflake\MastodonSnowflakeFactory;
use Ramsey\Identifier\Snowflake\TwitterSnowflakeFactory;
use Ramsey\Identifier\Snowflake as SnowflakeInterface;

abstract class ListenerTest extends BaseTest
{
    use TableTrait;

    public function testAssignManually(): void
    {
        $this->withListeners([
            SnowflakeGenericListener::class,
            [
                'field' => 'snowflake',
            ],
        ]);

        $user = new User();
        $user->snowflake = (new GenericSnowflakeFactory(0, 0))->create();
        $bytes = $user->snowflake->toBytes();

        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertSame($bytes, $data->snowflake->toBytes());
    }

    public function testDiscordSnowflake(): void
    {
        $this->withListeners([
            SnowflakeDiscordListener::class,
            [
                'field' => 'snowflake',
                'workerId' => 10,
                'processId' => 20,
            ],
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(SnowflakeInterface::class, $data->snowflake);
        $this->assertIsString($data->snowflake->toBytes());
        $this->assertIsString($data->snowflake->toString());
    }

    public function testNullableDiscordSnowflake(): void
    {
        $this->withListeners([
            SnowflakeDiscordListener::class,
            [
                'field' => 'foo_snowflake',
                'nullable' => true,
            ],
        ]);

        $user = new User();
        $user->snowflake = (new DiscordSnowflakeFactory(0, 0))->create();

        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchData();

        $this->assertNull($data[0]['foo_snowflake']);
    }

    public function testGenericSnowflake(): void
    {
        $this->withListeners([
            SnowflakeGenericListener::class,
            [
                'field' => 'snowflake',
                'node' => 10,
                'epochOffset' => 1662744255000,
            ],
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(SnowflakeInterface::class, $data->snowflake);
        $this->assertIsString($data->snowflake->toBytes());
        $this->assertIsString($data->snowflake->toString());
    }

    public function testNullableGenericSnowflake(): void
    {
        $this->withListeners([
            SnowflakeGenericListener::class,
            [
                'field' => 'foo_snowflake',
                'nullable' => true,
            ],
        ]);

        $user = new User();
        $user->snowflake = (new GenericSnowflakeFactory(0, 0))->create();

        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchData();

        $this->assertNull($data[0]['foo_snowflake']);
    }

    public function testInstagramSnowflake(): void
    {
        $this->withListeners([
            SnowflakeInstagramListener::class,
            [
                'field' => 'snowflake',
                'shardId' => 10,
            ],
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(SnowflakeInterface::class, $data->snowflake);
        $this->assertIsString($data->snowflake->toBytes());
        $this->assertIsString($data->snowflake->toString());
    }

    public function testNullableInstagramSnowflake(): void
    {
        $this->withListeners([
            SnowflakeInstagramListener::class,
            [
                'field' => 'foo_snowflake',
                'nullable' => true,
            ],
        ]);

        $user = new User();
        $user->snowflake = (new InstagramSnowflakeFactory(0))->create();

        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchData();

        $this->assertNull($data[0]['foo_snowflake']);
    }

    public function testMastodonSnowflake(): void
    {
        $this->withListeners([
            SnowflakeMastodonListener::class,
            [
                'field' => 'snowflake',
                'tableName' => 'users',
            ],
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(SnowflakeInterface::class, $data->snowflake);
        $this->assertIsString($data->snowflake->toBytes());
        $this->assertIsString($data->snowflake->toString());
    }

    public function testNullableMastodonSnowflake(): void
    {
        $this->withListeners([
            SnowflakeMastodonListener::class,
            [
                'field' => 'foo_snowflake',
                'nullable' => true,
            ],
        ]);

        $user = new User();
        $user->snowflake = (new MastodonSnowflakeFactory(null))->create();

        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchData();

        $this->assertNull($data[0]['foo_snowflake']);
    }

    public function testTwitterSnowflake(): void
    {
        $this->withListeners([
            SnowflakeTwitterListener::class,
            [
                'field' => 'snowflake',
                'machineId' => 10,
            ],
        ]);

        $user = new User();
        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(SnowflakeInterface::class, $data->snowflake);
        $this->assertIsString($data->snowflake->toBytes());
        $this->assertIsString($data->snowflake->toString());
    }

    public function testNullableTwitterSnowflake(): void
    {
        $this->withListeners([
            SnowflakeTwitterListener::class,
            [
                'field' => 'foo_snowflake',
                'nullable' => true,
            ],
        ]);

        $user = new User();
        $user->snowflake = (new TwitterSnowflakeFactory(0))->create();

        $this->save($user);

        $select = new Select($this->orm->with(heap: new Heap()), User::class);
        $data = $select->fetchData();

        $this->assertNull($data[0]['foo_snowflake']);
    }

    public function withListeners(array|string $listeners): void
    {
        $factory = new GenericSnowflakeFactory(0, 0);

        $this->withSchema(new Schema([
            User::class => [
                SchemaInterface::ROLE => 'user',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'users',
                SchemaInterface::PRIMARY_KEY => 'snowflake',
                SchemaInterface::COLUMNS => ['snowflake', 'foo_snowflake'],
                SchemaInterface::LISTENERS => [$listeners],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
                SchemaInterface::TYPECAST => [
                    'snowflake' => [$factory, 'createFromInteger'],
                    'foo_snowflake' => [$factory, 'createFromInteger'],
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
                'snowflake' => 'snowflake',
                'foo_snowflake' => 'snowflake,nullable',
            ],
        );
    }
}
