# phossa2/route
[![Build Status](https://travis-ci.org/phossa2/route.svg?branch=master)](https://travis-ci.org/phossa2/route)
[![Code Quality](https://scrutinizer-ci.com/g/phossa2/route/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phossa2/route/)
[![PHP 7 ready](http://php7ready.timesplinter.ch/phossa2/route/master/badge.svg)](https://travis-ci.org/phossa2/route)
[![HHVM](https://img.shields.io/hhvm/phossa2/route.svg?style=flat)](http://hhvm.h4cc.de/package/phossa2/route)
[![Latest Stable Version](https://img.shields.io/packagist/vpre/phossa2/route.svg?style=flat)](https://packagist.org/packages/phossa2/route)
[![License](https://poser.pugx.org/phossa2/route/license)](http://mit-license.org/)

**phossa2/route** is a *fast*, *full-fledged* and *feature-rich* application
level routing library for PHP.

It requires PHP 5.4, supports PHP 7.0+ and HHVM. It is compliant with [PSR-1][PSR-1],
[PSR-2][PSR-2], [PSR-3][PSR-3], [PSR-4][PSR-4], and the proposed [PSR-5][PSR-5].

[PSR-1]: http://www.php-fig.org/psr/psr-1/ "PSR-1: Basic Coding Standard"
[PSR-2]: http://www.php-fig.org/psr/psr-2/ "PSR-2: Coding Style Guide"
[PSR-3]: http://www.php-fig.org/psr/psr-3/ "PSR-3: Logger Interface"
[PSR-4]: http://www.php-fig.org/psr/psr-4/ "PSR-4: Autoloader"
[PSR-5]: https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md "PSR-5: PHPDoc"

Why another routing library ?
---

- [Super fast](#performance) ! If it matters to you.

- Support different [routing strategies](#strategy) and combinations of these
  strategies.

- Support different [regular expression routing algorithms](#algorithm)
  including the [fastRoute algorithm](#fastroute)

- [Concise route syntax](#syntax). Route parameters and optional route segments.

- [Multiple routing collections](#collector) allowed.

- Different level of [default handlers](#default).

- Fine control of routing process by [multiple level of extensions](#extension).

- Route and regex [debugging](#debug).

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

<a name="usage"></a>Usage
---

Inject route definitions (pattern, handler, default values etc.) into the
dispatcher and then call either `match()` or `dispatch()`.

```php
use Phossa2\Route\Route;
use Phossa2\Route\Dispatcher;

// dispatcher with default collector & resolver
$dispatcher = (new Dispatcher())
    ->addGet(
        '/blog/{action:xd}[/{year:d}[/{month:d}[/{date:d}]]]',
        function($result) {
            $params = $result->getParameters();
            echo "action is " . $params['action'];
        }
    )->addPost(
        '/blog/post',
        'handler2'
    )->addRoute(new Route(
        'GET,HEAD',
        '/blog/read[/{id:d}]',
        'handler3',
        ['id' => '1'] // default values
    ));

// diaptcher (match & execute controller action)
$dispatcher->dispatch('GET', '/blog/list/2016/05/01');
```

Or load routes from an array,

```php
$routes = [
    '/user/{action:xd}/{id:d}' => [
        'GET,POST',               // methods,
        function ($result) {
            $params = $result->getParameters();
            echo "user id is " . $params['id'];
        },                        // handler,
        ['id' => 1]               // default values
    ],
    // ...
];
$dispatcher = (new Dispatcher())->loadRoutes($routes);
$dispatcher->dispatch('GET', '/user/view/123456');
```

<a name="syntax"></a>Route syntax
---

- **{Named} parameters**

  A route pattern syntax is used where `{foo}` specifies a named parameter or
  a placeholder with name `foo` and default regex pattern `[^/]++`. In order to
  match more specific types, you may specify a custom regex pattern like
  `{foo:[0-9]+}`.

  ```php
  // with 'action' & 'id' two named params
  $dispatcher->addGet('/user/{action:[^0-9/][^/]*}/{id:[0-9]+}', 'handler1');
  ```

  Predefined shortcuts can be used for placeholders as follows,

  ```php
  ':d}'   => ':[0-9]++}',             // digit only
  ':l}'   => ':[a-z]++}',             // lower case
  ':u}'   => ':[A-Z]++}',             // upper case
  ':a}'   => ':[0-9a-zA-Z]++}',       // alphanumeric
  ':c}'   => ':[0-9a-zA-Z+_\-\.]++}', // common chars
  ':nd}'  => ':[^0-9/]++}',           // not digits
  ':xd}'  => ':[^0-9/][^/]*+}',       // no leading digits
  ```

  The previous pattern can be rewritten into,

  ```php
  // with 'action' & 'id' two named params
  $dispatcher->addGet('/user/{action:xd}/{id:d}', 'handler1');
  ```

- **[Optional] segments**

  Optional segments in the route pattern can be specified with `[]` as follows,

  ```php
  // $action, $year/$month/$date are all optional
  $pattern = '/blog[/{action:xd}][/{year:d}[/{month:d}[/{date:d}]]]';
  ```

  where optional segments can be **NESTED**. Unlike other libraries, optional
  segments are not limited to the end of the pattern, as long as it is a valid
  pattern like the `[/{action:xd}]` in the example.

- **Syntax limitations**

  - Parameter name *MUST* start with a character

    Since `{2}` has special meanings in regex. Parameter name *MUST* start with
    a character. And the use of `{}` inside/outside placeholders may cause
    confusion, thus is not recommended.

  - `[]` outside placeholder means *OPTIONAL* segment only

    `[]` can not be used outside placeholders as part of a regex pattern, *IF
    YOU DO NEED* to use them as part of the regex pattern, please include them
    *INSIDE* a placeholder.

  - Use of capturing groups `()` inside placeholders is not allowed

    Capturing groups `()` can not be used inside placeholders. For example
    `{user:(root|phossa)}` is not valid. Instead, you can use either use
    `{user:root|phossa}` or `{user:(?:root|phossa)}`.

- **Default Values**

  Default values can be added to named parameters at the end in the form of
  `{action:xd=list}`. Default values have to be alphanumeric chars. For example,

  ```php
  // $action, $year/$month/$date are all optional
  $pattern = '/blog[/{action:xd=list}][/{year:d=2016}[/{month:d=01}[/{date:d=01}]]]';
  $dispatcher->addGet($pattern, function($result) {
      $params = $result->getParameters();
      echo $params['year'];
  })->dispatch('GET', '/blog');
  ```

<a name="routes"></a>Routes
---

- **Defining routes with dispatcher**

  You may define routes with dispatcher. But, it is actually defining routes
  with the first route collector in the dispatcher.

  ```php
  // a new route collector will be added automatically if not yet
  $dispatcher = (new Dispatcher())->addPost('/blog/post', 'handler2');
  ```

  `addGet()` and `addPost()` are wrappers of `addRoute(RouteInterface)`.

- **<a name="collector"></a>Multiple routing collectors**

  Routes can be grouped into different collections by using multiple collectors.

  ```php
  use Phossa2\Route\Collector\Collector;

  // '/user' related
  $collector_user = (new Collector())
      ->addGet('/user/list/{id:d}', 'handler1')
      ->addGet('/user/view/{id:d}', 'handler2')
      ->addPost('/user/new', 'handler3');

  // '/blog' related
  $collector_blog = (new Collector())
      ->addGet('/blog/list/{user_id:d}', 'handler4')
      ->addGet('/blog/read/{blog_id:d}', 'handler5');

  $dispatcher->addCollector($collector_user)
             ->addCollector($collector_blog);
  ```

- **Same route pattern**

  User can define same route pattern with different http methods.

  ```php
  $dispatcher
      ->addGet('/user/{$id}', 'handler1')
      ->addPost('/user/{$id}', 'handler2');
  ```

<a name="dispatch"></a>Dispatching
---

- **Dispatch with dispatcher's `dispatch()`**

  ```php
  $dispatcher->dispatch('GET', '/user/view/123');
  ```

- **Match instead of dispatching**

  Instead of executing handler by default in `dispatch()`, more control by
  user if using the `match()` method

  ```php
  if ($dispatcher->match('GET', '/user/view/1234')) {
      $result = $dispatcher->getResult();
      switch($result->getStatus()) {
          case 200:
            // ...
            break;
          case 404:
            // ...
            break;
          default:
            // ...
            break;
      }
  } else {
      // no match found
      // ...
  }
  ```
<a name="handler"></a>Handlers
---

- **Route handler**

  Route is defined with a handler for status `200 OK` only.

  ```php
  use Phossa2\Route\Route;
  use Phossa2\Route\Status;

  $route = new Route(
      'GET',
      '/user/{action:xd}/{id:d}',
      function($result) { // handler for Status::OK
          // ...
      }
  );
  ```

- **<a name="default"></a>Default handlers**

  Dispatcher and collectors can have multiple handlers corresponding to
  different result status.

  If the result has no handler set (for example, no match found), then the
  collector's handler(same status code) will be retrieved. If still no luck,
  the dispatcher's handler (same status code) will be used if defined.

  Dispatcher-level handlers,

  ```php
  use Phossa2\Route\Status;

  $dispatcher->addHandler(
      function($result) {
          echo "method " . $result->getMethod() . " not allowed";
      },
      Status::METHOD_NOT_ALLOWED
  );
  ```

  Collector-level handlers,

  ```php
  $collector->addHandler(
      function($result) {
          // ...
      },
      Status::MOVED_PERMANENTLY
  );
  ```

  When `addHandler()` with status set to `0` will cause this handler be the
  default handler for other status.

  ```php
  use Phossa2\Route\Status;

  $dispatcher->addHandler(
      function($result) {
          echo "no other handler found";
      },
      0 // <-- match all other status
  );
  ```

- **Handler resolving**

  Most of the time, matching route will return a handler like
  `[ 'ControllerName', 'actionName' ]`. Handler resolver can be used to
  resolving this pseudo handler into a real callable.

  ```php
  use Phossa2\Route\Collector\Collector;
  use Phossa2\Route\Resolver\ResolverSimple;

  // dispatcher with default resolver
  $dispatcher = new Route\Dispatcher(
      new Collector(),
      new ResolverSimple() // the default resolver anyway
  );
  ```

  Users may write their own handler resolver by implementing
  `Phossa2\Route\Interfaces\ResolverInterface`.

<a name="extension"></a>Extensions
---

Extensions are callables dealing with the matching result or other tasks before
or after certain dispatching stages.

Extensions can be added to `Dispatcher`, `Collector` or even `Route`.

- **Use of extensions**

  Extensions **MUST** return a boolean value to indicate wether to proceed with
  the dispatching process or not. `FALSE` means stop and returns to top level.

  ```php
  use Phossa2\Route\Status;
  use Phossa2\Route\Dispatcher;
  use Phossa2\Route\Extensions\RedirectToHttps;

  // create dispatcher
  $dispatcher = new Dispatcher();

  // direct any HTTP request to HTTPS port before any routing
  $dispatcher
      ->addExtension(new RedirectToHttps())
      ->addHandler(function() {
            echo "redirect to https";
        }, Status::MOVED_PERMANENTLY)
      ->dispatch('GET', '/user/view/123');
  ```

  Force authentication for any '/user/' prefixed URL,

  ```php
  use Phossa2\Route\Status;
  use Phossa2\Route\Dispatcher;
  use Phossa2\Route\Extensions\UserAuth;

  $dispatcher = new Dispatcher();

  $dispatcher
    ->addExtension(new UserAuth())
    ->addHandler(
        function() {
            echo "need auth";
        }, Status::UNAUTHORIZED)
    ->addGet('/user/view/{id:d}', function() {
            echo "AUTHED!";
        });

  // not authed
  $dispatcher->dispatch('GET', '/user/view/123');

  // authed
  $_SESSION['authed'] = 1;
  $dispatcher->dispatch('GET', '/user/view/123');
  ```

- **Examples of extension**

  Validation of a parameter value on a route,

  ```php
  use Phossa2\Route\Status;
  use Phossa2\Route\Dispatcher;
  use Phossa2\Route\Extensions\IdValidation;

  $dispatcher = new Dispatcher();

  // add extension to a route
  $route = (new Route('GET', '/user/{id:d}', null))
    ->addExtension(new IdValidation());

  // will fail
  $dispatcher->addRoute($route)->dispatch('GET', '/user/1000');
  ```

  Statistics for a route collector,

  ```php
  $collector->addExtension(
      function($result) {
          // collect statistics
      },
      Collector::BEFORE_COLL // before collector match
  )->addExtension(
      function($result) {
          // collect statistics
      },
      Collector::AFTER_COLL // after a successful match
  );
  ```

- **Extension stages**

  Three types of stages, dispatcher level, collector level and route level. List
  of all stages in the order of execution.

  - `Dispatcher::BEFORE_MATCH` before matching starts

    - `Collector::BEFORE_COLL` before matching in a collector

    - `Collector::AFTER_COLL` after a successful match in the collector

  - `Dispatcher::AFTER_MATCH` after a successful match at dispatcher level

  - `Dispatcher::BEFORE_DISPATCH` after a sucessful match, before dispatching
    to any handler

    - `Route::BEFORE_ROUTE` before executing handler(route's or collector's) for
       this route

    - `Route::AFTER_ROUTE` after handler successfully executed

  - `Dispatcher::AFTER_DISPATCH` back to dispatcher level, after handler
    executed successfully

  - `Dispatcher::BEFORE_DEFAULT` match failed or no handler found for the
    matching route, before execute dispatcher's default handler

  - `Dispatcher::AFTER_DEFAULT` after dispatcher's default handler executed

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
