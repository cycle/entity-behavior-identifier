<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Feature\Common\Ulid;

use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid\Comment;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid\MultipleUlid;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid\NullableUlid;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid\Post;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Ulid\User;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Support\IdentifierTestCase;
use Cycle\ORM\Entity\Behavior\Identifier\Ulid;
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

final class UlidTest extends IdentifierTestCase
{
    protected Registry $registry;
    protected TokenizerEntityLocator $tokenizer;

    #[Test]
    #[DataProvider('readersDataProvider')]
    public function testColumnExist(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(User::class)->getFields();

        Assert::true($fields->has('ulid'));
        Assert::true($fields->hasColumn('ulid'));
        Assert::same($fields->get('ulid')->getType(), 'ulid');
        Assert::same($fields->get('ulid')->getTypecast(), [Ulid::class, 'create']);
        Assert::same($fields->get('ulid')->getGenerated(), GeneratedField::BEFORE_INSERT);
        Assert::same($fields->count(), 1);
    }

    #[Test]
    #[DataProvider('readersDataProvider')]
    public function testAddColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(Post::class)->getFields();

        Assert::true($fields->has('customUlid'));
        Assert::true($fields->hasColumn('custom_ulid'));
        Assert::same($fields->get('customUlid')->getType(), 'ulid');
        Assert::same($fields->get('customUlid')->getTypecast(), [Ulid::class, 'create']);
        Assert::same($fields->get('customUlid')->getGenerated(), GeneratedField::BEFORE_INSERT);
    }

    #[Test]
    #[DataProvider('readersDataProvider')]
    public function testMultipleUlid(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(MultipleUlid::class)->getFields();

        Assert::true($fields->has('ulid'));
        Assert::true($fields->hasColumn('ulid'));
        Assert::same($fields->get('ulid')->getType(), 'ulid');
        Assert::same($fields->get('ulid')->getTypecast(), [Ulid::class, 'create']);
        Assert::same($fields->get('ulid')->getGenerated(), GeneratedField::BEFORE_INSERT);

        Assert::true($fields->has('fooUlid'));
        Assert::true($fields->hasColumn('foo_ulid'));
        Assert::same($fields->get('fooUlid')->getType(), 'ulid');
        Assert::same($fields->get('fooUlid')->getTypecast(), [Ulid::class, 'create']);
        Assert::same($fields->get('fooUlid')->getGenerated(), GeneratedField::BEFORE_INSERT);

        Assert::true($fields->has('bar'));
        Assert::true($fields->hasColumn('bar'));
        Assert::same($fields->get('bar')->getType(), 'ulid');
        Assert::same($fields->get('bar')->getTypecast(), [Ulid::class, 'create']);
        Assert::same($fields->get('bar')->getGenerated(), GeneratedField::BEFORE_INSERT);
    }

    #[Test]
    #[DataProvider('readersDataProvider')]
    public function testAddNullableColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(NullableUlid::class)->getFields();

        Assert::true($fields->has('notDefinedUlid'));
        Assert::true($fields->hasColumn('not_defined_ulid'));
        Assert::same($fields->get('notDefinedUlid')->getType(), 'ulid');
        Assert::same($fields->get('notDefinedUlid')->getTypecast(), [Ulid::class, 'create']);
        Assert::true($this->registry
            ->getTableSchema($this->registry->getEntity(NullableUlid::class))
            ->column('not_defined_ulid')
            ->isNullable());
        Assert::null($fields->get('notDefinedUlid')->getGenerated());
    }

    #[Test]
    #[DataProvider('readersDataProvider')]
    public function testAddColumnNamedAfterField(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $field = $this->registry->getEntity(Comment::class)->getFields()->get('token');

        Assert::same($field->getColumn(), 'token');
        Assert::same($field->getGenerated(), GeneratedField::BEFORE_INSERT);
        Assert::same($field->getTypecast(), [Ulid::class, 'create']);
    }

    #[Test]
    #[DataProvider('readersDataProvider')]
    public function testComputeConfiguresDeclaredFields(ReaderInterface $reader): void
    {
        $this->computeWithTokenizer($this->tokenizer, $reader);

        $field = $this->registry->getEntity(User::class)->getFields()->get('ulid');
        $nullable = $this->registry->getEntity(NullableUlid::class)->getFields();

        Assert::same($field->getTypecast(), [Ulid::class, 'create']);
        Assert::same($field->getGenerated(), GeneratedField::BEFORE_INSERT);
        Assert::null($nullable->get('notDefinedUlid')->getGenerated());
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $locator = new ClassLocator((new Finder())->files()->in([\dirname(__DIR__, 3) . '/Fixtures/Ulid']));
        $reader = new AttributeReader();
        $this->tokenizer = new TokenizerEntityLocator($locator, $reader);
    }
}
