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

use Phossa2\Route\Status;

/**
 * HandlerAwareInterface
 *
 * Route handler aware.
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface HandlerAwareInterface
{
    /**
     * Add route handler for the corresponding http status
     *
     * @param  mixed $handler callable or ['controller','action']
     * @param  int $status http status
     * @return $this
     * @access public
     * @api
     */
    public function addHandler($handler, /*# int */ $status = Status::OK);

    /**
     * Get route handler for this status code
     *
     * if handler is set for status: 0, then this handler will be used if no
     * handler found for $status !!
     *
     * @param  int $status http status
     * @return mixed
     * @access public
     */
    public function getHandler(/*# int */ $status);
}
