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
 * ResultInterface
 *
 * Route matching result
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface ResultInterface
{
    /**
     * Get the URI PATH
     *
     * @return string
     * @access public
     */
    public function getPath()/*# : string */;

    /**
     * Get the HTTP method
     *
     * @return string
     * @access public
     */
    public function getMethod()/*# : string */;

    /**
     * Get status code
     *
     * @return int
     * @access public
     */
    public function getStatus()/*# : int */;

    /**
     * Set status code
     *
     * @param  int $status
     * @return $this
     * @access public
     */
    public function setStatus(/*# int */ $status);

    /**
     * Get parsed parameters
     *
     * @return array
     * @access public
     */
    public function getParameters()/*# : array */;

    /**
     * Set parameters
     *
     * @param  array $params
     * @return $this
     * @access public
     */
    public function setParameters(array $params);

    /**
     * Set handler
     *
     * @param  mixed $handler
     * @return $this
     * @access public
     */
    public function setHandler($handler);

    /**
     * Get the handler (or pseudo callable)
     *
     * @return mixed
     * @access public
     */
    public function getHandler();

    /**
     * Set the matched route
     *
     * @param  RouteInterface $route
     * @return $this
     * @access public
     */
    public function setRoute(RouteInterface $route);

    /**
     * Get matched route
     *
     * @return RouteInterface|null
     * @access public
     */
    public function getRoute();
}
