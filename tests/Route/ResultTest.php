<?php

namespace Phossa2\Route;

/**
 * Result test case.
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Result
     */
    private $object;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new Result('POST', '/user/list/1');
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
     * Tests Result->getPath()
     *
     * @covers Phossa2\Route\Result::getPath()
     */
    public function testGetPath()
    {
        $this->assertEquals('/user/list/1', $this->object->getPath());
    }

    /**
     * Tests Result->getMethod()
     *
     * @covers Phossa2\Route\Result::getMethod()
     */
    public function testGetMethod()
    {
        $this->assertEquals('POST', $this->object->getMethod());
    }

    /**
     * Tests Result->getStatus()
     *
     * @covers Phossa2\Route\Result::getStatus()
     */
    public function testGetStatus()
    {
        $this->assertEquals(Status::NOT_FOUND, $this->object->getStatus());
    }

    /**
     * Tests Result->setStatus()
     *
     * @covers Phossa2\Route\Result::setStatus()
     */
    public function testSetStatus()
    {
        $this->object->setStatus(Status::MOVED_PERMANENTLY);
        $this->assertEquals(Status::MOVED_PERMANENTLY, $this->object->getStatus());
    }

    /**
     * Tests Result->getParameters()
     *
     * @covers Phossa2\Route\Result::getParameters()
     */
    public function testGetParameters()
    {
        $this->assertEquals([], $this->object->getParameters());
        $this->object = new Result('POST', '/user/list/1?b=1');
        $this->assertEquals(['b' => '1'], $this->object->getParameters());
    }

    /**
     * Tests Result->setParameters()
     *
     * @covers Phossa2\Route\Result::setParameters()
     */
    public function testSetParameters()
    {
        $res = ['a' => 10];
        $this->object->setParameters($res);
        $this->assertEquals($res, $this->object->getParameters());
    }

    /**
     * Tests Result->setHandler()
     *
     * @covers Phossa2\Route\Result::setHandler()
     */
    public function testSetHandler()
    {
        $this->object->setHandler(123);
        $this->assertEquals(123, $this->object->getHandler());
    }

    /**
     * Tests Result->getHandler()
     *
     * @covers Phossa2\Route\Result::getHandler()
     */
    public function testGetHandler()
    {
        $this->assertNull($this->object->getHandler());
    }

    /**
     * Tests Result->setRoute()
     *
     * @covers Phossa2\Route\Result::setRoute()
     */
    public function testSetRoute()
    {
        $route = new Route('GET', '/user', null);
        $this->object->setRoute($route);
        $this->assertTrue($route === $this->object->getRoute());
    }

    /**
     * Tests Result->getRoute()
     *
     * @covers Phossa2\Route\Result::getRoute()
     */
    public function testGetRoute()
    {
        $this->assertNull($this->object->getRoute());
    }
}

