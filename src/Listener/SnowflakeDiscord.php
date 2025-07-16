<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Listener;

use Cycle\ORM\Entity\Behavior\Attribute\Listen;
use Cycle\ORM\Entity\Behavior\Event\Mapper\Command\OnCreate;
use Ramsey\Identifier\Snowflake\DiscordSnowflakeFactory;

final class SnowflakeDiscord
{
    public function __construct(
        private string $field = 'snowflake',
        private int $workerId = 0,
        private int $processId = 0,
        private bool $nullable = false,
    ) {}

    #[Listen(OnCreate::class)]
    public function __invoke(OnCreate $event): void
    {
        if ($this->nullable || isset($event->state->getData()[$this->field])) {
            return;
        }

        $identifier = (new DiscordSnowflakeFactory($this->workerId, $this->processId))->create();

        $event->state->register($this->field, $identifier);
    }
}
