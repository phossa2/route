<?php

namespace Phossa2\Route\Collector;

use Phossa2\Route\Route;
use Phossa2\Route\Result;
use Phossa2\Route\Status;

/**
 * CollectorPPR test case.
 */
class CollectorPPRTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CollectorPPR
     */
    private $object;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new CollectorPPR();
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
     * Tests CollectorPPR->addRoute()
     *
     * @covers Phossa2\Route\Collector\CollectorPPR::addRoute()
     * @expectedException Phossa2\Route\Exception\LogicException
     * @expectedExceptionCode Phossa2\Route\Message\Message::RTE_ROUTE_DISALLOWED
     */
    public function testAddRoute()
    {
        $this->object->addRoute(new Route('GET', '/user', 2));
    }

    /**
     * test parameter capture
     *
     * @covers Phossa2\Route\Collector\CollectorPPR::matchRoute
     */
    public function testMatchRoute1()
    {
        $result = new Result('GET', '/controller/action/id/1/name/nick?a=b');

        if ($this->object->matchRoute($result)) {
            $this->assertEquals(Status::OK, $result->getStatus());
            $param = $result->getParameters();
            $this->assertEquals('b', $param['a']);
            $this->assertEquals('1', $param['id']);
            $this->assertEquals('nick', $param['name']);
            $this->assertEquals(['controller', 'action'], $result->getHandler());
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * Bad result
     *
     * @covers Phossa2\Route\Collector\CollectorPPR::matchRoute
     */
    public function testMatchRoute2()
    {
        $result = new Result('GET', '/controller/action/id/name/nick?a=b');
        $this->assertFalse($this->object->matchRoute($result));
        $this->assertEquals(Status::BAD_REQUEST, $result->getStatus());
    }
}
