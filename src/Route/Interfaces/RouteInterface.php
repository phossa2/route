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

use Phossa2\Route\Exception\LogicException;

/**
 * RouteInterface
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface RouteInterface
{
    /**
     * Set route matching pattern
     *
     * @param  string $pattern pattern to match
     * @return $this
     * @throws LogicException if pattern malformed
     * @access public
     * @api
     */
    public function setPattern(/*# string */ $pattern);

    /**
     * Get matching pattern
     *
     * @return string
     * @access public
     */
    public function getPattern()/*# : string */;

    /**
     * Set route http methods allowed, such as 'GET|HEAD|POST'
     *
     * @param  string|string[] $methods method to match
     * @return $this
     * @access public
     * @api
     */
    public function setMethods($methods);

    /**
     * Get allowed methods
     *
     * @return array
     * @access public
     */
    public function getMethods()/*# : array */;

    /**
     * Set default values for placeholders/parameters
     *
     * @param  array $values default values
     * @return $this
     * @access public
     */
    public function setDefault(array $values);

    /**
     * Get default values for placeholders
     *
     * @return array
     * @access public
     */
    public function getDefault()/*# : array */;
}
