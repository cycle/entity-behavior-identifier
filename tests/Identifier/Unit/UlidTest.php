<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Unit;

use Cycle\ORM\Entity\Behavior\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Behavior\Identifier\Ulid;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Ulid as UlidListener;
use Cycle\ORM\SchemaInterface;
use PHPUnit\Framework\TestCase;

final class UlidTest extends TestCase
{
    public static function schemaDataProvider(): \Traversable
    {
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => UlidListener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'ulid',
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
                        ListenerProvider::DEFINITION_CLASS => UlidListener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_ulid',
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['custom_ulid'],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => UlidListener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_ulid',
                            'nullable' => true,
                        ],
                    ],
                ],
            ],
            ['custom_ulid', null, true],
        ];
    }

    /**
     * @dataProvider schemaDataProvider
     */
    public function testModifySchema(array $expected, array $args): void
    {
        $schema = [];
        $ulid = new Ulid(...$args);
        $ulid->modifySchema($schema);

        $this->assertSame($expected, $schema);
    }
}
