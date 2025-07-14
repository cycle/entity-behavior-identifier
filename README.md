# Cycle ORM Entity Behavior Identifier
[![Latest Stable Version](https://poser.pugx.org/cycle/entity-behavior-Identifier/version)](https://packagist.org/packages/cycle/entity-behavior-Identifier)
[![Build Status](https://github.com/cycle/entity-behavior-Identifier/workflows/build/badge.svg)](https://github.com/cycle/entity-behavior-Identifier/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cycle/entity-behavior-Identifier/badges/quality-score.png?b=1.x)](https://scrutinizer-ci.com/g/cycle/entity-behavior-Identifier/?branch=1.x)
[![Codecov](https://codecov.io/gh/cycle/entity-behavior-Identifier/graph/badge.svg)](https://codecov.io/gh/cycle/entity-behavior)
<a href="https://discord.gg/TFeEmCs"><img src="https://img.shields.io/badge/discord-chat-magenta.svg"></a>

The package provides the ability to use `ramsey/identifier` as various Cycle ORM entity column types.

## Installation

Install this package as a dependency using Composer.

```bash
composer require cycle/entity-behavior-Identifier
```

## Example

They are randomly-generated and do not contain any information about the time they are created or the machine that
generated them.

```php
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Idetifier\Uuid4;
use Ramsey\Identifier\Uuid;

#[Entity]
#[Uuid4]
class User
{
    #[Column(field: 'uuid', type: 'uuid', primary: true)]
    private Uuid $uuid;
}
```

You can find more information about Entity behavior UUID [here](https://cycle-orm.dev/docs/entity-behaviors-identifier).

## License:

The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information.
Maintained by [Spiral Scout](https://spiralscout.com).
