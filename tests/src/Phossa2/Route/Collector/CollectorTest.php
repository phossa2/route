<?php

namespace Phossa2\Route\Collector;

use Phossa2\Route\Route;
use Phossa2\Route\Result;
use Phossa2\Route\Status;
use Phossa2\Route\Parser\ParserStd;

/**
 * Collector test case.
 */
class CollectorTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Collector
     */
    private $object;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new Collector();
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
     * Tests Collector->addRoute()
     *
     * @covers Phossa2\Route\Collector\Collector::addRoute()
     */
    public function testAddRoute1()
    {
        $route = new Route('GET|POST', '/usr[/{action:c=list}[/{id:d=2}]]', null);
        $this->object->addRoute($route);
        $this->assertTrue(1 === count($this->getPrivateProperty('routes')));
    }

    /**
     * Test route duplicated
     *
     * @covers Phossa2\Route\Collector\Collector::addRoute()
     * @expectedException Phossa2\Route\Exception\LogicException
     * @expectedExceptionCode Phossa2\Route\Message\Message::RTE_ROUTE_DUPLICATED
     */
    public function testAddRoute2()
    {
        $this->object->addRoute(new Route('GET|POST', '/', 1));
        $this->object->addRoute(new Route('POST', '/', 2));
    }

    /**
     * Tests Collector->loadRoutes()
     *
     * @covers Phossa2\Route\Collector\Collector::loadRoutes()
     */
    public function testLoadRoutes()
    {
        $routes = [
            '/user/{action:xd}/{id:d}' => [
                'GET,POST',               // methods,
                ['collecor', 'action'],   // handler,
                ['id' => 1]               // default values
            ],
            '/usr/local' => ['GET', null]
        ];
        $this->object->loadRoutes($routes);
        $this->assertTrue(2 === count($this->getPrivateProperty('routes')));
    }

    /**
     * Tests Collector->addGet()
     *
     * @covers Phossa2\Route\Collector\Collector::addGet()
     */
    public function testAddGet()
    {
        $this->object->addGet('/user/local', null, ['id' => 2]);
        $this->assertTrue(1 === count($this->getPrivateProperty('routes')));
    }

    /**
     * Tests Collector->addPost()
     *
     * @covers Phossa2\Route\Collector\Collector::addPost()
     */
    public function testAddPost()
    {
        $this->object->addPost('/user/local', null, ['id' => 2]);
        $this->assertTrue(1 === count($this->getPrivateProperty('routes')));
    }

    /**
     * test parameter capture
     *
     * @covers Phossa2\Route\Collector\Collector::matchRoute
     */
    public function testMatchRoute1()
    {
        $this->object->addRoute(new Route('GET,POST', '/user[/{name:c}]', 1));
        $result = new Result('GET', '/user/phossa');

        if ($this->object->matchRoute($result)) {
            $this->assertEquals(Status::OK, $result->getStatus());
            $this->assertEquals(['name' => 'phossa'], $result->getParameters());
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * test parameter capture, test ParserStd
     *
     * @covers Phossa2\Route\Collector\Collector::matchRoute
     */
    public function testMatchRoute11()
    {
        $this->object = new Collector(new ParserStd(['chunk' => 3]));
        $this->testMatchRoute1();
    }

    /**
     * test optional segment
     *
     * @covers Phossa2\Route\Collector\Collector::matchRoute
     */
    public function testMatchRoute2()
    {
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));

        $res1 = new Result('GET', '/blog/');

        if ($this->object->matchRoute($res1)) {
            $this->assertEquals(Status::OK, $res1->getStatus());
            $this->assertEquals(['section' => 'list'], $res1->getParameters());

        } else {
            throw new \Exception('bad');
        }

        $res2 = new Result('GET', '/blog/edit/');
        if ($this->object->matchRoute($res2)) {
            $this->assertEquals(Status::OK, $res2->getStatus());
            $this->assertEquals(['section' => 'edit'], $res2->getParameters());
        } else {
            throw new \Exception('bad');
        }

        $res3 = new Result('GET', '/blog/edit/2016');
        if ($this->object->matchRoute($res3)) {
            $this->assertEquals(Status::OK, $res3->getStatus());
            $this->assertEquals(
                ['section' => 'edit', 'year' => '2016'],
                $res3->getParameters()
            );
        } else {
            throw new \Exception('bad');
        }

        $res4 = new Result('GET', '/blog/2016');
        if ($this->object->matchRoute($res4)) {
            $this->assertEquals(Status::OK, $res4->getStatus());
            $this->assertEquals(
                ['section' => 'list', 'year' => '2016'],
                $res4->getParameters()
            );
        } else {
            throw new \Exception('bad');
        }

        $res5 = new Result('GET', '/blog/add/2016/04');
        if ($this->object->matchRoute($res5)) {
            $this->assertEquals(Status::OK, $res5->getStatus());
            $this->assertEquals(
                ['section' => 'add', 'year' => '2016', 'month' => '04'],
                $res5->getParameters()
            );
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * test optional segment with ParserStd
     *
     * @covers Phossa2\Route\Collector\Collector::matchRoute
     */
    public function testMatchRoute21()
    {
        $this->object = new Collector(new ParserStd(['chunk' => 3]));
        $this->testMatchRoute2();
    }

    /**
     * test regex combination
     *
     * @covers Phossa2\Route\Collector\Collector::matchRoute
     */
    public function testMatchRoute3()
    {
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog1[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog2[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog3[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog4[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog5[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog6[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog7[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));

        $res1 = new Result('GET', '/blog7/');

        if ($this->object->matchRoute($res1)) {
            $this->assertEquals(Status::OK, $res1->getStatus());
            $this->assertEquals(['section' => 'list'], $res1->getParameters());
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * test regex combination with ParserStd
     *
     * @covers Phossa2\Route\Collector\Collector::matchRoute
     */
    public function testMatchRoute31()
    {
        $this->object = new Collector(new ParserStd(['chunk' => 3]));
        $this->testMatchRoute3();
    }

    /**
     * test method not allowed
     *
     * @covers Phossa2\Route\Collector\Collector::matchRoute
     */
    public function testMatchRoute4()
    {
        $this->object->addRoute(new Route('GET,POST', '/user[/{name:c}]', 2));

        $result = new Result('HEAD', '/user/phossa');

        if (!$this->object->matchRoute($result)) {
            $this->assertEquals(Status::METHOD_NOT_ALLOWED, $result->getStatus());
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * test method not allowed with ParserStd
     *
     * @covers Phossa2\Route\Collector\Collector::matchRoute
     */
    public function testMatchRoute41()
    {
        $this->object = new Collector(new ParserStd(['chunk' => 3]));
        $this->testMatchRoute4();
    }

    /**
     * test not match
     *
     * @covers Phossa2\Route\Collector\Collector::matchRoute
     */
    public function testMatchRoute5()
    {
        $this->object->addRoute(new Route('GET,POST', '/user[/{name:c}]', 2));

        $result = new Result('GET', '/user1/phossa');

        if (!$this->object->matchRoute($result)) {
            $this->assertEquals(Status::NOT_FOUND, $result->getStatus());
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * test not match with ParserStd
     *
     * @covers Phossa2\Route\Collector\Collector::matchRoute
     */
    public function testMatchRoute51()
    {
        $this->object = new Collector(new ParserStd(['chunk' => 3]));
        $this->testMatchRoute5();
    }
}

