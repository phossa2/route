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
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface DispatcherInterface
{
    /**
     * Match against URI PATH and HTTP METHOD
     *
     * @param  string $uriPath
     * @param  string $httpMethod
     * @return bool
     * @access public
     * @api
     */
    public function match(
        /*# string */ $uriPath = '',
        /*# string */ $httpMethod = 'GET'
    )/*# :  bool */;

    /**
     * Match and dispatch against URI PATH and HTTP METHOD
     *
     * @param  string $uriPath
     * @param  string $httpMethod
     * @return bool
     * @access public
     * @api
     */
    public function dispatch(
        /*# string */ $uriPath = '',
        /*# string */ $httpMethod = 'GET'
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
