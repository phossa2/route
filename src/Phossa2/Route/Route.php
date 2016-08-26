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

use Phossa2\Route\Message\Message;
use Phossa2\Route\Exception\LogicException;
use Phossa2\Route\Traits\HandlerAwareTrait;
use Phossa2\Route\Interfaces\RouteInterface;
use Phossa2\Route\Interfaces\HandlerAwareInterface;
use Phossa2\Event\EventableExtensionCapableAbstract;

/**
 * Route
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     EventableExtensionCapableAbstract
 * @see     RouteInterface
 * @see     HandlerAwareInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
class Route extends EventableExtensionCapableAbstract implements RouteInterface, HandlerAwareInterface
{
    use HandlerAwareTrait;

    /**#@+
     * Route related events
     *
     * @const
     */

    // before executing handler on this route
    const EVENT_BEFORE_HANDLER = 'route.handler.before';

    // after executing handler on this route
    const EVENT_AFTER_HANDLER = 'route.handler.before';

    /**#@-*/

    /**
     * pattern to match against
     *
     * @var    string
     * @access protected
     */
    protected $pattern;

    /**
     * allowed http methods
     *
     * @var    string[]
     * @access protected
     */
    protected $methods;

    /**
     * default values for placeholders in the pattern
     *
     * @var    array
     * @access protected
     */
    protected $defaults = [];

    /**
     * Constructor
     *
     * @param  string|string[] $httpMethod 'GET|POST' allowed for this route.
     * @param  string $pattern matching pattern
     * @param  mixed $handler for Status::OK status
     * @param  array $defaultValues default value for placeholders
     * @throws LogicException if pattern malformed
     * @access public
     */
    public function __construct(
        $httpMethod,
        /*# string */ $pattern,
        $handler,
        array $defaultValues = []
    ) {
        $this->setMethods($httpMethod)
             ->setPattern($pattern)
             ->addHandler($handler)
             ->setDefault($defaultValues);
    }

    /**
     * {@inheritDoc}
     */
    public function setPattern(/*# string */ $pattern)
    {
        // pattern checking
        $this->validatePattern($pattern);

        // check default values in the pattern
        if (false !== strpos($pattern, '=')) {
            $pattern = $this->extractDefaultValues($pattern);
        }

        $this->pattern = $pattern;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPattern()/*# : string */
    {
        return $this->pattern;
    }

    /**
     * {@inheritDoc}
     */
    public function setMethods($methods)
    {
        $this->methods = is_string($methods) ?
            preg_split('~[^A-Z]+~', strtoupper($methods), -1, PREG_SPLIT_NO_EMPTY) :
            array_map('strtoupper', $methods);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethods()/*# : array */
    {
        return $this->methods;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefault(array $values)
    {
        $this->defaults = array_replace($this->defaults, $values);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefault()/*# : array */
    {
        return $this->defaults;
    }

    /**
     * Validate the pattern
     *
     * @param  string $pattern
     * @throws LogicException
     * @access protected
     */
    protected function validatePattern(/*# string */ $pattern)
    {
        if (!is_string($pattern) ||
            substr_count($pattern, '[') !== substr_count($pattern, ']') ||
            substr_count($pattern, '{') !== substr_count($pattern, '}')
        ) {
            throw new LogicException(
                Message::get(Message::RTE_PATTERN_MALFORM, $pattern),
                Message::RTE_PATTERN_MALFORM
            );
        }
    }

    /**
     * Extract default values from the pattern
     *
     * @param  string $pattern
     * @return string
     * @access protected
     */
    protected function extractDefaultValues(
        /*# string */ $pattern
    )/*# : string */ {
        $regex = '~\{([a-zA-Z][a-zA-Z0-9_]*+)[^\}]*(=[a-zA-Z0-9._]++)\}~';
        if (preg_match_all($regex, $pattern, $matches, \PREG_SET_ORDER)) {
            $srch = $repl = $vals = [];
            foreach ($matches as $m) {
                $srch[] = $m[0];
                $repl[] = str_replace($m[2], '', $m[0]);
                $vals[$m[1]] = substr($m[2], 1);
            }
            $this->setDefault($vals);
            return str_replace($srch, $repl, $pattern);
        }
        return $pattern;
    }
}
