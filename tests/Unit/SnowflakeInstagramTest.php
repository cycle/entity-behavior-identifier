<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Unit;

use Cycle\ORM\Entity\Behavior\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeInstagram as Listener;
use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeInstagram;
use Cycle\ORM\SchemaInterface;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Lifecycle\AfterTest;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

#[Test]
final class SnowflakeInstagramTest
{
    public static function schemaDataProvider(): \Traversable
    {
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_snowflake',
                            'shardId' => null,
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['custom_snowflake'],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_snowflake',
                            'shardId' => null,
                            'nullable' => true,
                        ],
                    ],
                ],
            ],
            ['custom_snowflake', null, null, true],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_snowflake',
                            'shardId' => 3,
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['custom_snowflake', null, 3],
        ];
    }

    #[DataProvider('schemaDataProvider')]
    public function testModifySchema(array $expected, array $args): void
    {
        $schema = [];
        $snowflake = new SnowflakeInstagram(...$args);
        $snowflake->modifySchema($schema);

        Assert::same($schema, $expected);
    }

    public function testModifySchemaWithDefaults(): void
    {
        Listener::setDefaults(1);

        $args = ['snowflake', null, null, false];

        $expected = [
            SchemaInterface::LISTENERS => [
                [
                    ListenerProvider::DEFINITION_CLASS => Listener::class,
                    ListenerProvider::DEFINITION_ARGS => [
                        'field' => 'snowflake',
                        'shardId' => null,
                        'nullable' => false,
                    ],
                ],
            ],
        ];

        $schema = [];
        $snowflake = new SnowflakeInstagram(...$args);
        $snowflake->modifySchema($schema);

        Assert::same($schema, $expected);
    }

    #[BeforeTest]
    #[AfterTest]
    protected function resetDefaults(): void
    {
        Listener::setDefaults(null);
    }
}
