<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\Combined;

use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Fixtures\Combined\MultipleIdentifiers;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Entity\Behavior\Identifier\Ulid;
use Cycle\ORM\Entity\Behavior\Identifier\Uuid;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\Schema\Registry;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

abstract class CombinedTest extends BaseTest
{
    protected Registry $registry;
    protected TokenizerEntityLocator $tokenizer;

    /**
     * @dataProvider readersDataProvider
     */
    public function testColumnsExist(ReaderInterface $reader): void
    {
        $this->compileWithTokenizer($this->tokenizer, $reader);

        $fields = $this->registry->getEntity(MultipleIdentifiers::class)->getFields();

        $this->assertSame(4, $fields->count());

        $this->assertTrue($fields->has('uuid'));
        $this->assertTrue($fields->hasColumn('uuid'));
        $this->assertSame('uuid', $fields->get('uuid')->getType());
        $this->assertSame([Uuid::class, 'create'], $fields->get('uuid')->getTypecast());
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('uuid')->getGenerated());

        $this->assertTrue($fields->has('uuidNullable'));
        $this->assertTrue($fields->hasColumn('uuid_nullable'));
        $this->assertSame('uuid', $fields->get('uuidNullable')->getType());
        $this->assertSame([Uuid::class, 'create'], $fields->get('uuidNullable')->getTypecast());
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('uuidNullable')->getGenerated());

        $this->assertTrue($fields->has('ulid'));
        $this->assertTrue($fields->hasColumn('ulid'));
        $this->assertSame('ulid', $fields->get('ulid')->getType());
        $this->assertSame([Ulid::class, 'create'], $fields->get('ulid')->getTypecast());
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('ulid')->getGenerated());

        $this->assertTrue($fields->has('ulidNullable'));
        $this->assertTrue($fields->hasColumn('ulid_nullable'));
        $this->assertSame('ulid', $fields->get('ulidNullable')->getType());
        $this->assertSame([Ulid::class, 'create'], $fields->get('ulidNullable')->getTypecast());
        $this->assertSame(GeneratedField::BEFORE_INSERT, $fields->get('ulidNullable')->getGenerated());
    }

    #[\Override]
    public function setUp(): void
    {
        parent::setUp();

        $locator = new ClassLocator((new Finder())->files()->in([\dirname(__DIR__, 4) . '/Fixtures/Combined']));
        $reader = new AttributeReader();
        $this->tokenizer = new TokenizerEntityLocator($locator, $reader);
    }
}
