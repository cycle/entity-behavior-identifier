<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Unit;

use Cycle\ORM\Entity\Behavior\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Ulid as Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Ulid;
use Cycle\ORM\SchemaInterface;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Test;

#[Test]
final class UlidTest
{
    public static function schemaDataProvider(): \Traversable
    {
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
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
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
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
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
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

    #[DataProvider('schemaDataProvider')]
    public function testModifySchema(array $expected, array $args): void
    {
        $schema = [];
        $ulid = new Ulid(...$args);
        $ulid->modifySchema($schema);

        Assert::same($schema, $expected);
    }
}
