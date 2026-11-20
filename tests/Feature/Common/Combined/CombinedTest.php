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
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class CombinedTest extends IdentifierTestCase
{
    protected Registry $registry;
    protected TokenizerEntityLocator $tokenizer;

    #[Test]
    #[DataProvider('readersDataProvider')]
    public function testColumnsExist(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(MultipleIdentifiers::class)->getFields();

        Assert::same($fields->count(), 6);

        Assert::true($fields->has('uuid'));
        Assert::true($fields->hasColumn('uuid'));
        Assert::same($fields->get('uuid')->getType(), 'uuid');
        Assert::same($fields->get('uuid')->getTypecast(), [Uuid4::class, 'create']);
        Assert::same($fields->get('uuid')->getGenerated(), GeneratedField::BEFORE_INSERT);

        Assert::true($fields->has('uuidNullable'));
        Assert::true($fields->hasColumn('uuid_nullable'));
        Assert::same($fields->get('uuidNullable')->getType(), 'uuid');
        Assert::same($fields->get('uuidNullable')->getTypecast(), [Uuid4::class, 'create']);
        Assert::null($fields->get('uuidNullable')->getGenerated());

        Assert::true($fields->has('ulid'));
        Assert::true($fields->hasColumn('ulid'));
        Assert::same($fields->get('ulid')->getType(), 'ulid');
        Assert::same($fields->get('ulid')->getTypecast(), [Ulid::class, 'create']);
        Assert::same($fields->get('ulid')->getGenerated(), GeneratedField::BEFORE_INSERT);

        Assert::true($fields->has('ulidNullable'));
        Assert::true($fields->hasColumn('ulid_nullable'));
        Assert::same($fields->get('ulidNullable')->getType(), 'ulid');
        Assert::same($fields->get('ulidNullable')->getTypecast(), [Ulid::class, 'create']);
        Assert::null($fields->get('ulidNullable')->getGenerated());

        Assert::true($fields->has('snowflake'));
        Assert::true($fields->hasColumn('snowflake'));
        Assert::same($fields->get('snowflake')->getType(), 'snowflake');
        Assert::same($fields->get('snowflake')->getGenerated(), GeneratedField::BEFORE_INSERT);
        Assert::same($fields->get('snowflake')->getTypecast(), [SnowflakeGeneric::class, 'create', [0]]);

        Assert::true($fields->has('snowflakeNullable'));
        Assert::true($fields->hasColumn('snowflake_nullable'));
        Assert::same($fields->get('snowflakeNullable')->getType(), 'snowflake');
        Assert::null($fields->get('snowflakeNullable')->getGenerated());
        Assert::same($fields->get('snowflakeNullable')->getTypecast(), [SnowflakeGeneric::class, 'create', [0]]);
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $locator = new ClassLocator((new Finder())->files()->in([\dirname(__DIR__, 3) . '/Fixtures/Combined']));
        $reader = new AttributeReader();
        $this->tokenizer = new TokenizerEntityLocator($locator, $reader);
    }
}
