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

    /**
     * @dataProvider readersDataProvider
     */
    public function testColumnExist(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(User::class)->getFields();

        $this->assertTrue($fields->has('ulid'));
        $this->assertTrue($fields->hasColumn('ulid'));
        $this->assertSame('ulid', $fields->get('ulid')->getType());
        $this->assertSame([Ulid::class, 'create'], $fields->get('ulid')->getTypecast());
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('ulid')->getGenerated());
        $this->assertSame(1, $fields->count());
    }

    /**
     * @dataProvider readersDataProvider
     */
    public function testAddColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(Post::class)->getFields();

        $this->assertTrue($fields->has('customUlid'));
        $this->assertTrue($fields->hasColumn('custom_ulid'));
        $this->assertSame('ulid', $fields->get('customUlid')->getType());
        $this->assertSame([Ulid::class, 'create'], $fields->get('customUlid')->getTypecast());
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('customUlid')->getGenerated());
    }

    /**
     * @dataProvider readersDataProvider
     */
    public function testMultipleUlid(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(MultipleUlid::class)->getFields();

        $this->assertTrue($fields->has('ulid'));
        $this->assertTrue($fields->hasColumn('ulid'));
        $this->assertSame('ulid', $fields->get('ulid')->getType());
        $this->assertSame([Ulid::class, 'create'], $fields->get('ulid')->getTypecast());
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('ulid')->getGenerated());

        $this->assertTrue($fields->has('fooUlid'));
        $this->assertTrue($fields->hasColumn('foo_ulid'));
        $this->assertSame('ulid', $fields->get('fooUlid')->getType());
        $this->assertSame([Ulid::class, 'create'], $fields->get('fooUlid')->getTypecast());
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('fooUlid')->getGenerated());

        $this->assertTrue($fields->has('bar'));
        $this->assertTrue($fields->hasColumn('bar'));
        $this->assertSame('ulid', $fields->get('bar')->getType());
        $this->assertSame([Ulid::class, 'create'], $fields->get('bar')->getTypecast());
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('bar')->getGenerated());
    }

    /**
     * @dataProvider readersDataProvider
     */
    public function testAddNullableColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(NullableUlid::class)->getFields();

        $this->assertTrue($fields->has('notDefinedUlid'));
        $this->assertTrue($fields->hasColumn('not_defined_ulid'));
        $this->assertSame('ulid', $fields->get('notDefinedUlid')->getType());
        $this->assertSame([Ulid::class, 'create'], $fields->get('notDefinedUlid')->getTypecast());
        $this->assertTrue(
            $this->registry
                ->getTableSchema($this->registry->getEntity(NullableUlid::class))
                ->column('not_defined_ulid')
                ->isNullable(),
        );
        $this->assertNull($fields->get('notDefinedUlid')->getGenerated());
    }

    #[\Override]
    public function setUp(): void
    {
        parent::setUp();

        $locator = new ClassLocator((new Finder())->files()->in([\dirname(__DIR__, 4) . '/Fixtures/Ulid']));
        $reader = new AttributeReader();
        $this->tokenizer = new TokenizerEntityLocator($locator, $reader);
    }
}
