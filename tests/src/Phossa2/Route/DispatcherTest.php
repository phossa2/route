<?php

namespace Phossa2\Route;

use Phossa2\Route\Collector\Collector;
use Phossa2\Route\Resolver\ResolverSimple;
use Phossa2\Route\Parser\ParserStd;

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
}

