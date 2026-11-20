<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Unit\Listener;

use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Cycle\ORM\Entity\Behavior\Identifier\Listener\Ulid;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid\AllUlid;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\SchemaInterface;
use Cycle\Testing\ORM\Memory;
use Ramsey\Identifier\Ulid as UlidInterface;
use Ramsey\Identifier\Ulid\UlidFactory;
use Testo\Assert;
use Testo\Test;

#[Test]
final class UlidTest
{
    public function generatesUlidForEmptyField(): void
    {
        $event = self::event([]);

        (new Ulid('ulid'))($event);

        Assert::instanceOf($event->state->getData()['ulid'], UlidInterface::class);
    }

    public function keepsAssignedUlid(): void
    {
        $ulid = (new UlidFactory())->create();
        $event = self::event(['ulid' => $ulid]);

        (new Ulid('ulid'))($event);

        Assert::same($event->state->getData()['ulid'], $ulid);
    }

    public function leavesNullableFieldEmpty(): void
    {
        $event = self::event([]);

        (new Ulid('ulid', nullable: true))($event);

        Assert::false(\array_key_exists('ulid', $event->state->getData()));
    }

    private static function event(array $data): OnCreate
    {
        $orm = Memory::orm([
            'ulid' => [
                SchemaInterface::ENTITY => AllUlid::class,
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'ulids',
                SchemaInterface::PRIMARY_KEY => 'ulid',
                SchemaInterface::COLUMNS => ['ulid'],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::RELATIONS => [],
            ],
        ]);

        return new OnCreate(
            'ulid',
            $orm->getMapper('ulid'),
            new AllUlid(),
            new Node(Node::NEW, [], 'ulid'),
            new State(Node::NEW, $data),
            $orm->getSource('ulid'),
            new \DateTimeImmutable(),
        );
    }
}
