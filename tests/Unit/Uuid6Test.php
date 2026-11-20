<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Unit;

use Cycle\ORM\Entity\Behavior\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid6 as Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid6;
use Cycle\ORM\SchemaInterface;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Lifecycle\AfterTest;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

#[Test]
final class Uuid6Test
{
    public static function schemaDataProvider(): \Traversable
    {
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'uuid',
                            'node' => null,
                            'clockSeq' => null,
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            [],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_uuid',
                            'node' => null,
                            'clockSeq' => null,
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['custom_uuid'],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_uuid',
                            'node' => 'foo',
                            'clockSeq' => null,
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['custom_uuid', null, 'foo'],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_uuid',
                            'node' => 'foo',
                            'clockSeq' => 3,
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['custom_uuid', null, 'foo', 3],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_uuid',
                            'node' => 'foo',
                            'clockSeq' => 3,
                            'nullable' => true,
                        ],
                    ],
                ],
            ],
            ['custom_uuid', null, 'foo', 3, true],
        ];
    }

    #[DataProvider('schemaDataProvider')]
    public function testModifySchema(array $expected, array $args): void
    {
        $schema = [];
        $uuid = new Uuid6(...$args);
        $uuid->modifySchema($schema);

        Assert::same($schema, $expected);
    }

    public function testModifySchemaWithDefaults(): void
    {
        Listener::setDefaults('foo', 1);

        $args = ['uuid', null, null, null, false];

        $expected = [
            SchemaInterface::LISTENERS => [
                [
                    ListenerProvider::DEFINITION_CLASS => Listener::class,
                    ListenerProvider::DEFINITION_ARGS => [
                        'field' => 'uuid',
                        'node' => null,
                        'clockSeq' => null,
                        'nullable' => false,
                    ],
                ],
            ],
        ];

        $schema = [];
        $snowflake = new Uuid6(...$args);
        $snowflake->modifySchema($schema);

        Assert::same($schema, $expected);
    }

    #[BeforeTest]
    #[AfterTest]
    protected function resetDefaults(): void
    {
        Listener::setDefaults(null, null);
    }
}
