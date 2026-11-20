<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Support;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\EntityLocatorInterface;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\ORM\Collection\ArrayCollectionFactory;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateModifiers;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderModifiers;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Cycle\Testing\Container\SimpleContainer;
use Cycle\Testing\ORM\OrmTestCase;
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Composite\SelectiveReader;
use Spiral\Attributes\ReaderInterface;

/**
 * An ORM whose command generator dispatches the identifier listeners, plus the annotated schema compiler.
 */
abstract class IdentifierTestCase extends OrmTestCase
{
    protected Registry $registry;

    public static function readersDataProvider(): \Traversable
    {
        yield [new AnnotationReader()];
        yield [new AttributeReader()];
        yield [new SelectiveReader([new AttributeReader(), new AnnotationReader()])];
    }

    protected function withSchema(SchemaInterface|array $schema): ORMInterface
    {
        $schema = \is_array($schema) ? new Schema($schema) : $schema;

        return $this->orm = new ORM(
            new Factory($this->dbal, RelationConfig::getDefault(), null, new ArrayCollectionFactory()),
            $schema,
            new EventDrivenCommandGenerator($schema, new SimpleContainer()),
        );
    }

    protected function compileWithTokenizer(EntityLocatorInterface $tokenizer, ReaderInterface $reader): void
    {
        (new Compiler())->compile($this->registry = new Registry($this->dbal), [
            new Entities($tokenizer, $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new MergeIndexes($reader),
            new GenerateRelations(),
            new GenerateModifiers(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new RenderModifiers(),
            new GenerateTypecast(),
        ]);
    }

    /**
     * Stops after the modifiers' compute phase, before render can fill in what compute left out.
     */
    protected function computeWithTokenizer(EntityLocatorInterface $tokenizer, ReaderInterface $reader): void
    {
        (new Compiler())->compile($this->registry = new Registry($this->dbal), [
            new Entities($tokenizer, $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateModifiers(),
        ]);
    }
}
