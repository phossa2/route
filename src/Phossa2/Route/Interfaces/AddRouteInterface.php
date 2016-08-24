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
 * AddRouteInterface
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface AddRouteInterface
{
    /**
     * Add one route
     *
     * @param  RouteInterface $route
     * @return $this
     * @access public
     * @api
     */
    public function addRoute(RouteInterface $route);

    /**
     * Add multiple routes from array, format is
     *
     * ```php
     * return [
     *     '/user/{action:xd}/{id:d}' => [
     *         'GET,POST',               // methods,
     *         ['collecor', 'action'],   // handler,
     *         ['id' => 1]               // default values
     *     ],
     *     ...
     * ];
     * ```
     *
     * @param  array $routes route definitions
     * @return $this
     * @throws LogicException if route malformed
     * @access public
     * @api
     */
    public function loadRoutes(array $routes);

    /**
     * Add a 'GET,HEAD' route
     *
     * @param  string $pattern
     * @param  mixed $handler
     * @param  array $defaultValues default values for placeholders
     * @return $this
     * @throws LogicException if pattern malformed
     * @access public
     * @api
     */
    public function addGet(
        /*# string */ $pattern,
        $handler,
        array $defaultValues = []
    );

    /**
     * Add a 'POST' route
     *
     * @param  string $pattern
     * @param  mixed $handler
     * @param  array $defaultValues default values for placeholders
     * @return $this
     * @access public
     * @api
     */
    public function addPost(
        /*# string */ $pattern,
        $handler,
        array $defaultValues = []
    );
}
