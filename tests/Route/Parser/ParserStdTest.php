<?php

namespace Phossa2\Route\Parser;

/**
 * ParserStd test case.
 */
class ParserStdTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var ParserStd
     */
    private $object;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new ParserStd();
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
     * @covers Phossa2\Route\Parser\ParserStd::processRoute
     */
    public function testProcessRoute1()
    {
        $pattern = '/blog[/{section}][/{year:d}[/{month:d}[/{date:d}]]]';
        $this->assertEquals(
            "/blog(?:/(?<section>[^/]++))?(?:/(?<year>[0-9]++)(?:/(?<month>[0-9]++)(?:/(?<date>[0-9]++))?)?)?",
            $this->object->processRoute('', $pattern));
    }

    /**
     * @covers Phossa2\Route\Parser\ParserStd::processRoute
     */
    public function testProcessRoute2()
    {
        $pattern = '/blog/{section:xd}/';
        $this->assertEquals("(?<wow>/blog/(?<sectionwow>[^0-9/][^/]*+))",
            $this->object->processRoute('wow', $pattern));
    }
}
