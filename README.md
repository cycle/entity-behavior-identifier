# Cycle ORM Entity Behavior Identifier
[![Latest Stable Version](https://poser.pugx.org/cycle/entity-behavior-identifier/version)](https://packagist.org/packages/cycle/entity-behavior-identifier)
[![Build Status](https://github.com/cycle/entity-behavior-identifier/workflows/build/badge.svg)](https://github.com/cycle/entity-behavior-identifier/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cycle/entity-behavior-identifier/badges/quality-score.png?b=1.x)](https://scrutinizer-ci.com/g/cycle/entity-behavior-identifier/?branch=1.x)
[![Codecov](https://codecov.io/gh/cycle/entity-behavior-identifier/graph/badge.svg)](https://codecov.io/gh/cycle/entity-behavior)
<a href="https://discord.gg/TFeEmCs"><img src="https://img.shields.io/badge/discord-chat-magenta.svg"></a>

The package provides the ability to use `ramsey/identifier` as various Cycle ORM entity column types.

## Installation

> **Note:** Due to a dependency on `ramsey/identifier` this package requires PHP `8.2` or newer.

Install this package as a dependency using Composer.

```bash
composer require cycle/entity-behavior-identifier
```

## Snowflake Examples

**Generic:** A flexible Snowflake format that can use a node identifier and any epoch offset, suitable for various applications requiring unique identifiers.

```php
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Snowflake;

#[Entity]
#[Identifier\SnowflakeGeneric(field: 'id', node: 1, epochOffset: 1738265600000)]
class User
{
    #[Column(type: 'snowflake', primary: true)]
    private Snowflake $id;
}
```

**Discord:** Snowflake identifier for Discord's platform (voice, text, video), starting from epoch `2015-01-01`. Can incorporate a worker and process ID's to generate distinct Snowflakes.

```php
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Snowflake;

#[Entity]
#[Identifier\SnowflakeDiscord(field: 'id', workerId: 12, processId: 24)]
class User
{
    #[Column(type: 'snowflake', primary: true)]
    private Snowflake $id;
}
```

**Instagram:** Snowflake identifier for Instagram's photo and video sharing platform, with an epoch starting at `2011-08-24`. Can incorporate a shard ID to generate distinct Snowflakes.

```php
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Snowflake;

#[Entity]
#[Identifier\SnowflakeInstagram(field: 'id', shardId: 16)]
class User
{
    #[Column(type: 'snowflake', primary: true)]
    private Snowflake $id;
}
```

**Mastodon:** Snowflake identifier for Mastodon’s decentralized social network, generated within a database to ensure uniqueness and approximate order within 1ms. Can include a table name for distinct sequences per table; IDs are unique on a single database but not guaranteed across multiple machines.

```php
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Snowflake;

#[Entity]
#[Identifier\SnowflakeMastodon(field: 'id', tableName: 'users')]
class User
{
    #[Column(type: 'snowflake', primary: true)]
    private Snowflake $id;
}
```

**Twitter:** Snowflake identifier for Twitter (X), beginning from `2010-11-04`. Can incorporate a machine ID to generate distinct Snowflakes.

```php
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Snowflake;

#[Entity]
#[Identifier\SnowflakeTwitter(field: 'id', machineId: 30)]
class User
{
    #[Column(type: 'snowflake', primary: true)]
    private Snowflake $id;
}
```

## ULID Examples

**ULID (Universally Unique Lexicographically Sortable Identifier):** A 128-bit identifier designed for high uniqueness and lexicographical sortability. It combines a timestamp component with random data, allowing for ordered IDs that can be generated rapidly and are human-readable, making it ideal for databases and distributed systems.

```php
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Ulid;

#[Entity]
#[Identifier\Ulid(field: 'id')]
class User
{
    #[Column(type: 'ulid', primary: true)]
    private Ulid $id;
}
```

## UUID Examples

**UUID Version 1 (Time-based):** Generated using the current timestamp and the MAC address of the computer, ensuring unique identification based on time and hardware.

```php
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Uuid;

#[Entity]
#[Identifier\Uuid1(field: 'id')]
class User
{
    #[Column(type: 'uuid', primary: true)]
    private Uuid $id;
}
```

**UUID Version 2 (DCE Security):** Similar to version 1 but includes a local identifier such as a user ID or group ID, primarily used in DCE security contexts.

```php
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Uuid;

#[Entity]
#[Identifier\Uuid2(field: 'id')]
class User
{
    #[Column(type: 'uuid', primary: true)]
    private Uuid $id;
}
```

**UUID Version 3 (Name-based, MD5):** Created by hashing a namespace identifier and name using MD5, resulting in a deterministic UUID based on input data.

```php
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Uuid;

#[Entity]
#[Identifier\Uuid3(
    field: 'id',
    namespace: '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
    name: 'example.com',
)]
class User
{
    #[Column(type: 'uuid', primary: true)]
    private Uuid $id;
}
```

**UUID Version 4 (Random):** Generated entirely from random or pseudo-random numbers, offering high unpredictability and uniqueness.

```php
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Uuid;

#[Entity]
#[Identifier\Uuid4(field: 'id')]
class User
{
    #[Column(type: 'uuid', primary: true)]
    private Uuid $id;
}
```

**UUID Version 5 (Name-based, SHA-1):** Similar to version 3 but uses SHA-1 hashing, providing a different deterministic UUID based on namespace and name.

```php
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Uuid;

#[Entity]
#[Identifier\Uuid5(
    field: 'id',
    namespace: '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
    name: 'example.com',
)]
class User
{
    #[Column(type: 'uuid', primary: true)]
    private Uuid $id;
}
```

**UUID Version 6 (Draft/Upcoming):** An experimental or proposed version focused on improving time-based UUIDs with more sortable properties (not yet widely adopted).

```php
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Uuid;

#[Entity]
#[Identifier\Uuid6(field: 'id')]
class User
{
    #[Column(type: 'uuid', primary: true)]
    private Uuid $id;
}
```

**UUID Version 7 (Draft/Upcoming):** A newer proposal designed to incorporate sortable features based on Unix timestamp, enhancing performance in database indexing.

```php
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Identifier;
use Ramsey\Identifier\Uuid;

#[Entity]
#[Identifier\Uuid7(field: 'id')]
class User
{
    #[Column(type: 'uuid', primary: true)]
    private Uuid $id;
}
```

You can find more information about Entity behavior Identifier [here](https://cycle-orm.dev/docs/entity-behaviors-identifier).

## License:

The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information.
Maintained by [Spiral Scout](https://spiralscout.com).
