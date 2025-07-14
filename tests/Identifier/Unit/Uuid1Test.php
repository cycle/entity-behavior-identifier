<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Unit;

use Cycle\ORM\Entity\Behavior\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid1;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid1 as Listener;
use Cycle\ORM\SchemaInterface;
use PHPUnit\Framework\TestCase;

final class Uuid1Test extends TestCase
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

    /**
     * @dataProvider schemaDataProvider
     */
    public function testModifySchema(array $expected, array $args): void
    {
        $schema = [];
        $uuid = new Uuid1(...$args);
        $uuid->modifySchema($schema);

        $this->assertSame($expected, $schema);
    }
}
