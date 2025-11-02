<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Snowflake;

use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeDiscord;
use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeGeneric;
use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeInstagram;
use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeMastodon;
use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeTwitter;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Snowflake\AllSnowflake;
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
use Ramsey\Identifier\Snowflake\DiscordSnowflake;
use Ramsey\Identifier\Snowflake\DiscordSnowflakeFactory;
use Ramsey\Identifier\Snowflake\GenericSnowflake;
use Ramsey\Identifier\Snowflake\GenericSnowflakeFactory;
use Ramsey\Identifier\Snowflake\InstagramSnowflake;
use Ramsey\Identifier\Snowflake\InstagramSnowflakeFactory;
use Ramsey\Identifier\Snowflake\MastodonSnowflake;
use Ramsey\Identifier\Snowflake\MastodonSnowflakeFactory;
use Ramsey\Identifier\Snowflake\TwitterSnowflake;
use Ramsey\Identifier\Snowflake\TwitterSnowflakeFactory;

abstract class ListenerTest extends BaseTest
{
    use TableTrait;

    public function testNullable(): void
    {
        $this->withListeners([
            SnowflakeGenericListener::class,
            [
                'field' => 'generic',
                'nullable' => true,
            ],
            SnowflakeGenericListener::class,
            [
                'field' => 'discord',
                'nullable' => true,
            ],
            SnowflakeGenericListener::class,
            [
                'field' => 'instagram',
                'nullable' => true,
            ],
            SnowflakeGenericListener::class,
            [
                'field' => 'mastodon',
                'nullable' => true,
            ],
            SnowflakeGenericListener::class,
            [
                'field' => 'twitter',
                'nullable' => true,
            ],
        ]);

        $entity = new AllSnowflake();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllSnowflake::class);
        $data = $select->fetchOne();

        $this->assertNull($data->generic);
        $this->assertNull($data->discord);
        $this->assertNull($data->instagram);
        $this->assertNull($data->mastodon);
        $this->assertNull($data->twitter);
    }

    public function testAssignManually(): void
    {
        $this->withListeners();

        $entity = new AllSnowflake();
        $entity->generic = (new GenericSnowflakeFactory(10, 1662744255000))->create();
        $entity->discord = (new DiscordSnowflakeFactory(10, 20))->create();
        $entity->instagram = (new InstagramSnowflakeFactory(10))->create();
        $entity->mastodon = (new MastodonSnowflakeFactory('users'))->create();
        $entity->twitter = (new TwitterSnowflakeFactory(10))->create();

        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllSnowflake::class);
        $data = $select->fetchOne();

        $this->assertSame($entity->generic->toString(), $data->generic->toString());
        $this->assertSame($entity->discord->toString(), $data->discord->toString());
        $this->assertSame($entity->instagram->toString(), $data->instagram->toString());
        $this->assertSame($entity->mastodon->toString(), $data->mastodon->toString());
        $this->assertSame($entity->twitter->toString(), $data->twitter->toString());
    }

    public function testGenericSnowflake(): void
    {
        $this->withListeners([
            SnowflakeGenericListener::class,
            [
                'field' => 'generic',
                'node' => 10,
                'epochOffset' => 1662744255000,
            ],
        ]);

        $entity = new AllSnowflake();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllSnowflake::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(GenericSnowflake::class, $data->generic);
        $this->assertIsString($data->generic->toString());
        $this->assertSame(18, \strlen($data->generic->toString()));
    }

    public function testGenericDefaults(): void
    {
        SnowflakeGenericListener::setDefaults(20, 1662744250000);

        $this->withListeners([
            SnowflakeGenericListener::class,
            [
                'field' => 'generic',
            ],
        ]);

        $entity = new AllSnowflake();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllSnowflake::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(GenericSnowflake::class, $data->generic);
        $this->assertIsString($data->generic->toString());
        $this->assertSame(18, \strlen($data->generic->toString()));
    }

    public function testGenericNodeMinimumThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        SnowflakeGenericListener::setDefaults(-1, 1662744250000);
    }

    public function testGenericNodeMaximumThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        SnowflakeGenericListener::setDefaults(1024, 1662744250000);
    }

    public function testDiscordSnowflake(): void
    {
        $this->withListeners([
            SnowflakeDiscordListener::class,
            [
                'field' => 'discord',
                'workerId' => 10,
                'processId' => 20,
            ],
        ]);

        $entity = new AllSnowflake();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllSnowflake::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(DiscordSnowflake::class, $data->discord);
        $this->assertIsString($data->discord->toString());
        $this->assertSame(19, \strlen($data->discord->toString()));
    }

    public function testDiscordDefaults(): void
    {
        SnowflakeDiscordListener::setDefaults(20, 30);

        $this->withListeners([
            SnowflakeDiscordListener::class,
            [
                'field' => 'discord',
            ],
        ]);

        $entity = new AllSnowflake();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllSnowflake::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(DiscordSnowflake::class, $data->discord);
        $this->assertIsString($data->discord->toString());
        $this->assertSame(19, \strlen($data->discord->toString()));
    }

    public function testDiscordWorkerIdMinimumThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        SnowflakeDiscordListener::setDefaults(-1, 30);
    }

    public function testDiscordWorkerIdMaximumThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        SnowflakeDiscordListener::setDefaults(281474976710656, 30);
    }

    public function testDiscordProcessIdMinimumThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        SnowflakeDiscordListener::setDefaults(20, -1);
    }

    public function testDiscordProcessIdMaximumThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        SnowflakeDiscordListener::setDefaults(20, 281474976710656);
    }

    public function testInstagramSnowflake(): void
    {
        $this->withListeners([
            SnowflakeInstagramListener::class,
            [
                'field' => 'instagram',
                'shardId' => 10,
            ],
        ]);

        $entity = new AllSnowflake();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllSnowflake::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(InstagramSnowflake::class, $data->instagram);
        $this->assertIsString($data->instagram->toString());
        $this->assertSame(19, \strlen($data->instagram->toString()));
    }

    public function testInstagramDefaults(): void
    {
        SnowflakeInstagramListener::setDefaults(20);

        $this->withListeners([
            SnowflakeInstagramListener::class,
            [
                'field' => 'instagram',
            ],
        ]);

        $entity = new AllSnowflake();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllSnowflake::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(InstagramSnowflake::class, $data->instagram);
        $this->assertIsString($data->instagram->toString());
        $this->assertSame(19, \strlen($data->instagram->toString()));
    }

    public function testInstagramShardIdMinimumThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        SnowflakeInstagramListener::setDefaults(-1);
    }

    public function testInstagramShardIdMaximumThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        SnowflakeInstagramListener::setDefaults(1024);
    }

    public function testMastodonSnowflake(): void
    {
        $this->withListeners([
            SnowflakeMastodonListener::class,
            [
                'field' => 'mastodon',
                'tableName' => 'users',
            ],
        ]);

        $entity = new AllSnowflake();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllSnowflake::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(MastodonSnowflake::class, $data->mastodon);
        $this->assertIsString($data->mastodon->toString());
        $this->assertSame(18, \strlen($data->mastodon->toString()));
    }

    public function testTwitterSnowflake(): void
    {
        $this->withListeners([
            SnowflakeTwitterListener::class,
            [
                'field' => 'twitter',
                'machineId' => 10,
            ],
        ]);

        $entity = new AllSnowflake();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllSnowflake::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(TwitterSnowflake::class, $data->twitter);
        $this->assertIsString($data->twitter->toString());
        $this->assertSame(19, \strlen($data->twitter->toString()));
    }

    public function testTwitterDefaults(): void
    {
        SnowflakeTwitterListener::setDefaults(20);

        $this->withListeners([
            SnowflakeTwitterListener::class,
            [
                'field' => 'twitter',
            ],
        ]);

        $entity = new AllSnowflake();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllSnowflake::class);
        $data = $select->fetchOne();

        $this->assertInstanceOf(TwitterSnowflake::class, $data->twitter);
        $this->assertIsString($data->twitter->toString());
        $this->assertSame(19, \strlen($data->twitter->toString()));
    }

    public function testTwitterMachineIdMinimumThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        SnowflakeTwitterListener::setDefaults(-1);
    }

    public function testTwitterMachineIdMaximumThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        SnowflakeTwitterListener::setDefaults(1024);
    }

    public function withListeners(array|string|null $listeners = null): void
    {
        $this->withSchema(new Schema([
            AllSnowflake::class => [
                SchemaInterface::ROLE => 'all_snowflake',
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'all_snowflakes',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::COLUMNS => ['id', 'generic', 'discord', 'instagram', 'mastodon', 'twitter'],
                SchemaInterface::LISTENERS => $listeners ? [$listeners] : [],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
                SchemaInterface::TYPECAST => [
                    'generic' => [SnowflakeGeneric::class, 'create', [10]],
                    'discord' => [SnowflakeDiscord::class, 'create'],
                    'instagram' => [SnowflakeInstagram::class, 'create'],
                    'mastodon' => [SnowflakeMastodon::class, 'create'],
                    'twitter' => [SnowflakeTwitter::class, 'create'],
                ],
            ],
        ]));
    }

    #[\Override]
    public function setUp(): void
    {
        parent::setUp();

        SnowflakeGenericListener::setDefaults(0, 0);
        SnowflakeDiscordListener::setDefaults(0, null);
        SnowflakeInstagramListener::setDefaults(0);
        SnowflakeTwitterListener::setDefaults(0);

        $this->makeTable(
            'all_snowflakes',
            [
                'id' => 'integer',
                'generic' => 'snowflake,nullable',
                'discord' => 'snowflake,nullable',
                'instagram' => 'snowflake,nullable',
                'mastodon' => 'snowflake,nullable',
                'twitter' => 'snowflake,nullable',
            ],
        );
    }
}
