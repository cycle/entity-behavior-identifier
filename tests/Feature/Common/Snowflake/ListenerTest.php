<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Feature\Common\Snowflake;

use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeDiscord as SnowflakeDiscordListener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeGeneric as SnowflakeGenericListener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeInstagram as SnowflakeInstagramListener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeMastodon as SnowflakeMastodonListener;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeTwitter as SnowflakeTwitterListener;
use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeDiscord;
use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeGeneric;
use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeInstagram;
use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeMastodon;
use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeTwitter;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Snowflake\AllSnowflake;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Support\IdentifierTestCase;
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
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Expect;
use Testo\Lifecycle\AfterTest;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class ListenerTest extends IdentifierTestCase
{
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

    #[Test]
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

        Assert::null($data->generic);
        Assert::null($data->discord);
        Assert::null($data->instagram);
        Assert::null($data->mastodon);
        Assert::null($data->twitter);
    }

    #[Test]
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

        Assert::same($data->generic->toString(), $entity->generic->toString());
        Assert::same($data->discord->toString(), $entity->discord->toString());
        Assert::same($data->instagram->toString(), $entity->instagram->toString());
        Assert::same($data->mastodon->toString(), $entity->mastodon->toString());
        Assert::same($data->twitter->toString(), $entity->twitter->toString());
    }

    #[Test]
    #[DataProvider('snowflakeGenerationDataProvider')]
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
        Assert::instanceOf($snowflake, $expectedClass);
        Assert::string($snowflake->toString());
        Assert::same(\strlen($snowflake->toString()), $expectedLength);
    }

    #[Test]
    #[DataProvider('snowflakeExceptionDataProvider')]
    public function testSnowflakeException(
        string $listenerClass,
        array $defaults,
        string $expectedException,
    ): void {
        Expect::exception($expectedException);

        if (\class_exists($listenerClass) && \method_exists($listenerClass, 'setDefaults')) {
            $listenerClass::setDefaults(...$defaults);
        }
    }

    #[Test]
    public function testListenerArgumentsOverrideDefaults(): void
    {
        SnowflakeGenericListener::setDefaults(5, 0);
        SnowflakeDiscordListener::setDefaults(3, 7);
        SnowflakeInstagramListener::setDefaults(5);
        SnowflakeTwitterListener::setDefaults(5);

        $generic = $this->generate([SnowflakeGenericListener::class, ['field' => 'generic', 'node' => 10]]);
        $discord = $this->generate([
            SnowflakeDiscordListener::class,
            ['field' => 'discord', 'workerId' => 10, 'processId' => 20],
        ]);
        $instagram = $this->generate([SnowflakeInstagramListener::class, ['field' => 'instagram', 'shardId' => 10]]);
        $twitter = $this->generate([SnowflakeTwitterListener::class, ['field' => 'twitter', 'machineId' => 10]]);

        Assert::same($generic >> 12 & 0x3FF, 10);
        Assert::same($discord >> 17 & 0x1F, 10);
        Assert::same($discord >> 12 & 0x1F, 20);
        Assert::same($instagram >> 10 & 0x1FFF, 10);
        Assert::same($twitter >> 12 & 0x3FF, 10);
    }

    #[Test]
    public function testDefaultsAcceptUpperBounds(): void
    {
        SnowflakeGenericListener::setDefaults(1023, 0);
        SnowflakeInstagramListener::setDefaults(1023);
        SnowflakeTwitterListener::setDefaults(1023);
        SnowflakeDiscordListener::setDefaults(281474976710655, 281474976710655);

        $generic = $this->generate([SnowflakeGenericListener::class, ['field' => 'generic']]);
        $instagram = $this->generate([SnowflakeInstagramListener::class, ['field' => 'instagram']]);
        $twitter = $this->generate([SnowflakeTwitterListener::class, ['field' => 'twitter']]);
        $discord = $this->generate([SnowflakeDiscordListener::class, ['field' => 'discord']]);

        Assert::same($generic >> 12 & 0x3FF, 1023);
        Assert::same($instagram >> 10 & 0x1FFF, 1023);
        Assert::same($twitter >> 12 & 0x3FF, 1023);
        // Discord keeps the worker and process IDs modulo 32.
        Assert::same($discord >> 17 & 0x1F, 31);
        Assert::same($discord >> 12 & 0x1F, 31);
    }

    #[Test]
    public function testDiscordDefaultProcessId(): void
    {
        SnowflakeDiscordListener::setDefaults(0, 0);
        $zero = $this->generate([SnowflakeDiscordListener::class, ['field' => 'discord']]);
        SnowflakeDiscordListener::setDefaults(0, 5);
        $five = $this->generate([SnowflakeDiscordListener::class, ['field' => 'discord']]);
        SnowflakeDiscordListener::setDefaults(0, 6);
        $six = $this->generate([SnowflakeDiscordListener::class, ['field' => 'discord']]);

        Assert::same($zero >> 12 & 0x1F, 0);
        Assert::same($five >> 12 & 0x1F, 5);
        Assert::same($six >> 12 & 0x1F, 6);
    }

    #[Test]
    public function testGenericDefaultEpochOffset(): void
    {
        SnowflakeGenericListener::setDefaults(0, 1662744255000);
        Assert::same(SnowflakeGenericListener::getEpochOffset(), 1662744255000);

        SnowflakeGenericListener::setDefaults(0, null);
        Assert::same(SnowflakeGenericListener::getEpochOffset(), 0);
    }

    #[Test]
    public function testMastodonRejectsEmptyTableName(): never
    {
        Expect::exception(\InvalidArgumentException::class);

        SnowflakeMastodonListener::setDefaults('');
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

    #[BeforeTest]
    protected function setUp(): void
    {
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

    /**
     * The listener defaults are static: reset them after each test too, so they never leak into other classes.
     */
    #[BeforeTest]
    #[AfterTest]
    protected function resetDefaults(): void
    {
        SnowflakeGenericListener::setDefaults(0, 0);
        SnowflakeDiscordListener::setDefaults(0, null);
        SnowflakeInstagramListener::setDefaults(0);
        SnowflakeMastodonListener::setDefaults(null);
        SnowflakeTwitterListener::setDefaults(0);
    }

    /**
     * Saves a new entity with the given listener and returns the generated Snowflake as an integer.
     */
    private function generate(array $listener): int
    {
        $this->getDatabase()->table('all_snowflakes')->delete()->run();
        $this->withListeners($listener);
        $this->save(new AllSnowflake());

        $field = $listener[1]['field'];
        $data = (new Select($this->orm->with(heap: new Heap()), AllSnowflake::class))->fetchOne();

        return (int) $data->$field->toString();
    }
}
