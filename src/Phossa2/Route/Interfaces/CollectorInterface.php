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
 * CollectorInterface
 *
 * Collector: collection of routes
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface CollectorInterface extends AddRouteInterface
{
    /**
     * Match with all routes in this collector
     *
     * @param  ResultInterface $result
     * @return bool
     * @access public
     */
    public function matchRoute(ResultInterface $result)/*# : bool */;
}
