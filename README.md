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

  Extensions can be either a `Phossa2\Event\EventableExtensionAbstract` and added
  with `addExtension()` or `addExt()`, or a callable with signature of
  `callableName(Phossa2\Event\EventInterface $event): bool` which can be added
  as extension via `addExt(callable, eventName, priority)`.

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
    // add handler for unauthorized routing
    ->addHandler(
        function() {
            echo "need auth";
        }, Status::UNAUTHORIZED)

    // add a route
    ->addGet('/user/view/{id:d}', function() {
            echo "AUTHED!";
        })

    // add extension to force auth routes under '/user/'
    ->addExt(function($event) {
            $result = $event->getParam('result');
            $path = $result->getPath();
            if (!isset($_SESSION['authed']) && '/user/' === substr($path, 0, 6)) {
                $result->setStatus(Status::UNAUTHORIZED);
                return false;
            }
            return true;
        }, Dispatcher::EVENT_BEFORE_MATCH);

  // try a not authed route
  $dispatcher->dispatch('GET', '/user/view/123');

  // try a authed route
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

- **Extension events**

  Three types of events, dispatcher level, collector level and route level.
  List of all events in the order of execution.

  - `Dispatcher::EVENT_BEFORE_MATCH` before matching starts

    - `Collector::EVENT_BEFORE_MATCH` before matching in a collector

    - `Collector::EVENT_AFTER_MATCH` after a successful match in the collector

  - `Dispatcher::EVENT_AFTER_MATCH` after a successful match at dispatcher level

  - `Dispatcher::EVENT_BEFORE_DISPATCH` after a sucessful match, before
    dispatching to any handler

    - `Route::EVENT_BEFORE_HANDLER` before executing handler(route's or
       collector's) for this route

    - `Route::EVENT_AFTER_HANDLER` after handler successfully executed

  - `Dispatcher::EVENT_AFTER_DISPATCH` back to dispatcher level, after handler
    executed successfully

  - `Dispatcher::EVENT_BEFORE_HANDLER` match failed or no handler found for the
    matching route, before execute dispatcher's default handler

  - `Dispatcher::EVENT_AFTER_HANDLER` after dispatcher's default handler
    executed

<a name="debug"></a>Debugging
---

Sometimes, you need to know what went wrong.

```php
$dispatcher->enableDebug()->setDebugger($logger);
```

Where `$logger` is a PSR-3 compatible logger implmenting the interface
`Psr\Log\LoggerInterface`. The dispatcher will send logs of dispatching process
to the logger.

<a name="strategy"></a>Routing strategies
---

There are a couple of URL based routing strategies supported in this library.
Different strategy collectors can be combined together into one dispatcher.

- <a name="ppr"></a>**Parameter Pairs Routing (PPR)**

  Using parameter and value pairs for routing,

  ```
  http://servername/path/index.php/controller/action/id/1/name/nick
  ```

  Parameters order can be arbitary, but have to appear in pairs. Advantage of
  this scheme is fast and web crawler friendly. If URL rewriting is used, the
  above can be written into the following,

  ```
  http://servername/path/controller/action/id/1/name/nick
  ```

  Instead of using '/' as the parameter seperator, any URL valid characters
  except for the '?' and '&' can be used as a seperator.

  ```
  http://servername/path/controller-action-id-1-name-nick
  ```

  This strategy is implemented in `Phossa2\Route\Collector\CollectorPPR` class.

- **Query Parameter Routing (QPR)**

  The routing info is directly embedded in the URL query. The advantage of this
  scheme is fast and clear.

  ```
  http://servername/path/?r=controller-action-id-1-name-nick
  ```

  This strategy is implemented in `Phossa2\Route\Collector\CollectorQPR` class.

- **Regular Expression Routing (RER)**

  Regular expression based routing is the default routing strategy for this
  library and implemented in `Phossa2\Route\Collector\Collector` class.

  ```php
  // created with default RER collector
  $dispatcher = (new Dispatcher())
      ->addCollector(new Collector())     // regex based routing first
      ->addCollector(new CollectorQPR()); // support for legacy QPR
  ```

<a name="algorithm"></a>Regex matching algorithms
---

Different regex matching algorithms can be used with the RER collector.

- <a name="fastroute"></a>**FastRoute algorithm**

  This *Group Count Based algorithm* is implemented in
  `Phossa2\Route\Parser\ParserGcb` class and explained in  detail in this article
  ["Fast request routing using regular expressions"](http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html).

  phossa-route uses this algorithm by default.

- **Standard algorithm**

  This algorithm is developed by phossa2/route and a little bit slower than the
  fastRoute GCB algorithm. It is implemented in `Phossa2\Route\Parser\ParserStd`
  class.

  Use this standard algorithm,

  ```php
  use Phossa2\Route\Dispatcher;
  use Phossa2\Route\Parser\ParserStd;
  use Phossa2\Route\Collector\Collector;

  // use standard algorithm
  $dispatcher = new Dispatcher(new Collector(new ParserStd));
  ```

- **Comments on routing algorithms**

  - It does **NOT** matter that much as you may think.

    If you are using routing library in your application, different algorithms
    may differ only 0.1 - 0.2ms for a single request, which seems meaningless
    for an application unless you are using it as a standalone router.

  - If you **DO** care about routing speed

    Use different routing strategy like [*Parameter Pairs Routing (PPR)*](#ppr)
    which is [much faster](#performance) than the regex based routing. Also by
    carefully design your routes, you may achieve better results even if you
    are using a *slower* algorithm.

  - Try [network routing or server routing](#issue) if you just **CRAZY ABOUT
    THE SPEED**.

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

- phossa2/shared >= 2.0.27

License
---

[MIT License](http://mit-license.org/)

Appendix
---

- <a name="performance"></a>**Performance**

  - Worst-case matching

    This benchmark matches the last route and unknown route. It generates a
    randomly prefixed and suffixed route in an attempt to thwart any optimization.
    1,000 routes each with 8 arguments.

    This benchmark consists of 14 tests. Each test is executed 1,000 times, the
    results pruned, and then averaged. Values that fall outside of 3 standard
    deviations of the mean are discarded.

    ["Parameter Pairs Routing (PPR)"](#ppr) is fastest and used as baseline.

    Test Name | Results | Time | + Interval | Change
    --------- | ------- | ---- | ---------- | ------
    Phossa PPR - unknown route (1000 routes) | 998 | 0.0000724551 | +0.0000000000 | baseline
    Phossa PPR - last route (1000 routes) | 993 | 0.0000925307 | +0.0000200755 | 28% slower
    Symfony2 Dumped - unknown route (1000 routes) | 998 | 0.0004353616 | +0.0003629065 | 501% slower
    Phroute - last route (1000 routes) | 999 | 0.0006205601 | +0.0005481050 | 756% slower
    Phossa - unknown route (1000 routes) | 998 | 0.0006903790 | +0.0006179239 | 853% slower
    FastRoute - unknown route (1000 routes) | 1,000 | 0.0006911943 | +0.0006187392 | 854% slower
    FastRoute - last route (1000 routes) | 999 | 0.0006962751 | +0.0006238200 | 861% slower
    Phroute - unknown route (1000 routes) | 998 | 0.0007134676 | +0.0006410125 | 885% slower
    Symfony2 Dumped - last route (1000 routes) | 993 | 0.0008066097 | +0.0007341545 | 1013% slower
    Phossa - last route (1000 routes) | 998 | 0.0009104498 | +0.0008379947 | 1157% slower
    Symfony2 - unknown route (1000 routes) | 989 | 0.0023998006 | +0.0023273455 | 3212% slower
    Symfony2 - last route (1000 routes) | 999 | 0.0025880890 | +0.0025156339 | 3472% slower
    Aura v2 - last route (1000 routes) | 981 | 0.0966411463 | +0.0965686912 | 133281% slower
    Aura v2 - unknown route (1000 routes) | 992 | 0.1070026719 | +0.1069302168 | 147581% slower

  - First route matching

    This benchmark tests how quickly each router can match the first route. 1,000
    routes each with 8 arguments.

    This benchmark consists of 7 tests. Each test is executed 1,000 times, the
    results pruned, and then averaged. Values that fall outside of 3 standard
    deviations of the mean are discarded.

    **Note** Both *FastRoute* and *Phroute* implement a static route table, so
    they are fast at the first route matching (which is a static route)

    Test Name | Results | Time | + Interval | Change
    --------- | ------- | ---- | ---------- | ------
    FastRoute - first route | 999 | 0.0000403543 | +0.0000000000 | baseline
    Phroute - first route | 998 | 0.0000405911 | +0.0000002368 | 1% slower
    Symfony2 Dumped - first route | 999 | 0.0000590617 | +0.0000187074 | 46% slower
    Phossa PPR - first route | 977 | 0.0000678727 | +0.0000275184 | 68% slower
    Phossa - first route | 999 | 0.0000898475 | +0.0000494932 | 123% slower
    Symfony2 - first route | 998 | 0.0003983802 | +0.0003580259 | 887% slower
    Aura v2 - first route | 986 | 0.0004391784 | +0.0003988241 | 988% slower

- **URL rewrite**

  Setup URL rewriting to do routing with `index.php`

  - Apache `.htaccess` with `mod_rewrite` engine is on

    ```
    DirectorySlash Off
    Options -MultiViews
    DirectoryIndex index.php
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-l
    RewriteRule ^ index.php [QSA,L]
    ```

    and in your `httpd.conf` file to enable using of `.htaccess`

    ```
    <VirtualHost *:80>
      ServerAdmin me@mysite.com
      DocumentRoot "/path/www.mysite.com/public"
      ServerName mysite.com
      ServerAlias www.mysite.com

      <Directory "/path/www.mysite.com/public">
        Options -Indexes +FollowSymLinks +Includes
        AllowOverride All
        Order allow,deny
        Allow from all
      </Directory>
    </VirtualHost>
    ```

  - Nginx configration in `nginx.conf`

    ```
    server {
        listen       80;
        server_name  www.mysite.com mysite.com;
        root         /path/www.mysite.com/public;

        try_files $uri $uri/ /index.php$is_args$args;

        location /index.php {
            fastcgi_connect_timeout 3s;
            fastcgi_read_timeout 10s;
            include fastcgi.conf;
            fastcgi_pass 127.0.0.1:9000;
        }
    }
    ```

- <a name="issue"></a>**Routing issues**

  Base on the request informations, such as request device, source ip, request
  method etc., service provider may direct request to different hosts, servers,
  app modules or handlers.

  - *Network level routing*

    Common case, such as routing based on request's source ip, routes the
    request to a *NEAREST* server, this is common in content distribution
    network (CDN), and is done at network level.

  - *Web server routing*

    For performance reason, some of the simple routing can be done at web
    server level, such as using apache or ngix configs to do simple routing.

    For example, if your server goes down for maintenance, you may replace
    the `.htaccess` file as follows,

    ```
    DirectorySlash Off
    Options -MultiViews
    DirectoryIndex maintenance.php
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-l
    RewriteRule ^ maintenance.php [QSA,L]
    ```

  - *App level routing*

    It solves much more complicated issues, and much more flexible.

    Usually, routing is done at a single point `index.php`. All the requests
    are configured to be handled by this script first and routed to different
    routines.
