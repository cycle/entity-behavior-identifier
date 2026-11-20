<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Feature\Common\Uuid;

use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid\MultipleUuid;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid\NullableUuid;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid\Post;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid\User;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Support\IdentifierTestCase;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid1;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid4;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid7;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\Schema\Registry;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

final class UuidTest extends IdentifierTestCase
{
    protected Registry $registry;
    protected TokenizerEntityLocator $tokenizer;

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('readersDataProvider')]
    public function testColumnExist(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(User::class)->getFields();

        \Testo\Assert::true($fields->has('uuid'));
        \Testo\Assert::true($fields->hasColumn('uuid'));
        \Testo\Assert::same($fields->get('uuid')->getType(), 'uuid');
        \Testo\Assert::same($fields->get('uuid')->getTypecast(), [Uuid1::class, 'create']);
        \Testo\Assert::same($fields->get('uuid')->getGenerated(), GeneratedField::BEFORE_INSERT);
        \Testo\Assert::same($fields->count(), 1);
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('readersDataProvider')]
    public function testAddColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(Post::class)->getFields();

        \Testo\Assert::true($fields->has('customUuid'));
        \Testo\Assert::true($fields->hasColumn('custom_uuid'));
        \Testo\Assert::same($fields->get('customUuid')->getType(), 'uuid');
        \Testo\Assert::same($fields->get('customUuid')->getTypecast(), [Uuid4::class, 'create']);
        \Testo\Assert::same($fields->get('customUuid')->getGenerated(), GeneratedField::BEFORE_INSERT);
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('readersDataProvider')]
    public function testMultipleUuid(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(MultipleUuid::class)->getFields();

        \Testo\Assert::true($fields->has('uuid'));
        \Testo\Assert::true($fields->hasColumn('uuid'));
        \Testo\Assert::same($fields->get('uuid')->getType(), 'uuid');
        \Testo\Assert::same($fields->get('uuid')->getTypecast(), [Uuid1::class, 'create']);
        \Testo\Assert::same($fields->get('uuid')->getGenerated(), GeneratedField::BEFORE_INSERT);

        \Testo\Assert::true($fields->has('otherUuid'));
        \Testo\Assert::true($fields->hasColumn('other_uuid'));
        \Testo\Assert::same($fields->get('otherUuid')->getType(), 'uuid');
        \Testo\Assert::same($fields->get('otherUuid')->getTypecast(), [Uuid1::class, 'create']);
        \Testo\Assert::same($fields->get('otherUuid')->getGenerated(), GeneratedField::BEFORE_INSERT);

        \Testo\Assert::true($fields->has('uuid7'));
        \Testo\Assert::true($fields->hasColumn('uuid7'));
        \Testo\Assert::same($fields->get('uuid7')->getType(), 'uuid');
        \Testo\Assert::same($fields->get('uuid7')->getTypecast(), [Uuid7::class, 'create']);
        \Testo\Assert::same($fields->get('uuid7')->getGenerated(), GeneratedField::BEFORE_INSERT);

        \Testo\Assert::true($fields->has('otherUuid7'));
        \Testo\Assert::true($fields->hasColumn('other_uuid7'));
        \Testo\Assert::same($fields->get('otherUuid7')->getType(), 'uuid');
        \Testo\Assert::same($fields->get('otherUuid7')->getTypecast(), [Uuid7::class, 'create']);
        \Testo\Assert::same($fields->get('otherUuid7')->getGenerated(), GeneratedField::BEFORE_INSERT);
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('readersDataProvider')]
    public function testAddNullableColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(NullableUuid::class)->getFields();

        \Testo\Assert::true($fields->has('notDefinedUuid'));
        \Testo\Assert::true($fields->hasColumn('not_defined_uuid'));
        \Testo\Assert::same($fields->get('notDefinedUuid')->getType(), 'uuid');
        \Testo\Assert::same($fields->get('notDefinedUuid')->getTypecast(), [Uuid1::class, 'create']);
        \Testo\Assert::true($this->registry
            ->getTableSchema($this->registry->getEntity(NullableUuid::class))
            ->column('not_defined_uuid')
            ->isNullable());
        \Testo\Assert::null($fields->get('notDefinedUuid')->getGenerated());
    }

    #[\Testo\Lifecycle\BeforeTest]
    protected function setUp(): void
    {
        $locator = new ClassLocator((new Finder())->files()->in([\dirname(__DIR__, 3) . '/Fixtures/Uuid']));
        $reader = new AttributeReader();
        $this->tokenizer = new TokenizerEntityLocator($locator, $reader);
    }
}
