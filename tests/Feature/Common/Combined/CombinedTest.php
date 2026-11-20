<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Feature\Common\Combined;

use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\ORM\Entity\Behavior\Identifier\SnowflakeGeneric;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Combined\MultipleIdentifiers;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Support\IdentifierTestCase;
use Cycle\ORM\Entity\Behavior\Identifier\Ulid;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid4;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\Schema\Registry;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

final class CombinedTest extends IdentifierTestCase
{
    protected Registry $registry;
    protected TokenizerEntityLocator $tokenizer;

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('readersDataProvider')]
    public function testColumnsExist(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(MultipleIdentifiers::class)->getFields();

        \Testo\Assert::same($fields->count(), 6);

        \Testo\Assert::true($fields->has('uuid'));
        \Testo\Assert::true($fields->hasColumn('uuid'));
        \Testo\Assert::same($fields->get('uuid')->getType(), 'uuid');
        \Testo\Assert::same($fields->get('uuid')->getTypecast(), [Uuid4::class, 'create']);
        \Testo\Assert::same($fields->get('uuid')->getGenerated(), GeneratedField::BEFORE_INSERT);

        \Testo\Assert::true($fields->has('uuidNullable'));
        \Testo\Assert::true($fields->hasColumn('uuid_nullable'));
        \Testo\Assert::same($fields->get('uuidNullable')->getType(), 'uuid');
        \Testo\Assert::same($fields->get('uuidNullable')->getTypecast(), [Uuid4::class, 'create']);
        \Testo\Assert::null($fields->get('uuidNullable')->getGenerated());

        \Testo\Assert::true($fields->has('ulid'));
        \Testo\Assert::true($fields->hasColumn('ulid'));
        \Testo\Assert::same($fields->get('ulid')->getType(), 'ulid');
        \Testo\Assert::same($fields->get('ulid')->getTypecast(), [Ulid::class, 'create']);
        \Testo\Assert::same($fields->get('ulid')->getGenerated(), GeneratedField::BEFORE_INSERT);

        \Testo\Assert::true($fields->has('ulidNullable'));
        \Testo\Assert::true($fields->hasColumn('ulid_nullable'));
        \Testo\Assert::same($fields->get('ulidNullable')->getType(), 'ulid');
        \Testo\Assert::same($fields->get('ulidNullable')->getTypecast(), [Ulid::class, 'create']);
        \Testo\Assert::null($fields->get('ulidNullable')->getGenerated());

        \Testo\Assert::true($fields->has('snowflake'));
        \Testo\Assert::true($fields->hasColumn('snowflake'));
        \Testo\Assert::same($fields->get('snowflake')->getType(), 'snowflake');
        \Testo\Assert::same($fields->get('snowflake')->getGenerated(), GeneratedField::BEFORE_INSERT);
        \Testo\Assert::same($fields->get('snowflake')->getTypecast(), [SnowflakeGeneric::class, 'create', [0]]);

        \Testo\Assert::true($fields->has('snowflakeNullable'));
        \Testo\Assert::true($fields->hasColumn('snowflake_nullable'));
        \Testo\Assert::same($fields->get('snowflakeNullable')->getType(), 'snowflake');
        \Testo\Assert::null($fields->get('snowflakeNullable')->getGenerated());
        \Testo\Assert::same($fields->get('snowflakeNullable')->getTypecast(), [SnowflakeGeneric::class, 'create', [0]]);
    }

    #[\Testo\Lifecycle\BeforeTest]
    protected function setUp(): void
    {
        $locator = new ClassLocator((new Finder())->files()->in([\dirname(__DIR__, 3) . '/Fixtures/Combined']));
        $reader = new AttributeReader();
        $this->tokenizer = new TokenizerEntityLocator($locator, $reader);
    }
}
