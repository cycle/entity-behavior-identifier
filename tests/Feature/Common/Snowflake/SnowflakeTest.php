<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Feature\Common\Snowflake;

use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Snowflake\MultipleSnowflake;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Snowflake\NullableSnowflake;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Snowflake\Post;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Snowflake\User;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Support\IdentifierTestCase;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\Schema\Registry;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

final class SnowflakeTest extends IdentifierTestCase
{
    protected Registry $registry;
    protected TokenizerEntityLocator $tokenizer;

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('readersDataProvider')]
    public function testColumnExist(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(User::class)->getFields();

        \Testo\Assert::true($fields->has('snowflake'));
        \Testo\Assert::true($fields->hasColumn('snowflake'));
        \Testo\Assert::same($fields->get('snowflake')->getType(), 'snowflake');
        \Testo\Assert::array($fields->get('snowflake')->getTypecast());
        \Testo\Assert::same($fields->get('snowflake')->getGenerated(), GeneratedField::BEFORE_INSERT);
        \Testo\Assert::same($fields->count(), 1);
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('readersDataProvider')]
    public function testAddColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(Post::class)->getFields();

        \Testo\Assert::true($fields->has('customSnowflake'));
        \Testo\Assert::true($fields->hasColumn('custom_snowflake'));
        \Testo\Assert::same($fields->get('customSnowflake')->getType(), 'snowflake');
        \Testo\Assert::same($fields->get('customSnowflake')->getGenerated(), GeneratedField::BEFORE_INSERT);
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('readersDataProvider')]
    public function testMultipleSnowflake(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(MultipleSnowflake::class)->getFields();

        \Testo\Assert::true($fields->has('snowflake'));
        \Testo\Assert::true($fields->hasColumn('snowflake'));
        \Testo\Assert::same($fields->get('snowflake')->getType(), 'snowflake');
        \Testo\Assert::same($fields->get('snowflake')->getGenerated(), GeneratedField::BEFORE_INSERT);

        \Testo\Assert::true($fields->has('discord'));
        \Testo\Assert::true($fields->hasColumn('discord'));
        \Testo\Assert::same($fields->get('discord')->getType(), 'snowflake');
        \Testo\Assert::same($fields->get('discord')->getGenerated(), GeneratedField::BEFORE_INSERT);

        \Testo\Assert::true($fields->has('instagram'));
        \Testo\Assert::true($fields->hasColumn('instagram'));
        \Testo\Assert::same($fields->get('instagram')->getType(), 'snowflake');
        \Testo\Assert::same($fields->get('instagram')->getGenerated(), GeneratedField::BEFORE_INSERT);

        \Testo\Assert::true($fields->has('mastodon'));
        \Testo\Assert::true($fields->hasColumn('mastodon'));
        \Testo\Assert::same($fields->get('mastodon')->getType(), 'snowflake');
        \Testo\Assert::same($fields->get('mastodon')->getGenerated(), GeneratedField::BEFORE_INSERT);

        \Testo\Assert::true($fields->has('twitter'));
        \Testo\Assert::true($fields->hasColumn('twitter'));
        \Testo\Assert::same($fields->get('twitter')->getType(), 'snowflake');
        \Testo\Assert::same($fields->get('twitter')->getGenerated(), GeneratedField::BEFORE_INSERT);
    }

    #[\Testo\Test]
    #[\Testo\Data\DataProvider('readersDataProvider')]
    public function testAddNullableColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(NullableSnowflake::class)->getFields();

        \Testo\Assert::true($fields->has('notDefinedSnowflake'));
        \Testo\Assert::true($fields->hasColumn('not_defined_snowflake'));
        \Testo\Assert::same($fields->get('notDefinedSnowflake')->getType(), 'snowflake');
        \Testo\Assert::true($this->registry
            ->getTableSchema($this->registry->getEntity(NullableSnowflake::class))
            ->column('not_defined_snowflake')
            ->isNullable());
        \Testo\Assert::null($fields->get('notDefinedSnowflake')->getGenerated());
    }

    #[\Testo\Lifecycle\BeforeTest]
    protected function setUp(): void
    {
        $locator = new ClassLocator((new Finder())->files()->in([\dirname(__DIR__, 3) . '/Fixtures/Snowflake']));
        $reader = new AttributeReader();
        $this->tokenizer = new TokenizerEntityLocator($locator, $reader);
    }
}
