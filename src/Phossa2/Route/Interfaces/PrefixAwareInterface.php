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
 * PrefixAwareInterface
 *
 * Make collector aware of path prefix. If prefix not match, skip this
 * collector right way instead of loop thru all the routes in the collector
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.1
 * @since   2.0.1 added
 */
interface PrefixAwareInterface
{
    /**
     * Add path prefix to the collector
     *
     * @param  string $pathPrefix
     * @return $this
     * @access public
     * @api
     */
    public function setPathPrefix(/*# string */ $pathPrefix = null);

    /**
     * Matching with collector's path prefix
     *
     * @param  string $uriPath
     * @return bool
     * @access public
     * @api
     */
    public function matchPathPrefix(/*# string */ $uriPath)/*# : bool */;
}
