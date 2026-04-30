<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Unit;

use Cycle\ORM\Entity\Behavior\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeDiscord;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeDiscord as Listener;
use Cycle\ORM\SchemaInterface;
use PHPUnit\Framework\TestCase;

final class SnowflakeDiscordTest extends TestCase
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
                            'workerId' => null,
                            'processId' => null,
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
                            'workerId' => null,
                            'processId' => null,
                            'nullable' => true,
                        ],
                    ],
                ],
            ],
            ['custom_snowflake', null, null, null, true],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => Listener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_snowflake',
                            'workerId' => 3,
                            'processId' => 6,
                            'nullable' => false,
                        ],
                    ],
                ],
            ],
            ['custom_snowflake', null, 3, 6],
        ];
    }

    /**
     * @dataProvider schemaDataProvider
     */
    public function testModifySchema(array $expected, array $args): void
    {
        $schema = [];
        $snowflake = new SnowflakeDiscord(...$args);
        $snowflake->modifySchema($schema);

        $this->assertSame($expected, $schema);
    }

    public function testModifySchemaWithDefaults(): void
    {
        Listener::setDefaults(1, 2);

        $args = ['snowflake', null, null, null, false];

        $expected = [
            SchemaInterface::LISTENERS => [
                [
                    ListenerProvider::DEFINITION_CLASS => Listener::class,
                    ListenerProvider::DEFINITION_ARGS => [
                        'field' => 'snowflake',
                        'workerId' => null,
                        'processId' => null,
                        'nullable' => false,
                    ],
                ],
            ],
        ];

        $schema = [];
        $snowflake = new SnowflakeDiscord(...$args);
        $snowflake->modifySchema($schema);

        $this->assertSame($expected, $schema);
    }

    #[\Override]
    protected function setUp(): void
    {
        Listener::setDefaults(null, null);

        parent::setUp();
    }
}
