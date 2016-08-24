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
 * CollectorAwareInterface
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface CollectorAwareInterface
{
    /**
     * Inject a collector
     *
     * @param  CollectorInterface $collector
     * @return $this
     * @access public
     * @api
     */
    public function addCollector(CollectorInterface $collector);

    /**
     * Get all collectors
     *
     * @return CollectorInterface[]
     * @access public
     */
    public function getCollectors()/*# : array */;
}
