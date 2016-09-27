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

namespace Phossa2\Route\Collector;

use Phossa2\Route\Status;
use Phossa2\Route\Interfaces\ResultInterface;

/**
 * CollectorQPR
 *
 * Query Parameter Routing (QPR)
 *
 * ```
 * http://servername/path/?r=controller-action-id-1-name-nick
 * ```
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     CollectorPPR
 * @version 2.0.0
 * @since   2.0.0 added
 */
class CollectorQPR extends CollectorPPR
{
    /**
     * variable name
     *
     * @var    string
     * @access protected
     */
    protected $varname = 'r';

    /**
     * seperator
     *
     * @var    string
     * @access protected
     */
    protected $seperator = '-';

    /**
     * Query Parameter Routing
     *
     * http://servername/path/?r=controller-action-id-1-name-nick
     *
     * {@inheritDoc}
     */
    protected function match(ResultInterface $result)/*# : bool */
    {
        $param = $result->getParameters();
        if (isset($param[$this->varname])) {
            $parts = explode($this->seperator, $param[$this->varname]);
            if (count($parts) > 1) {
                return $this->processParts($parts, $result);
            }
        }
        $result->setStatus(Status::BAD_REQUEST);
        return false;
    }
}
