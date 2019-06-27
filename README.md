# HalfFlake

HalfFlake is a PHP library for distributive generating unique ID numbers using
Twitter's [Snowflake](https://github.com/twitter-archive/snowflake/blob/snowflake-2010/README.mkd) Algorithm.

## Installation

via Composer

``` bash
$ composer require real-digital/half-flake
```

via GitHub

``` bash
$ git clone https://github.com/real-digital/half-flake.git
```

### Usage

```php
<?php

use Real\HalfFlake;

// an unique pair of constants defined per node
const ID_DATACENTER = 1;
const ID_NODE = 1;

$shard = new HalfFlake\Seed(ID_DATACENTER, ID_NODE);
$clock = new HalfFlake\Time();

try {
    $generator = new HalfFlake\Generator($shard, $clock);
    $id = $generator->nextId();
} catch (HalfFlake\RuntimeException $e) {
    // handle errors
}
```

## Testing

```bash
composer test
```

## License

HalfFlake is licensed under the MIT License. Please see [LICENSE](LICENSE) for details.


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information.
