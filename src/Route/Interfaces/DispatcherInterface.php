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
 * DispatcherInterface
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.1
 * @since   2.0.0 added
 * @since   2.0.1 added $parameters to match() & dispatch()
 */
interface DispatcherInterface
{
    /**
     * Match against URI PATH and HTTP METHOD
     *
     * @param  string $httpMethod
     * @param  string $uriPath
     * @param  string $parameters parameters pass over to the result
     * @return bool
     * @access public
     * @api
     * @since  2.0.1 added $parameters
     */
    public function match(
        /*# string */ $httpMethod,
        /*# string */ $uriPath,
        array $parameters = []
    )/*# : bool */;

    /**
     * Match and dispatch against URI PATH and HTTP METHOD
     *
     * @param  string $httpMethod
     * @param  string $uriPath
     * @param  string $parameters parameters pass over to the result
     * @return bool
     * @access public
     * @api
     * @since  2.0.1 added $parameters
     */
    public function dispatch(
        /*# string */ $httpMethod,
        /*# string */ $uriPath,
        array $parameters = []
    )/*# : bool */;

    /**
     * Get the result object
     *
     * @return ResultInterface
     * @access public
     * @api
     */
    public function getResult()/*# : ResultInterface */;
}
