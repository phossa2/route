# phossa2/route
[![Build Status](https://travis-ci.org/phossa2/route.svg?branch=master)](https://travis-ci.org/phossa2/route)
[![Code Quality](https://scrutinizer-ci.com/g/phossa2/route/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phossa2/route/)
[![PHP 7 ready](http://php7ready.timesplinter.ch/phossa2/route/master/badge.svg)](https://travis-ci.org/phossa2/route)
[![HHVM](https://img.shields.io/hhvm/phossa2/route.svg?style=flat)](http://hhvm.h4cc.de/package/phossa2/route)
[![Latest Stable Version](https://img.shields.io/packagist/vpre/phossa2/route.svg?style=flat)](https://packagist.org/packages/phossa2/route)
[![License](https://poser.pugx.org/phossa2/route/license)](http://mit-license.org/)

**phossa2/route** is a PHP library.

It requires PHP 5.4, supports PHP 7.0+ and HHVM. It is compliant with [PSR-1][PSR-1],
[PSR-2][PSR-2], [PSR-3][PSR-3], [PSR-4][PSR-4], and the proposed [PSR-5][PSR-5].

[PSR-1]: http://www.php-fig.org/psr/psr-1/ "PSR-1: Basic Coding Standard"
[PSR-2]: http://www.php-fig.org/psr/psr-2/ "PSR-2: Coding Style Guide"
[PSR-3]: http://www.php-fig.org/psr/psr-3/ "PSR-3: Logger Interface"
[PSR-4]: http://www.php-fig.org/psr/psr-4/ "PSR-4: Autoloader"
[PSR-5]: https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md "PSR-5: PHPDoc"

Installation
---
Install via the `composer` utility.

```bash
composer require "phossa2/route"
```

or add the following lines to your `composer.json`

```json
{
    "require": {
       "phossa2/route": "^2.0.0"
    }
}
```

Usage
---

Create the route instance,

```php
use Phossa2\Route\Route;

$route = new Route();
```

Features
---

- <a name="anchor"></a>**Feature One**


APIs
---

- <a name="api"></a>`LoggerInterface` related

Change log
---

Please see [CHANGELOG](CHANGELOG.md) from more information.

Testing
---

```bash
$ composer test
```

Contributing
---

Please see [CONTRIBUTE](CONTRIBUTE.md) for more information.

Dependencies
---

- PHP >= 5.4.0

- phossa2/event >= 2.1.5

- phossa2/shared >= 2.0.25

License
---

[MIT License](http://mit-license.org/)
