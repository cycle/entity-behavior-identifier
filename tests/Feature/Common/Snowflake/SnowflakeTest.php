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
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

final class SnowflakeTest extends IdentifierTestCase
{
    protected Registry $registry;
    protected TokenizerEntityLocator $tokenizer;

    #[Test]
    #[DataProvider('readersDataProvider')]
    public function testColumnExist(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(User::class)->getFields();

        Assert::true($fields->has('snowflake'));
        Assert::true($fields->hasColumn('snowflake'));
        Assert::same($fields->get('snowflake')->getType(), 'snowflake');
        Assert::array($fields->get('snowflake')->getTypecast());
        Assert::same($fields->get('snowflake')->getGenerated(), GeneratedField::BEFORE_INSERT);
        Assert::same($fields->count(), 1);
    }

    #[Test]
    #[DataProvider('readersDataProvider')]
    public function testAddColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(Post::class)->getFields();

        Assert::true($fields->has('customSnowflake'));
        Assert::true($fields->hasColumn('custom_snowflake'));
        Assert::same($fields->get('customSnowflake')->getType(), 'snowflake');
        Assert::same($fields->get('customSnowflake')->getGenerated(), GeneratedField::BEFORE_INSERT);
    }

    #[Test]
    #[DataProvider('readersDataProvider')]
    public function testMultipleSnowflake(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(MultipleSnowflake::class)->getFields();

        Assert::true($fields->has('snowflake'));
        Assert::true($fields->hasColumn('snowflake'));
        Assert::same($fields->get('snowflake')->getType(), 'snowflake');
        Assert::same($fields->get('snowflake')->getGenerated(), GeneratedField::BEFORE_INSERT);

        Assert::true($fields->has('discord'));
        Assert::true($fields->hasColumn('discord'));
        Assert::same($fields->get('discord')->getType(), 'snowflake');
        Assert::same($fields->get('discord')->getGenerated(), GeneratedField::BEFORE_INSERT);

        Assert::true($fields->has('instagram'));
        Assert::true($fields->hasColumn('instagram'));
        Assert::same($fields->get('instagram')->getType(), 'snowflake');
        Assert::same($fields->get('instagram')->getGenerated(), GeneratedField::BEFORE_INSERT);

        Assert::true($fields->has('mastodon'));
        Assert::true($fields->hasColumn('mastodon'));
        Assert::same($fields->get('mastodon')->getType(), 'snowflake');
        Assert::same($fields->get('mastodon')->getGenerated(), GeneratedField::BEFORE_INSERT);

        Assert::true($fields->has('twitter'));
        Assert::true($fields->hasColumn('twitter'));
        Assert::same($fields->get('twitter')->getType(), 'snowflake');
        Assert::same($fields->get('twitter')->getGenerated(), GeneratedField::BEFORE_INSERT);
    }

    #[Test]
    #[DataProvider('readersDataProvider')]
    public function testAddNullableColumn(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(NullableSnowflake::class)->getFields();

        Assert::true($fields->has('notDefinedSnowflake'));
        Assert::true($fields->hasColumn('not_defined_snowflake'));
        Assert::same($fields->get('notDefinedSnowflake')->getType(), 'snowflake');
        Assert::true($this->registry
            ->getTableSchema($this->registry->getEntity(NullableSnowflake::class))
            ->column('not_defined_snowflake')
            ->isNullable());
        Assert::null($fields->get('notDefinedSnowflake')->getGenerated());
    }

    #[BeforeTest]
    protected function setUp(): void
    {
        $locator = new ClassLocator((new Finder())->files()->in([\dirname(__DIR__, 3) . '/Fixtures/Snowflake']));
        $reader = new AttributeReader();
        $this->tokenizer = new TokenizerEntityLocator($locator, $reader);
    }
}
