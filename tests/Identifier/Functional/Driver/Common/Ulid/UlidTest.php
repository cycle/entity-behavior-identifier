<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Ulid;

use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid\MultipleUlid;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid\NullableUlid;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid\Post;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid\User;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Behavior\Identifier\Ulid;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\Schema\Registry;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

abstract class UlidTest extends BaseTest
{
    protected Registry $registry;
    protected TokenizerEntityLocator $tokenizer;

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('readersDataProvider')]
    public function testColumnExist(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(User::class)->getFields();

        \Testo\Assert::true($fields->has('ulid'));
        \Testo\Assert::true($fields->hasColumn('ulid'));
        \Testo\Assert::same($fields->get('ulid')->getType(), 'ulid');
        \Testo\Assert::same($fields->get('ulid')->getTypecast(), [Ulid::class, 'create']);
        \Testo\Assert::same($fields->get('ulid')->getGenerated(), GeneratedField::BEFORE_INSERT);
        \Testo\Assert::same($fields->count(), 1);
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('readersDataProvider')]
    public function testAddColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(Post::class)->getFields();

        \Testo\Assert::true($fields->has('customUlid'));
        \Testo\Assert::true($fields->hasColumn('custom_ulid'));
        \Testo\Assert::same($fields->get('customUlid')->getType(), 'ulid');
        \Testo\Assert::same($fields->get('customUlid')->getTypecast(), [Ulid::class, 'create']);
        \Testo\Assert::same($fields->get('customUlid')->getGenerated(), GeneratedField::BEFORE_INSERT);
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('readersDataProvider')]
    public function testMultipleUlid(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(MultipleUlid::class)->getFields();

        \Testo\Assert::true($fields->has('ulid'));
        \Testo\Assert::true($fields->hasColumn('ulid'));
        \Testo\Assert::same($fields->get('ulid')->getType(), 'ulid');
        \Testo\Assert::same($fields->get('ulid')->getTypecast(), [Ulid::class, 'create']);
        \Testo\Assert::same($fields->get('ulid')->getGenerated(), GeneratedField::BEFORE_INSERT);

        \Testo\Assert::true($fields->has('fooUlid'));
        \Testo\Assert::true($fields->hasColumn('foo_ulid'));
        \Testo\Assert::same($fields->get('fooUlid')->getType(), 'ulid');
        \Testo\Assert::same($fields->get('fooUlid')->getTypecast(), [Ulid::class, 'create']);
        \Testo\Assert::same($fields->get('fooUlid')->getGenerated(), GeneratedField::BEFORE_INSERT);

        \Testo\Assert::true($fields->has('bar'));
        \Testo\Assert::true($fields->hasColumn('bar'));
        \Testo\Assert::same($fields->get('bar')->getType(), 'ulid');
        \Testo\Assert::same($fields->get('bar')->getTypecast(), [Ulid::class, 'create']);
        \Testo\Assert::same($fields->get('bar')->getGenerated(), GeneratedField::BEFORE_INSERT);
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('readersDataProvider')]
    public function testAddNullableColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(NullableUlid::class)->getFields();

        \Testo\Assert::true($fields->has('notDefinedUlid'));
        \Testo\Assert::true($fields->hasColumn('not_defined_ulid'));
        \Testo\Assert::same($fields->get('notDefinedUlid')->getType(), 'ulid');
        \Testo\Assert::same($fields->get('notDefinedUlid')->getTypecast(), [Ulid::class, 'create']);
        \Testo\Assert::true($this->registry
            ->getTableSchema($this->registry->getEntity(NullableUlid::class))
            ->column('not_defined_ulid')
            ->isNullable());
        \Testo\Assert::null($fields->get('notDefinedUlid')->getGenerated());
    }

    #[\Override]
    #[\Testo\Lifecycle\BeforeTest]
    public function setUp(): void
    {
        parent::setUp();

        $locator = new ClassLocator((new Finder())->files()->in([\dirname(__DIR__, 4) . '/Fixtures/Ulid']));
        $reader = new AttributeReader();
        $this->tokenizer = new TokenizerEntityLocator($locator, $reader);
    }
}
