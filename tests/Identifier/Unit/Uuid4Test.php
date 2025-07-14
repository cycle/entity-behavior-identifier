<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Unit;

use Cycle\ORM\Entity\Behavior\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid4;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid4 as Listener;
use Cycle\ORM\SchemaInterface;
use PHPUnit\Framework\TestCase;

final class Uuid4Test extends TestCase
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
                            'nullable' => true,
                        ],
                    ],
                ],
            ],
            ['custom_uuid', null, true],
        ];
    }

    /**
     * @dataProvider schemaDataProvider
     */
    public function testModifySchema(array $expected, array $args): void
    {
        $schema = [];
        $uuid = new Uuid4(...$args);
        $uuid->modifySchema($schema);

        $this->assertSame($expected, $schema);
    }
}
