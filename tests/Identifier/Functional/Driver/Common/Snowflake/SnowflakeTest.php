<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Snowflake;

use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Snowflake\MultipleSnowflake;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Snowflake\NullableSnowflake;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Snowflake\Post;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Snowflake\User;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\Schema\Registry;
use Ramsey\Identifier\SnowflakeFactory;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

abstract class SnowflakeTest extends BaseTest
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

        $this->assertTrue($fields->has('snowflake'));
        $this->assertTrue($fields->hasColumn('snowflake'));
        $this->assertSame('snowflake', $fields->get('snowflake')->getType());
        $this->assertIsArray($fields->get('snowflake')->getTypecast());
        $this->assertInstanceOf(SnowflakeFactory::class, $fields->get('snowflake')->getTypecast()[0]);
        $this->assertSame('createFromInteger', $fields->get('snowflake')->getTypecast()[1]);
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('snowflake')->getGenerated());
        $this->assertSame(1, $fields->count());
    }

    /**
     * @dataProvider readersDataProvider
     */
    public function testAddColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(Post::class)->getFields();

        $this->assertTrue($fields->has('customSnowflake'));
        $this->assertTrue($fields->hasColumn('custom_snowflake'));
        $this->assertSame('snowflake', $fields->get('customSnowflake')->getType());
        $this->assertInstanceOf(SnowflakeFactory::class, $fields->get('customSnowflake')->getTypecast()[0] ?? null);
        $this->assertSame('createFromInteger', $fields->get('customSnowflake')->getTypecast()[1] ?? null);
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('customSnowflake')->getGenerated());
    }

    /**
     * @dataProvider readersDataProvider
     */
    public function testMultipleSnowflake(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(MultipleSnowflake::class)->getFields();

        $this->assertTrue($fields->has('snowflake'));
        $this->assertTrue($fields->hasColumn('snowflake'));
        $this->assertSame('snowflake', $fields->get('snowflake')->getType());
        $this->assertInstanceOf(SnowflakeFactory::class, $fields->get('snowflake')->getTypecast()[0] ?? null);
        $this->assertSame('createFromInteger', $fields->get('snowflake')->getTypecast()[1] ?? null);
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('snowflake')->getGenerated());

        $this->assertTrue($fields->has('discord'));
        $this->assertTrue($fields->hasColumn('discord'));
        $this->assertSame('snowflake', $fields->get('discord')->getType());
        $this->assertInstanceOf(SnowflakeFactory::class, $fields->get('discord')->getTypecast()[0] ?? null);
        $this->assertSame('createFromInteger', $fields->get('discord')->getTypecast()[1] ?? null);
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('discord')->getGenerated());

        $this->assertTrue($fields->has('instagram'));
        $this->assertTrue($fields->hasColumn('instagram'));
        $this->assertSame('snowflake', $fields->get('instagram')->getType());
        $this->assertInstanceOf(SnowflakeFactory::class, $fields->get('instagram')->getTypecast()[0] ?? null);
        $this->assertSame('createFromInteger', $fields->get('instagram')->getTypecast()[1] ?? null);
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('instagram')->getGenerated());

        $this->assertTrue($fields->has('mastodon'));
        $this->assertTrue($fields->hasColumn('mastodon'));
        $this->assertSame('snowflake', $fields->get('mastodon')->getType());
        $this->assertInstanceOf(SnowflakeFactory::class, $fields->get('mastodon')->getTypecast()[0] ?? null);
        $this->assertSame('createFromInteger', $fields->get('mastodon')->getTypecast()[1] ?? null);
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('mastodon')->getGenerated());

        $this->assertTrue($fields->has('twitter'));
        $this->assertTrue($fields->hasColumn('twitter'));
        $this->assertSame('snowflake', $fields->get('twitter')->getType());
        $this->assertInstanceOf(SnowflakeFactory::class, $fields->get('twitter')->getTypecast()[0] ?? null);
        $this->assertSame('createFromInteger', $fields->get('twitter')->getTypecast()[1] ?? null);
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('twitter')->getGenerated());
    }

    /**
     * @dataProvider readersDataProvider
     */
    public function testAddNullableColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(NullableSnowflake::class)->getFields();

        $this->assertTrue($fields->has('notDefinedSnowflake'));
        $this->assertTrue($fields->hasColumn('not_defined_snowflake'));
        $this->assertSame('snowflake', $fields->get('notDefinedSnowflake')->getType());
        $this->assertInstanceOf(SnowflakeFactory::class, $fields->get('notDefinedSnowflake')->getTypecast()[0] ?? null);
        $this->assertSame('createFromInteger', $fields->get('notDefinedSnowflake')->getTypecast()[1] ?? null);
        $this->assertTrue(
            $this->registry
                ->getTableSchema($this->registry->getEntity(NullableSnowflake::class))
                ->column('not_defined_snowflake')
                ->isNullable(),
        );
        $this->assertNull($fields->get('notDefinedSnowflake')->getGenerated());
    }

    #[\Override]
    public function setUp(): void
    {
        parent::setUp();

        $locator = new ClassLocator((new Finder())->files()->in([\dirname(__DIR__, 4) . '/Fixtures/Snowflake']));
        $reader = new AttributeReader();
        $this->tokenizer = new TokenizerEntityLocator($locator, $reader);
    }
}
