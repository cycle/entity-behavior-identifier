<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Unit;

use Cycle\ORM\Entity\Behavior\Dispatcher\ListenerProvider;
use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeDiscord;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\SnowflakeDiscord as SnowflakeDiscordListener;
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
                        ListenerProvider::DEFINITION_CLASS => SnowflakeDiscordListener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'snowflake',
                            'workerId' => 0,
                            'processId' => 0,
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
                        ListenerProvider::DEFINITION_CLASS => SnowflakeDiscordListener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_snowflake',
                            'workerId' => 0,
                            'processId' => 0,
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
                        ListenerProvider::DEFINITION_CLASS => SnowflakeDiscordListener::class,
                        ListenerProvider::DEFINITION_ARGS => [
                            'field' => 'custom_snowflake',
                            'workerId' => 0,
                            'processId' => 0,
                            'nullable' => true,
                        ],
                    ],
                ],
            ],
            ['custom_snowflake', null, 0, 0, true],
        ];
        yield [
            [
                SchemaInterface::LISTENERS => [
                    [
                        ListenerProvider::DEFINITION_CLASS => SnowflakeDiscordListener::class,
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
}
