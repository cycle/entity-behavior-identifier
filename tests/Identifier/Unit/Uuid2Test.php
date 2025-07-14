<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Unit;

use Cycle\ORM\Entity\Behavior\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid2;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid2 as Listener;
use Cycle\ORM\SchemaInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Identifier\Uuid\DceDomain;

final class Uuid2Test extends TestCase
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
                            'localDomain' => DceDomain::Person,
                            'localIdentifier' => null,
                            'node' => null,
                            'clockSeq' => null,
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['uuid'],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_uuid',
                            'localDomain' => DceDomain::Person,
                            'localIdentifier' => null,
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
                            'localDomain' => DceDomain::Person,
                            'localIdentifier' => 3,
                            'node' => null,
                            'clockSeq' => null,
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['custom_uuid', null, 0, 3],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_uuid',
                            'localDomain' => DceDomain::Person,
                            'localIdentifier' => 3,
                            'node' => 'bar',
                            'clockSeq' => null,
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['custom_uuid', null, 0, 3, 'bar'],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_uuid',
                            'localDomain' => DceDomain::Group,
                            'localIdentifier' => 3,
                            'node' => 'bar',
                            'clockSeq' => 4,
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['custom_uuid', null, 1, 3, 'bar', 4],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_uuid',
                            'localDomain' => DceDomain::Org,
                            'localIdentifier' => 3,
                            'node' => 'bar',
                            'clockSeq' => 4,
                            'nullable' => true,
                        ],
                    ],
                ],
            ],
            ['custom_uuid', null, 2, 3, 'bar', 4, true],
        ];
    }

    /**
     * @dataProvider schemaDataProvider
     */
    public function testModifySchema(array $expected, array $args): void
    {
        $schema = [];
        $uuid = new Uuid2(...$args);
        $uuid->modifySchema($schema);

        $this->assertSame($expected, $schema);
    }
}
