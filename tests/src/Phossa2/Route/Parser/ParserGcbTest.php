<?php

namespace Phossa2\Route\Parser;

/**
 * ParserGcb test case.
 */
class ParserGcbTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var ParserGcb
     */
    private $object;
    private $pattern;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new ParserGcb();
        $this->pattern = '/blog[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]';
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
     * getPrivateProperty
     *
     * @param 	string $propertyName
     * @return	the property
     */
    public function getPrivateProperty($propertyName) {
        $reflector = new \ReflectionClass($this->object);
        $property  = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($this->object);
    }

    /**
     * @covers Phossa2\Route\Parser\ParserGcb::convert
     */
    public function testConvert()
    {
        list(, $map) = $this->invokeMethod('convert', [ $this->pattern ]);
        $this->assertTrue(4 == count($map));
    }

    /**
     * @covers Phossa2\Route\Parser\ParserGcb::getMapData
     */
    public function testGetMapData()
    {
        $maps = [
            'a' => [ 'sec', 'name', 'key'],
            'b' => [ 'wow', 'bingo', 'yes'],
            'c' => [ 'yes', 'no' ],
            'd' => [ 'xox', 'yoy'],
            'e' => [ 'test', 'help']
        ];
        $arr  = [
            'a' => 'sdfadfssadfa',
            'b' => 'dssssx',
            'c' => 'ccccccc',
            'd' => 'dfadafadfa',
            'e' => 'sxxxx'
        ];

        $res = $this->invokeMethod('getMapData', [ $arr, $maps ]);
        $this->assertTrue(5 == $res['a']);
        $this->assertTrue(6 == $res['c']);
        $this->assertTrue(7 == $res['d']);
    }

    /**
     * @covers Phossa2\Route\Parser\ParserGcb::processRoute
     * @covers Phossa2\Route\Parser\ParserGcb::matchPath
     */
    public function testProcessRoute()
    {
        // parse route
        $p1 = '/user[/{name:c}]';
        $p2 = '/blog/{section:xd}[/{year:d}]';
        $p3 = '/news[/{year:d}[/{month:d}[/{date:d}]]]';
        $p4 = '/sport/{name:xd}/{season:xd}';
        $this->object->processRoute('p1', $p1);
        $this->object->processRoute('p2', $p2);
        $this->object->processRoute('p3', $p3);
        $this->object->processRoute('p4', $p4);

        // match
        $this->assertFalse($this->object->matchPath('/sport/bike'));

        list($r1, $a1) = $this->object->matchPath('/news/2016/12');
        $this->assertTrue('p3' === $r1);
        $this->assertEquals(['year' => '2016', 'month' => '12'], $a1);

        list($r2, $a2) = $this->object->matchPath('/user');
        $this->assertTrue('p1' === $r2);
        $this->assertEquals([], $a2);

        list($r3, $a3) = $this->object->matchPath('/blog/list');
        $this->assertTrue('p2' === $r3);
        $this->assertEquals(['section' => 'list'], $a3);

        list($r4, $a4) = $this->object->matchPath('/user/phossa');
        $this->assertTrue('p1' === $r4);
        $this->assertEquals(['name' => 'phossa'], $a4);
    }
}
