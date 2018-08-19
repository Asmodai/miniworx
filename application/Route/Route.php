<?php
/**
 * PHP version 7
 *
 * Route class.
 *
 * @category Route
 * @package MiniworX
 * @author Paul Ward <asmodai@gmail.com>
 * @copyright 2018 Paul Ward <asmodai@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT The MIT License
 * @link https://github.com/vivi90/miniworx
 */
/*
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 * BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

declare(strict_types=1);

namespace miniworx\Application\Route;

use \miniworx\Application\Exceptions;

/**
 * Route class.
 *
 * @package MiniworX
 */
class Route
{
    /**
     * @var array Array of fragments used for matching routes.
     */
    private $path = array();

    /**
     * @var array Array of variable bindings.
     */
    private $bindings = array();

    /**
     * @var object Target class for route.
     */
    private $instance = null;

    /**
     * Constructor method.
     *
     * @param string $path     The route's path.
     * @param mixed  $instance The route's class instance.
     *
     * @SuppressWarnings(StaticAccess)
     */
    public function __construct(string $path, $instance = null)
    {
        if (isset($instance)) {
            $this->instance = $instance;
        }

        foreach (explode('/', ltrim($path, '/')) as $fragment) {
            if (substr($fragment, 0, 1) === ':') {
                $name = substr($fragment, 1);

                if (($parsed = Parser::Parse($name)) !== false) {
                    $name = $parsed['variable'];

                    $this->bindings[$name] = $parsed;
                    $this->path[]          = ':' . $name;
                    continue;
                }

                $this->bindings[$name] = $name;
            }

            $this->path[] = $fragment;
        }
    }

    /**
     * Returns the route's class instance.
     *
     * @return object The route's class instance.
     */
    public function instance()
    {
        return $this->instance;
    }

    /**
     * Returns the route's path.
     *
     * @return array The route's path.
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Match this route against the given path.
     *
     * @param string $against The path to match this route against.
     * @return boolean True if the route matches the given path; otherwise
     *                 false is returned.
     *
     * @see matchArray
     */
    public function match(string &$against)
    {
        $elements = explode('/', $against);

        return $this->matchArray($elements);
    }

    /**
     * Match this route against the given array containing path elements.
     *
     * @param array $against The path to match this route against.
     * @return boolean True if the route matches the given path elements;
     *                 otherwise false is returned.
     *
     * @see match
     */
    public function matchArray(array &$against)
    {
        if (count($against) != $this->matchesLen) {
            return false;
        }

        $combined = array_combine($this->matches, $against);
        foreach (array_keys($combined) as $key) {
            if (! $this->fragmentMatch($combined[$key], $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Apply a filter for the binding at the given key to the given value.
     *
     * @param mixed  $value The value to which we apply the filter.
     * @param string $key   The key for the binding containing the filter.
     * @return boolean True if the filter is validated; otherwise false.
     */
    private function applyFilter(&$value, string &$key)
    {
        if (!$value) {
            // TODO: Should empty values be handled in a special way?
            return false;
        }

        if (!isset($this->bindings[$key])) {
            return false;
        }

        if (!isset($this->bindings[$key]['filter'])) {
            /*
             * If there is no filter, then simply return `true'.
             *
             * Returning `false' here can break variable bindings that do
             * not have a filter.
             */
            return true;
        }

        return $this->bindings[$key]['filter']->validate($value);
    }

    /**
     * Apply a constraint for the binding a the given key to the given
     * value;
     *
     * @param mixed  $value The value to which we apply the constraint.
     * @param string $key   The key for the binding containing the constraint.
     * @return boolean True if the constraint is validated; otherwise false.
     */
    private function applyConstraint(&$value, string &$key)
    {
        if (!$value) {
            // TODO: Should empty values be handled in a special way?
            return false;
        }

        if (!isset($this->bindings[$key])) {
            return false;
        }

        if (!isset($this->bindings[$key]['constraint'])) {
            /*
             * Similarly to the filter application, a lack of a constraint
             * for a binding requires us to return `true'.
             */
            return true;
        }

        return $this->bindings[$key]['constraint']->validate($value);
    }

    /**
     * Compute any bindings for this match.
     *
     * A binding is an associated array where the key is the name of the
     * variable and the value is its binding.
     *
     * @param array $groups The data from which bindings are extracted.
     * @return array An array of bindings.
     */
    public function bindings(array &$groups)
    {
        if (isset($groups)) {
            $result = array();
            $errors = array();

            $combined = array_combine($this->path, $groups);
            foreach (array_keys($this->bindings) as $key) {
                $value = $combined[':' . $key];

                if (!$this->applyFilter($value, $key)) {
                    $obj = new Exceptions\FailedFilterException(
                        "Filter failed for argument `:${key}'.  " .
                        'Expected ' . $this->bindings[$key]['filter']
                    );

                    $obj->addAttribute('argument', ':' . $key)
                        ->addAttribute(
                            'filter',
                            $this->bindings[$key]['filter']->toJson()
                        );

                    $errors[] = $obj;
                }

                if (!$this->applyConstraint($value, $key)) {
                    $obj = new Exceptions\FailedConstraintException(
                        "Constraint failed for argument `:${key}'.  " .
                        'Expected ' . $this->bindings[$key]['constraint']
                    );

                    $obj->addAttribute('argument', ':' . $key)
                        ->addAttribute(
                            'constraint',
                            $this->bindings[$key]['constraint']->toJson()
                        );

                    $errors[] = $obj;
                }

                $result[$key] = $value;
            }

            if (!empty($errors)) {
                $exception = new Exceptions\RouteException($this->path);

                $exception->setExceptions($errors);

                throw $exception;
            }

            return $result;
        }
    }

    /**
     * Match a URI fragment.
     *
     * @param string $what The URI fragment to match against a route fragment.
     * @param string $with The route fragment to match against.
     * @return boolean True if there is a match; otherwise false is returned.
     * @private
     */
    private function fragmentMatch(string &$what, string &$with)
    {
        if (substr($with, 0, 1) === ':') {
            return true;
        }

        if ($what === $with) {
            return true;
        }

        return false;
    }
}

/* Route.php ends here. */
