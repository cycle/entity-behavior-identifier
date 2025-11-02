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

    public static function snowflakeGenerationDataProvider(): array
    {
        return [
            'generic' => [
                'listeners' => [
                    SnowflakeGenericListener::class,
                    [
                        'field' => 'generic',
                        'node' => 10,
                        'epochOffset' => 1662744255000,
                    ],
                ],
                'field' => 'generic',
                'expectedClass' => GenericSnowflake::class,
                'expectedLength' => 18,
            ],
            'discord' => [
                'listeners' => [
                    SnowflakeDiscordListener::class,
                    [
                        'field' => 'discord',
                        'workerId' => 10,
                        'processId' => 20,
                    ],
                ],
                'field' => 'discord',
                'expectedClass' => DiscordSnowflake::class,
                'expectedLength' => 19,
            ],
            'discord-omit-process-id' => [
                'listeners' => [
                    SnowflakeDiscordListener::class,
                    [
                        'field' => 'discord',
                        'workerId' => 10,
                    ],
                ],
                'field' => 'discord',
                'expectedClass' => DiscordSnowflake::class,
                'expectedLength' => 19,
            ],
            'instagram' => [
                'listeners' => [
                    SnowflakeInstagramListener::class,
                    [
                        'field' => 'instagram',
                        'shardId' => 10,
                    ],
                ],
                'field' => 'instagram',
                'expectedClass' => InstagramSnowflake::class,
                'expectedLength' => 19,
            ],
            'mastodon' => [
                'listeners' => [
                    SnowflakeMastodonListener::class,
                    [
                        'field' => 'mastodon',
                        'tableName' => 'foo',
                    ],
                ],
                'field' => 'mastodon',
                'expectedClass' => MastodonSnowflake::class,
                'expectedLength' => 18,
            ],
            'twitter' => [
                'listeners' => [
                    SnowflakeTwitterListener::class,
                    [
                        'field' => 'twitter',
                        'machineId' => 10,
                    ],
                ],
                'field' => 'twitter',
                'expectedClass' => TwitterSnowflake::class,
                'expectedLength' => 19,
            ],
        ];
    }

    public static function snowflakeExceptionDataProvider(): array
    {
        return [
            'generic-node-minimum' => [
                'listenerClass' => SnowflakeGenericListener::class,
                'defaults' => [-1, 1662744250000],
                'expectedException' => \InvalidArgumentException::class,
            ],
            'generic-node-maximum' => [
                'listenerClass' => SnowflakeGenericListener::class,
                'defaults' => [1024, 1662744250000],
                'expectedException' => \InvalidArgumentException::class,
            ],
            'discord-worker-id-minimum' => [
                'listenerClass' => SnowflakeDiscordListener::class,
                'defaults' => [-1, 30],
                'expectedException' => \InvalidArgumentException::class,
            ],
            'discord-worker-id-maximum' => [
                'listenerClass' => SnowflakeDiscordListener::class,
                'defaults' => [281474976710656, 30],
                'expectedException' => \InvalidArgumentException::class,
            ],
            'discord-process-id-minimum' => [
                'listenerClass' => SnowflakeDiscordListener::class,
                'defaults' => [20, -1],
                'expectedException' => \InvalidArgumentException::class,
            ],
            'discord-process-id-maximum' => [
                'listenerClass' => SnowflakeDiscordListener::class,
                'defaults' => [20, 281474976710656],
                'expectedException' => \InvalidArgumentException::class,
            ],
            'instagram-shard-id-minimum' => [
                'listenerClass' => SnowflakeInstagramListener::class,
                'defaults' => [-1],
                'expectedException' => \InvalidArgumentException::class,
            ],
            'instagram-shard-id-maximum' => [
                'listenerClass' => SnowflakeInstagramListener::class,
                'defaults' => [1024],
                'expectedException' => \InvalidArgumentException::class,
            ],
            'twitter-machine-id-minimum' => [
                'listenerClass' => SnowflakeTwitterListener::class,
                'defaults' => [-1],
                'expectedException' => \InvalidArgumentException::class,
            ],
            'twitter-machine-id-maximum' => [
                'listenerClass' => SnowflakeTwitterListener::class,
                'defaults' => [1024],
                'expectedException' => \InvalidArgumentException::class,
            ],
        ];
    }

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

    /**
     * @dataProvider snowflakeGenerationDataProvider
     */
    public function testSnowflakeGeneration(
        array $listeners,
        string $field,
        string $expectedClass,
        int $expectedLength,
    ): void {
        $this->withListeners($listeners);

        $entity = new AllSnowflake();
        $this->save($entity);

        $select = new Select($this->orm->with(heap: new Heap()), AllSnowflake::class);
        $data = $select->fetchOne();

        $snowflake = $data->$field;
        $this->assertInstanceOf($expectedClass, $snowflake);
        $this->assertIsString($snowflake->toString());
        $this->assertSame($expectedLength, \strlen($snowflake->toString()));
    }

    /**
     * @dataProvider snowflakeExceptionDataProvider
     */
    public function testSnowflakeException(
        string $listenerClass,
        array $defaults,
        string $expectedException,
    ): void {
        $this->expectException($expectedException);

        if (\class_exists($listenerClass) && \method_exists($listenerClass, 'setDefaults')) {
            $listenerClass::setDefaults(...$defaults);
        }
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
