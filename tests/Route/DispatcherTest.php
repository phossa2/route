<?php

namespace Phossa2\Route;

use Phossa2\Route\Collector\Collector;
use Phossa2\Route\Resolver\ResolverSimple;
use Phossa2\Route\Parser\ParserStd;
use Phossa2\Route\Collector\CollectorPPR;
use Phossa2\Route\Extension\RedirectToHttps;
use Phossa2\Route\Extension\IdValidation;
use Phossa2\Route\Extension\CollectorStats;

/**
 * Dispatcher test case.
 */
class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Dispatcher
     */
    private $object;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new Dispatcher();
        $this->object->addRoute(
            new Route('GET', '/user[/{name:xd}]', function(Result $result) {
                $params = $result->getParameters();
                echo sprintf("%d: USER '%s'", $result->getStatus(), $params['name']);
                return true;
            })
        );
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->object = null;
        parent::tearDown();
    }

    /**
     * getPrivateProperty
     *
     * @param  string $propertyName
     * @return the property
     */
    public function getPrivateProperty($propertyName) {
        $reflector = new \ReflectionClass($this->object);
        $property  = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($this->object);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    protected function invokeMethod($methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass($this->object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($this->object, $parameters);
    }

    /**
     * Tests Dispatcher->__construct()
     *
     * @covers Phossa2\Route\Dispatcher::__construct()
     */
    public function test__construct()
    {
        $this->assertTrue($this->object->getCollectors()[0] instanceof Collector);
        $this->assertTrue($this->object->getResolver() instanceof ResolverSimple);

        // try set new values
        $collector = new Collector(new ParserStd());
        $resolver  = new ResolverSimple();
        $obj = new Dispatcher($collector, $resolver);

        $this->assertTrue($collector === $obj->getCollectors()[0]);
        $this->assertTrue($resolver === $obj->getResolver());
    }

    /**
     * Tests Dispatcher->match()
     *
     * @covers Phossa2\Route\Dispatcher::match()
     */
    public function testMatch()
    {
        // match
        if ($this->object->match('GET', '/user/phossa')) {
            $res1 = $this->object->getResult();
            $this->assertEquals(Status::OK, $res1->getStatus());
            $param = $res1->getParameters();
            $this->assertEquals('phossa', $param['name']);
        } else {
            throw new \Exception('bad');
        }

        // not match
        if (!$this->object->match('GET', '/user2/bingo')) {
            $res2 = $this->object->getResult();
            $this->assertEquals(Status::NOT_FOUND, $res2->getStatus());
        } else {
            throw new \Exception('bad');
        }

        // add another route
        $this->object->addRoute(new Route('GET,POST', '/user\d+[/{name:xd}]', 2));

        if ($this->object->match('GET', '/user2/bingo')) {
            $res3 = $this->object->getResult();
            $param = $res3->getParameters();
            $this->assertEquals('bingo', $param['name']);
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * Tests Dispatcher->dispatch(), route handler
     *
     * @covers Phossa2\Route\Dispatcher::dispatch
     */
    public function testDispatch1()
    {
        // route handler 200
        $this->expectOutputString("200: USER 'phossa'");
        $this->object->dispatch('GET', '/user/phossa');
    }

    /**
     * Tests Dispatcher->dispatch(), collector level handler
     *
     * @covers Phossa2\Route\Dispatcher::dispatch
     */
    public function testDispatch2()
    {
        $obj = new Dispatcher((new Collector())->addHandler(
            function(Result $result) {
                echo sprintf("%d: NOT FOUND", $result->getStatus());
                return true;
            }, Status::NOT_FOUND
        ));
        $obj->addRoute(new Route('GET', '/user[/{name:xd}]', null));

        $this->expectOutputString("404: NOT FOUND");
        $obj->dispatch('GET', '/user2/phossa');
    }

    /**
     * Tests Dispatcher->dispatch(), dispatcher level handler
     *
     * @covers Phossa2\Route\Dispatcher::dispatch
     */
    public function testDispatch3()
    {
        // add univeral handler
        $obj = (new Dispatcher())->addHandler(function(Result $result) {
            echo sprintf("%d: UNIVERSAL", $result->getStatus());
        }, 0);

        $obj->addRoute(new Route('GET', '/user[/{name:xd}]', null));
        $this->expectOutputString("200: UNIVERSAL");
        $obj->dispatch('GET', '/user/phossa');
    }

    /**
     * Tests Dispatcher->getResult()
     *
     * @covers Phossa2\Route\Dispatcher::getResult
     */
    public function testGetResult()
    {
        $this->assertNull($this->object->getResult());

        // create result by default
        $this->object->match('GET', '/user/xyz');
        $this->assertEquals('/user/xyz', $this->object->getResult()->getPath());
    }

    /**
     * Test multiple collectors
     *
     * @covers Phossa2\Route\Dispatcher::addCollector()
     */
    public function testAddCollector()
    {
        $obj = (new Dispatcher())->addHandler(function(Result $result) {
            echo $result->getPath();
        }, 0);

        $col1 = (new Collector())->addRoute(
            new Route('GET', '/user[/{name:xd}]', function(Result $result) {
                $params = $result->getParameters();
                echo sprintf("%d: USER '%s'", $result->getStatus(), $params['name']);
                return true;
            })
        );
        $col2 = new CollectorPPR();

        $obj->addCollector($col1)->addCollector($col2);

        $this->expectOutputString("200: USER 'phossa'/controller/action/id/1/name/nick");
        $obj->dispatch('GET', '/user/phossa');
        $obj->dispatch('GET', '/controller/action/id/1/name/nick?a=b');
    }

    /**
     * README example 1, simple usage
     *
     * @covers Phossa2\Route\Dispatcher::dispatch()
     */
    public function testDispatch10()
    {
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
        $this->expectOutputString("action is list");
        $dispatcher->dispatch('GET', '/blog/list/2016/05/01');
    }

    /**
     * README example 2, load routes from array
     *
     * @covers Phossa2\Route\Dispatcher::dispatch()
     */
    public function testDispatch11()
    {
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

        $this->expectOutputString("user id is 123456");
        $dispatcher->dispatch('GET', '/user/view/123456');
    }

    /**
     * README example 3, default value is pattern
     *
     * @covers Phossa2\Route\Dispatcher::dispatch()
     */
    public function testDispatch12()
    {
        $dispatcher = new Dispatcher();
        $this->expectOutputString("01");
        $pattern = '/blog[/{action:xd=list}][/{year:d=2016}[/{month:d=01}[/{date:d=01}]]]';

        $dispatcher->addGet($pattern, function($result) {
            $params = $result->getParameters();
            echo $params['month'];
        })->dispatch('GET', '/blog');
    }

    /**
     * README example 4, multiple collectors
     *
     * @covers Phossa2\Route\Dispatcher::dispatch()
     */
    public function testDispatch13()
    {
        $dispatcher = new Dispatcher();
        $collector_user = (new Collector())
            ->addGet('/user/list/{id:d}', 'handler1')
            ->addGet('/user/view/{id:d}', 'handler2')
            ->addPost('/user/new', 'handler3');

        // '/blog' related
        $collector_blog = (new Collector())
            ->addGet('/blog/list/{user_id:d}', 'handler4')
            ->addGet('/blog/read/{blog_id:d}', function($result) {
                $params = $result->getParameters();
                echo "blog id " . $params['blog_id'];
            });

        $this->expectOutputString("blog id 123");
        $dispatcher
            ->addCollector($collector_user)
            ->addCollector($collector_blog)
            ->dispatch('GET', '/blog/read/123');
    }

    /**
     * README example 5, dispatcher level handler
     *
     * @covers Phossa2\Route\Dispatcher::dispatch()
     */
    public function testDispatch14()
    {
        $dispatcher = new Dispatcher();

        $dispatcher->addHandler(
            function($result) {
                echo "method " . $result->getMethod() . " not allowed";
            },
            Status::METHOD_NOT_ALLOWED
        );

        $route = new Route('GET', '/user/{action:xd}/{id:d}',
            function($result) { // handler for Status::OK
                // ...
            }
        );

        $this->expectOutputString("method POST not allowed");
        $dispatcher
                ->addRoute($route)
                ->dispatch('POST', '/user/view/123');
    }

    /**
     * README example 6, global default handler
     *
     * @covers Phossa2\Route\Dispatcher::dispatch()
     */
    public function testDispatch15()
    {
        $dispatcher = new Dispatcher();

        // global default handler
        $dispatcher->addHandler(
            function($result) {
                echo "no other handler found";
            },
            0
        );

        $this->expectOutputString("no other handler found");
        $dispatcher->dispatch('GET', '/user/view/123');
    }

    /**
     * README example 7 use redirecto https extension
     *
     * @covers Phossa2\Route\Dispatcher::dispatch()
     */
    public function testDispatch16()
    {
        $dispatcher = new Dispatcher();

        $this->expectOutputString("redirect to https");

        // direct any HTTP request to HTTPS port before any routing
        $dispatcher
            ->addExtension(new RedirectToHttps())
            ->addHandler(
                function() {
                    echo "redirect to https";
                }, Status::MOVED_PERMANENTLY)
            ->dispatch('GET', '/user/view/123');
    }

    /**
     * README example 8 use user_auth extension
     *
     * @covers Phossa2\Route\Dispatcher::dispatch()
     */
    public function testDispatch17()
    {
        $dispatcher = new Dispatcher();

        $this->expectOutputString("need auth AUTHED!");

        $dispatcher
            ->addHandler(
                function() {
                    echo "need auth";
                }, Status::UNAUTHORIZED)
            ->addGet('/user/view/{id:d}', function() {
                    echo " AUTHED!";
                })
            ->addExt(function($event) {
                $result = $event->getParam('result');
                $path = $result->getPath();
                if (!isset($_SESSION['authed']) && '/user/' === substr($path, 0, 6)) {
                    $result->setStatus(Status::UNAUTHORIZED);
                    return false;
                }
                return true;
            }, Dispatcher::EVENT_BEFORE_MATCH);

        $dispatcher->dispatch('GET', '/user/view/123');

        // authed
        $_SESSION['authed'] = 1;
        $dispatcher->dispatch('GET', '/user/view/123');
    }

    /**
     * README example 9, id validation on a route
     *
     * @covers Phossa2\Route\Dispatcher::dispatch()
     */
    public function testDispatch18()
    {
        $dispatcher = (new Dispatcher())->addHandler(function($result) {
            $params = $result->getParameters();
            echo "invalid id " . $params['id'];
        }, Status::PRECONDITION_FAILED);

        $this->expectOutputString("invalid id 1000");

        // add extension to a route
        $route = (new Route('GET', '/user/{id:d}', function() {}))
            ->addExtension(new IdValidation());

        // will fail
        $dispatcher->addRoute($route)->dispatch('GET', '/user/1000');
    }

    /**
     * README example 10, stats on a collector
     *
     * @covers Phossa2\Route\Dispatcher::dispatch()
     */
    public function testDispatch19()
    {
        // add ext to collector
        $col = (new Collector())->addExtension(new CollectorStats());

        $dispatcher = (new Dispatcher($col))->addGet('/user/{id:d}', null);

        $this->expectOutputString("Total 2 Matched 1 (50.0%)");
        $dispatcher->dispatch('GET', '/user/1000');
        $dispatcher->dispatch('GET', '/article/1500');
        $col->getStats();
    }
}

