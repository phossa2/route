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

use Phossa2\Shared\Base\StaticAbstract;

/**
 * Status
 *
 * HTTP status code
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
class Status extends StaticAbstract
{
    /**#@+
     * Routing result code (similar to HTTP codes)
     *
     * @const
     */
    const OK                    = 200;
    const MOVED_PERMANENTLY     = 301;
    const NOT_MODIFIED          = 304;
    const BAD_REQUEST           = 400;
    const UNAUTHORIZED          = 401;
    const NOT_FOUND             = 404;
    const METHOD_NOT_ALLOWED    = 405;
    const PRECONDITION_FAILED   = 412;
    const SERVER_ERROR          = 500;
    const SERVICE_UNAVAILABLE   = 503;
    /**#@-*/
}
