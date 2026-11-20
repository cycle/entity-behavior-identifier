<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Unit;

use Cycle\ORM\Entity\Behavior\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Uuid2 as Listener;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid2;
use Cycle\ORM\SchemaInterface;
use Ramsey\Identifier\Uuid\DceDomain;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Lifecycle\AfterTest;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

#[Test]
final class Uuid2Test
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
                            'localDomain' => DceDomain::Person->value,
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
                            'localDomain' => DceDomain::Person->value,
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
                            'localDomain' => DceDomain::Person->value,
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
                            'localDomain' => DceDomain::Person->value,
                            'localIdentifier' => 3,
                            'node' => 'bar',
                            'clockSeq' => null,
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['custom_uuid', null, DceDomain::Person, 3, 'bar'],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_uuid',
                            'localDomain' => DceDomain::Group->value,
                            'localIdentifier' => 3,
                            'node' => 'bar',
                            'clockSeq' => 4,
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['custom_uuid', null, DceDomain::Group, 3, 'bar', 4],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_uuid',
                            'localDomain' => DceDomain::Org->value,
                            'localIdentifier' => 3,
                            'node' => 'bar',
                            'clockSeq' => 4,
                            'nullable' => true,
                        ],
                    ],
                ],
            ],
            ['custom_uuid', null, DceDomain::Org, 3, 'bar', 4, true],
        ];
    }

    #[DataProvider('schemaDataProvider')]
    public function testModifySchema(array $expected, array $args): void
    {
        $schema = [];
        $uuid = new Uuid2(...$args);
        $uuid->modifySchema($schema);

        Assert::same($schema, $expected);
    }

    public function testModifySchemaWithDefaults(): void
    {
        Listener::setDefaults(DceDomain::Group, 2, 'foo', 3);

        $args = ['uuid', null, null, null, null, null, false];

        $expected = [
            SchemaInterface::LISTENERS => [
                [
                    ListenerProvider::DEFINITION_CLASS => Listener::class,
                    ListenerProvider::DEFINITION_ARGS => [
                        'field' => 'uuid',
                        'localDomain' => DceDomain::Person->value,
                        'localIdentifier' => null,
                        'node' => null,
                        'clockSeq' => null,
                        'nullable' => false,
                    ],
                ],
            ],
        ];

        $schema = [];
        $snowflake = new Uuid2(...$args);
        $snowflake->modifySchema($schema);

        Assert::same($schema, $expected);
    }

    #[BeforeTest]
    #[AfterTest]
    protected function resetDefaults(): void
    {
        Listener::setDefaults(DceDomain::Person, null, null, null);
    }
}
