<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Uuid;

use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid\MultipleUuid;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid\NullableUuid;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid\Post;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Uuid\User;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\Schema\Registry;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

abstract class UuidTest extends BaseTest
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

        $this->assertTrue($fields->has('uuid'));
        $this->assertTrue($fields->hasColumn('uuid'));
        $this->assertSame('uuid', $fields->get('uuid')->getType());
        $this->assertSame([Uuid::class, 'fromString'], $fields->get('uuid')->getTypecast());
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('uuid')->getGenerated());
        $this->assertSame(1, $fields->count());
    }

    /**
     * @dataProvider readersDataProvider
     */
    public function testAddColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(Post::class)->getFields();

        $this->assertTrue($fields->has('customUuid'));
        $this->assertTrue($fields->hasColumn('custom_uuid'));
        $this->assertSame('uuid', $fields->get('customUuid')->getType());
        $this->assertSame([Uuid::class, 'fromString'], $fields->get('customUuid')->getTypecast());
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('customUuid')->getGenerated());
    }

    /**
     * @dataProvider readersDataProvider
     */
    public function testMultipleUuid(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(MultipleUuid::class)->getFields();

        $this->assertTrue($fields->has('uuid'));
        $this->assertTrue($fields->hasColumn('uuid'));
        $this->assertSame('uuid', $fields->get('uuid')->getType());
        $this->assertSame([Uuid::class, 'fromString'], $fields->get('uuid')->getTypecast());
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('uuid')->getGenerated());

        $this->assertTrue($fields->has('otherUuid'));
        $this->assertTrue($fields->hasColumn('other_uuid'));
        $this->assertSame('uuid', $fields->get('otherUuid')->getType());
        $this->assertSame([Uuid::class, 'fromString'], $fields->get('otherUuid')->getTypecast());
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('otherUuid')->getGenerated());

        $this->assertTrue($fields->has('uuid7'));
        $this->assertTrue($fields->hasColumn('uuid7'));
        $this->assertSame('uuid', $fields->get('uuid7')->getType());
        $this->assertSame([Uuid::class, 'fromString'], $fields->get('uuid7')->getTypecast());
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('uuid7')->getGenerated());

        $this->assertTrue($fields->has('otherUuid7'));
        $this->assertTrue($fields->hasColumn('other_uuid7'));
        $this->assertSame('uuid', $fields->get('otherUuid7')->getType());
        $this->assertSame([Uuid::class, 'fromString'], $fields->get('otherUuid7')->getTypecast());
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('otherUuid7')->getGenerated());
    }

    /**
     * @dataProvider readersDataProvider
     */
    public function testAddNullableColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(NullableUuid::class)->getFields();

        $this->assertTrue($fields->has('notDefinedUuid'));
        $this->assertTrue($fields->hasColumn('not_defined_uuid'));
        $this->assertSame('uuid', $fields->get('notDefinedUuid')->getType());
        $this->assertSame([Uuid::class, 'fromString'], $fields->get('notDefinedUuid')->getTypecast());
        $this->assertTrue(
            $this->registry
                ->getTableSchema($this->registry->getEntity(NullableUuid::class))
                ->column('not_defined_uuid')
                ->isNullable(),
        );
        $this->assertNull($fields->get('notDefinedUuid')->getGenerated());
    }

    #[\Override]
    public function setUp(): void
    {
        parent::setUp();

        $locator = new ClassLocator((new Finder())->files()->in([\dirname(__DIR__, 4) . '/Fixtures/Uuid']));
        $reader = new AttributeReader();
        $this->tokenizer = new TokenizerEntityLocator($locator, $reader);
    }
}
