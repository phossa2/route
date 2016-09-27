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

use Phossa2\Route\Resolver\ResolverSimple;
use Phossa2\Route\Interfaces\ResolverInterface;
use Phossa2\Route\Interfaces\ResolverAwareInterface;

/**
 * ResolverAwareTrait
 *
 * Implementation of ResolverAwareInterface
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     ResolverAwareInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
trait ResolverAwareTrait
{
    /**
     * @var    ResolverInterface
     * @access protected
     */
    protected $resolver;

    /**
     * {@inheritDoc}
     */
    public function setResolver(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getResolver()/*# : ResolverInterface */
    {
        if (is_null($this->resolver)) {
            $this->resolver = new ResolverSimple();
        }
        return $this->resolver;
    }
}
