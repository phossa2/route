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

use Phossa2\Route\Interfaces\PrefixAwareInterface;

/**
 * PrefixAwareTrait
 *
 * Implementation of PrefixAwareInterface
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     PrefixAwareInterface
 * @version 2.0.1
 * @since   2.0.1 added
 */
trait PrefixAwareTrait
{
    /**
     * prefix to match
     *
     * @var    string
     * @access protected
     */
    protected $path_prefix;

    /**
     * {@inheritDoc}
     */
    public function setPathPrefix(/*# string */ $pathPrefix = null)
    {
        $this->path_prefix = $pathPrefix;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function matchPathPrefix(/*# string */ $uriPath)/*# : bool */
    {
        if (!is_string($this->path_prefix) ||
            empty($this->path_prefix) ||
            0 === strpos($uriPath, $this->path_prefix)
        ) {
            return true;
        }
        return false;
    }
}
