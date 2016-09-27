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

namespace Phossa2\Route;

use Phossa2\Shared\Base\ObjectAbstract;
use Phossa2\Route\Interfaces\RouteInterface;
use Phossa2\Route\Interfaces\ResultInterface;

/**
 * Result
 *
 * Mathed result
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     ObjectAbstract
 * @see     ResultInterface
 * @version 2.0.1
 * @since   2.0.0 added
 */
class Result extends ObjectAbstract implements ResultInterface
{
    /**
     * http status
     *
     * @var    int
     * @access protected
     */
    protected $status = Status::NOT_FOUND;

    /**
     * parsed parameters
     *
     * @var    array
     * @access protected
     */
    protected $parameters = [];

    /**
     * the handler
     *
     * @var    mixed
     * @access protected
     */
    protected $handler;

    /**
     * the matched route
     *
     * @var    RouteInterface
     * @access protected
     */
    protected $route;

    /**
     * URI path
     *
     * @var    string
     * @access protected
     */
    protected $path;

    /**
     * HTTP method
     *
     * @var    string
     * @access protected
     */
    protected $method;

    /**
     * @param  string $httpMethod HTTP method
     * @param  string $uriPath the URI path to match with
     * @access public
     */
    public function __construct(
        /*# string */ $httpMethod,
        /*# string */ $uriPath
    ) {
        $this->method = $httpMethod;
        $this->setPath($uriPath);
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()/*# : string */
    {
        return $this->path;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethod()/*# : string */
    {
        return $this->method;
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()/*# : int */
    {
        return $this->status;
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus(/*# int */ $status)
    {
        $this->status = (int) $status;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getParameters()/*# : array */
    {
        return $this->parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function setParameters(array $params)
    {
        if (!empty($params)) {
            $this->parameters = array_replace($this->parameters, $params);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * {@inheritDoc}
     */
    public function setRoute(RouteInterface $route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set the path
     * @param  string $uriPath
     * @access protected
     */
    protected function setPath(/*# string */ $uriPath)
    {
        $pos = strpos($uriPath, '?');
        if (false !== $pos) {
            $path = substr($uriPath, 0, $pos);
            parse_str(substr($uriPath, $pos + 1), $this->parameters);
        } else {
            $path = $uriPath;
        }

        // remove trailing '/'
        $this->path = rtrim($path, '/');
    }
}
