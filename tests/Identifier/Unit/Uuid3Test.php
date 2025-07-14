<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Unit;

use Cycle\ORM\Entity\Behavior\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid3;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid3 as Listener;
use Cycle\ORM\SchemaInterface;
use PHPUnit\Framework\TestCase;

final class Uuid3Test extends TestCase
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
                            'namespace' => 'foo',
                            'name' => 'bar',
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['foo', 'bar'],
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
            ['foo', 'bar', 'custom_uuid'],
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
            ['foo', 'bar', 'custom_uuid', null, true],
        ];
    }

    /**
     * @dataProvider schemaDataProvider
     */
    public function testModifySchema(array $expected, array $args): void
    {
        $schema = [];
        $uuid = new Uuid3(...$args);
        $uuid->modifySchema($schema);

        $this->assertSame($expected, $schema);
    }
}
