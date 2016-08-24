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

namespace Phossa2\Route\Traits;

use Phossa2\Route\Route;
use Phossa2\Route\Interfaces\RouteInterface;
use Phossa2\Route\Interfaces\AddRouteInterface;

/**
 * AddRouteTrait
 *
 * Implementation of AddRouteInterface
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     AddRouteInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
trait AddRouteTrait
{
    /**
     * {@inheritDoc}
     */
    abstract public function addRoute(RouteInterface $route);

    /**
     * {@inheritDoc}
     */
    public function loadRoutes(array $routes)
    {
        foreach ($routes as $pattern => $definition) {
            $method  = $definition[0];
            $handler = $definition[1];
            $default = isset($definition[2]) ? $definition[2] : [];
            $this->addRoute(new Route($method, $pattern, $handler, $default));
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addGet(
        /*# string */ $pattern,
        $handler,
        array $defaultValues = []
    ) {
        return $this->addRoute(
            new Route('GET,HEAD', $pattern, $handler, $defaultValues)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function addPost(
        /*# string */ $pattern,
        $handler,
        array $defaultValues = []
    ) {
        return $this->addRoute(
            new Route('POST', $pattern, $handler, $defaultValues)
        );
    }
}
