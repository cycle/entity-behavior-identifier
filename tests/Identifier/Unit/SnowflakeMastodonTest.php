<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Unit;

use Cycle\ORM\Entity\Behavior\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeMastodon as Listener;
use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeMastodon;
use Cycle\ORM\SchemaInterface;
use PHPUnit\Framework\TestCase;

final class SnowflakeMastodonTest
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
                            'tableName' => null,
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
                            'tableName' => null,
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
                            'tableName' => 'users',
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['custom_snowflake', null, 'users'],
        ];
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('schemaDataProvider')]
    public function testModifySchema(array $expected, array $args): void
    {
        $schema = [];
        $snowflake = new SnowflakeMastodon(...$args);
        $snowflake->modifySchema($schema);

        \Testo\Assert::same($schema, $expected);
    }

    #[\Testo\Test]
    public function testModifySchemaWithDefaults(): void
    {
        Listener::setDefaults('users');

        $args = ['snowflake', null, null, false];

        $expected = [
            SchemaInterface::LISTENERS => [
                [
                    ListenerProvider::DEFINITION_CLASS => Listener::class,
                    ListenerProvider::DEFINITION_ARGS => [
                        'field' => 'snowflake',
                        'tableName' => null,
                        'nullable' => false,
                    ],
                ],
            ],
        ];

        $schema = [];
        $snowflake = new SnowflakeMastodon(...$args);
        $snowflake->modifySchema($schema);

        \Testo\Assert::same($schema, $expected);
    }

    #[\Testo\Lifecycle\BeforeTest]
    protected function setUp(): void
    {
        Listener::setDefaults(null);
    }
}
