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

namespace Phossa2\Route\Parser;

use Phossa2\Route\Message\Message;

/**
 * ParserGcb
 *
 * FastRoute algorithm
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     ParserAbstract
 * @version 2.0.0
 * @since   2.0.0 added
 */
class ParserGcb extends ParserAbstract
{
    /**
     * group position map
     *
     * @var    array
     * @access protected
     */
    protected $maps = [];

    /**
     * chunk size 4 - 12 for merging regex
     *
     * @var    int
     * @access protected
     */
    protected $chunk = 8;

    /**
     * combined regex (cache)
     *
     * @var    string[]
     * @access protected
     */
    protected $data = [];

    /**
     * another cache
     *
     * @var    string[]
     * @access protected
     */
    protected $xmap = [];

    /**
     * {@inheritDoc}
     */
    public function processRoute(
        /*# string */ $routeName,
        /*# string */ $routePattern
    )/*# : string */ {
        list($regex, $map) = $this->convert($routePattern);
        $this->maps[$routeName] = $map;
        $this->doneProcess($routeName, $routePattern, $regex);
        return $regex;
    }

    /**
     * {@inheritDoc}
     */
    public function matchPath(/*# string */ $uriPath)
    {
        $matches = [];
        foreach ($this->getRegexData() as $i => $regex) {
            if (preg_match($regex, $uriPath, $matches)) {
                $map = array_flip($this->xmap[$i]);
                $key = $map[count($matches) - 1];
                return $this->fixMatches($key, $matches);
            }
        }
        return false;
    }

    /**
     * Convert to regex
     *
     * @param  string $pattern pattern to parse
     * @return array
     * @access protected
     */
    protected function convert(/*# string */ $pattern)/*# : array */
    {
        $ph = sprintf("\{%s(?:%s)?\}", self::MATCH_GROUP_NAME, self::MATCH_GROUP_TYPE);

        // count placeholders
        $map = $m = [];
        if (preg_match_all('~'. $ph .'~x', $pattern, $m)) {
            $map = $m[1];
        }

        $result = preg_replace(
            [
            '~' . $ph . '(*SKIP)(*FAIL) | \[~x', '~' . $ph . '(*SKIP)(*FAIL) | \]~x',
            '~\{' . self::MATCH_GROUP_NAME . '\}~x', '~' . $ph . '~x',
            ],
            ['(?:', ')?', '{\\1:' . self::MATCH_SEGMENT . '}', '(\\2)'],
            strtr('/' . trim($pattern, '/'), $this->shortcuts)
        );

        return [$result, $map];
    }

    /**
     * Merge several (chunk size) regex into one
     *
     * @return array
     * @access protected
     */
    protected function getRegexData()/*# : array */
    {
        // load from cache
        if (!$this->modified) {
            return $this->data;
        }

        // merge
        $this->data = array_chunk($this->regex, $this->chunk, true);
        foreach ($this->data as $i => $arr) {
            $map = $this->getMapData($arr, $this->maps);
            $str = '~^(?|';
            foreach ($arr as $k => $reg) {
                $str .= $reg . str_repeat('()', $map[$k] - count($this->maps[$k])) . '|';
            }
            $this->data[$i] = substr($str, 0, -1) . ')$~x';
            $this->xmap[$i] = $map;
        }
        $this->modified = false;
        return $this->data;
    }

    /**
     * @param  array $arr
     * @param  array $maps
     * @return array
     * @access protected
     */
    protected function getMapData(array $arr, array $maps)/*#: array */
    {
        $new1 = [];
        $keys = array_keys($arr);
        foreach ($keys as $k) {
            $new1[$k] = count($maps[$k]) + 1; // # of PH for route $k
        }
        $new2 = array_flip($new1);
        $new3 = array_flip($new2);

        foreach ($keys as $k) {
            if (!isset($new3[$k])) {
                foreach (range(1, 200) as $i) {
                    $cnt = $new1[$k] + $i;
                    if (!isset($new2[$cnt])) {
                        $new2[$cnt] = $k;
                        $new3[$k] = $cnt;
                        break;
                    }
                }
            }
        }
        return $new3;
    }

    /**
     * Fix matched placeholders, return with unique route key
     *
     * @param  string $name the route key/name
     * @param  array $matches desc
     * @return array [ $name, $matches ]
     * @access protected
     */
    protected function fixMatches(
        /*# string */ $name,
        array $matches
    )/*# : array */ {
        $res = [];
        $map = $this->maps[$name];
        foreach ($matches as $idx => $val) {
            if ($idx > 0 && '' !== $val) {
                $res[$map[$idx - 1]] = $val;
            }
        }

        // debug
        $this->debug(Message::get(
            Message::RTE_PARSER_MATCH,
            $name,
            $this->regex[$name]
        ));
        return [$name, $res];
    }
}
