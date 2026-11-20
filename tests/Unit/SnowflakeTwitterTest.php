<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Unit;

use Cycle\ORM\Entity\Behavior\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeTwitter as Listener;
use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeTwitter;
use Cycle\ORM\SchemaInterface;
use PHPUnit\Framework\TestCase;

final class SnowflakeTwitterTest
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
                            'machineId' => null,
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
                            'machineId' => null,
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
                            'machineId' => 3,
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['custom_snowflake', null, 3],
        ];
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('schemaDataProvider')]
    public function testModifySchema(array $expected, array $args): void
    {
        $schema = [];
        $snowflake = new SnowflakeTwitter(...$args);
        $snowflake->modifySchema($schema);

        \Testo\Assert::same($schema, $expected);
    }

    #[\Testo\Test]
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
                        'machineId' => null,
                        'nullable' => false,
                    ],
                ],
            ],
        ];

        $schema = [];
        $snowflake = new SnowflakeTwitter(...$args);
        $snowflake->modifySchema($schema);

        \Testo\Assert::same($schema, $expected);
    }

    #[\Testo\Lifecycle\BeforeTest]
    #[\Testo\Lifecycle\AfterTest]
    protected function resetDefaults(): void
    {
        Listener::setDefaults(null);
    }
}
