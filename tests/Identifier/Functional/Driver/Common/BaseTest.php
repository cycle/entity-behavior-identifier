<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Identifier\Tests\Functional\Driver\Common;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\EntityLocatorInterface;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
use Cycle\Database\Driver\DriverInterface;
use Cycle\Database\Driver\Handler;
use Cycle\ORM\Collection\ArrayCollectionFactory;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Traits\Loggable;
use Cycle\ORM\Entity\Behavior\Identifier\Tests\Utils\SimpleContainer;
use Cycle\ORM\EntityManager;
use Cycle\ORM\Factory;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\ORM;
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
use PHPUnit\Framework\TestCase;
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Composite\SelectiveReader;
use Spiral\Attributes\ReaderInterface;

abstract class BaseTest extends TestCase
{
    use Loggable;

    public const DRIVER = null;

    public static array $config;
    protected ?DatabaseManager $dbal = null;
    protected ?ORM $orm = null;
    protected ?DriverInterface $driver = null;
    private static array $driverCache = [];

    public static function readersDataProvider(): \Traversable
    {
        yield [new AnnotationReader()];
        yield [new AttributeReader()];
        yield [new SelectiveReader([new AttributeReader(), new AnnotationReader()])];
    }

    public function getDriver(): DriverInterface
    {
        if (isset(static::$driverCache[static::DRIVER])) {
            return static::$driverCache[static::DRIVER];
        }

        $config = self::$config[static::DRIVER];
        if (!isset($this->driver)) {
            $this->driver = $config->driver::create($config);
        }

        return static::$driverCache[static::DRIVER] = $this->driver;
    }

    public function withSchema(SchemaInterface $schema): ORM
    {
        $this->orm = new ORM(
            new Factory(
                $this->dbal,
                RelationConfig::getDefault(),
                null,
                new ArrayCollectionFactory(),
            ),
            $schema,
            new EventDrivenCommandGenerator($schema, new SimpleContainer()),
        );

        return $this->orm;
    }

    public function compileWithTokenizer(EntityLocatorInterface $tokenizer, ReaderInterface $reader): void
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

    public function setUp(): void
    {
        $this->dbal = new DatabaseManager(new DatabaseConfig());
        $this->dbal->addDatabase(
            new Database(
                'default',
                '',
                $this->getDriver(),
            ),
        );

        if (self::$config['debug'] ?? false) {
            $this->setUpLogger($this->getDriver());
            $this->enableProfiling();
        }
    }

    public function tearDown(): void
    {
        $this->dropDatabase($this->dbal->database('default'));

        $this->orm = null;
        $this->dbal = null;

        if (\function_exists('gc_collect_cycles')) {
            \gc_collect_cycles();
        }
    }

    protected function dropDatabase(?Database $database = null): void
    {
        if ($database === null) {
            return;
        }

        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();

            foreach ($schema->getForeignKeys() as $foreign) {
                $schema->dropForeignKey($foreign->getColumns());
            }

            $schema->save(Handler::DROP_FOREIGN_KEYS);
        }

        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();
            $schema->declareDropped();
            $schema->save();
        }
    }

    protected function getDatabase(): Database
    {
        return $this->dbal->database('default');
    }

    protected function save(object ...$entities): void
    {
        $em = new EntityManager($this->orm);
        foreach ($entities as $entity) {
            $em->persist($entity);
        }
        $em->run();
    }
}
