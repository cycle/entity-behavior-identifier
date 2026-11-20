<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Unit;

use Cycle\ORM\Entity\Behavior\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid3 as Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid3;
use Cycle\ORM\SchemaInterface;
use PHPUnit\Framework\TestCase;

final class Uuid3Test
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
                            'namespace' => null,
                            'name' => null,
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
                            'field' => 'uuid',
                            'namespace' => 'foo',
                            'name' => 'bar',
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['uuid', null, 'foo', 'bar'],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_uuid',
                            'namespace' => 'foo',
                            'name' => 'bar',
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['custom_uuid', null, 'foo', 'bar'],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_uuid',
                            'namespace' => 'foo',
                            'name' => 'bar',
                            'nullable' => true,
                        ],
                    ],
                ],
            ],
            ['custom_uuid', null, 'foo', 'bar', true],
        ];
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('schemaDataProvider')]
    public function testModifySchema(array $expected, array $args): void
    {
        $schema = [];
        $uuid = new Uuid3(...$args);
        $uuid->modifySchema($schema);

        \Testo\Assert::same($schema, $expected);
    }

    #[\Testo\Test]
    public function testModifySchemaWithDefaults(): void
    {
        Listener::setDefaults('foo', 'bar');

        $args = ['uuid', null, null, null, false];

        $expected = [
            SchemaInterface::LISTENERS => [
                [
                    ListenerProvider::DEFINITION_CLASS => Listener::class,
                    ListenerProvider::DEFINITION_ARGS => [
                        'field' => 'uuid',
                        'namespace' => null,
                        'name' => null,
                        'nullable' => false,
                    ],
                ],
            ],
        ];

        $schema = [];
        $uuid = new Uuid3(...$args);
        $uuid->modifySchema($schema);

        \Testo\Assert::same($schema, $expected);
    }

    #[\Testo\Lifecycle\BeforeTest]
    #[\Testo\Lifecycle\AfterTest]
    protected function resetDefaults(): void
    {
        Listener::setDefaults(null, null);
    }
}
