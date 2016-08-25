<?php
/**
 * Phossa Project
 *
 * PHP version 5.4
 *
 * @category  Library
 * @package   Phossa2\Route
 * @copyright Copyright (c) 2016 phossa.com
 * @license   http://mit-license.org/ MIT License
 * @link      http://www.phossa.com/
 */
/*# declare(strict_types=1); */

namespace Phossa2\Route\Interfaces;

/**
 * ParserInterface
 *
 * Route pattern parser
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface ParserInterface
{
    /**
     * Process a named route pattern as follows into regular expression
     *
     * '/blog/{section}[/{year:\d+}[/{month:\d+}[/{date:\d+}]]]'
     *
     * @param  string $routeName
     * @param  string $routePattern
     * @return string the result regex for the pattern
     * @access public
     */
    public function processRoute(
        /*# string */ $routeName,
        /*# string */ $routePattern
    )/*# : string */;

    /**
     * Match an URI path, return the matched route name and parameters
     *
     * @param  string $uriPath
     * @return array|false [ $routeName, $matchedParams ] or false
     * @access public
     */
    public function matchPath(/*# string */ $uriPath);
}
