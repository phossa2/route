<?php

namespace Phossa2\Route;

/**
 * Route test case.
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
    private $object;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new Route('GET', '/test', null);
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
     * Test addHandler
     *
     * @covers Phossa2\Route\Route::addHandler()
     */
    public function testAddHandler()
    {
        $this->object->addHandler('test', Status::METHOD_NOT_ALLOWED);
        $this->assertEquals(
            'test',
            $this->object->getHandler(Status::METHOD_NOT_ALLOWED)
        );
    }

    /**
     * Test getHandler
     *
     * @covers Phossa2\Route\Route::getHandler()
     */
    public function testGetHandler()
    {
        $this->assertTrue(is_null($this->object->getHandler(Status::OK)));
    }

    /**
     * Test getPattern
     *
     * @cover Phossa2\Route\Route::getPattern()
     */
    public function testGetPattern()
    {
        $this->assertEquals('/test', $this->object->getPattern());
    }

    /**
     * Test setPattern, non string
     *
     * @cover Phossa2\Route\Route::setPattern()
     * @expectedException Phossa2\Route\Exception\LogicException
     * @expectedExceptionCode Phossa2\Route\Message\Message::RTE_PATTERN_MALFORM
     *
     */
    public function testSetPattern1()
    {
        $this->object->setPattern(2);
    }

    /**
     * Test setPattern, non matching '[]'
     *
     * @cover Phossa2\Route\Route::setPattern()
     * @expectedException Phossa2\Route\Exception\LogicException
     * @expectedExceptionCode Phossa2\Route\Message\Message::RTE_PATTERN_MALFORM
     *
     */
    public function testSetPattern2()
    {
        $this->object->setPattern('/user/{action:[^0-9/][^/*}/{id:[0-9]+}');
    }

    /**
     * Test setMethods
     *
     * @cover Phossa2\Route\Route::setMethods()
     * @cover Phossa2\Route\Route::getMethods()
     */
    public function testSetMethods()
    {
        $res = ['GET', 'POST', 'HEAD'];

        $this->object->setMethods('get, post, head');
        $this->assertEquals($res, $this->object->getMethods());

        $this->object->setMethods(['get', 'post', 'head']);
        $this->assertEquals($res, $this->object->getMethods());
    }

    /**
     * Test setDefault
     *
     * @cover Phossa2\Route\Route::setDefault()
     */
    public function testSetDefault()
    {
        $res1 = ['a'=> 1, 'b'=> 2];
        $this->object->setDefault($res1);
        $this->assertEquals($res1, $this->object->getDefault());

        $res2 = ['c' => 3];
        $this->object->setDefault($res2);
        $this->assertEquals(
            ['a' => 1, 'b' => 2, 'c' => 3],
            $this->object->getDefault()
        );
    }

    /**
     * Test getDefault (default values in pattern)
     *
     * @cover Phossa2\Route\Route::getDefault()
     */
    public function testGetDefault()
    {
        $this->object->setPattern('/user/{action:[^0-9/][^/]*=view}/{id:[0-9]+=12}');
        $this->assertEquals(
            ['action' => 'view', 'id' => '12'],
            $this->object->getDefault()
        );
    }
}
