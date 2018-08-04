<?php
/**
 * PHP version 7
 *
 * Route class.
 *
 * @category Classes
 * @package Classes
 * @author Paul Ward <asmodai@gmail.com>
 * @copyright 2018 Paul Ward <asmodai@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://www.github.com/...
 *
 * Created:    04 Aug 2018 04:14:15
 *
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

namespace miniworx\Route;

/**
 * Route class.
 *
 * @category Classes
 * @package Classes
 * @author Paul Ward <asmodai@gmail.com>
 * @copyright 2018 Paul Ward <asmodai@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://www.github.com/...
 */
class Route
{
    /**
     * @var string Textual representation of the route.
     */
    private $text = '';

    /**
     * @var array Array of fragments used for matching routes.
     */
    private $matches = array();

    /**
     * @var integer Length of fragments array.
     */
    private $matchesLen = 0;

    /**
     * @var array Array of variable bindings.
     */
    private $bindings = array();

    /**
     * Constructor method.
     *
     * @param string $path The route's path.
     *
     * @SuppressWarnings(StaticAccess)
     */
    public function __construct(string $path)
    {
        foreach (explode('/', $path) as $fragment) {
            if (substr($fragment, 0, 1) === ':') {
                $name = substr($fragment, 1);

                if (($parsed = Parser::Parse($name)) !== false) {
                    $name = $parsed['variable'];

                    $this->bindings[$name] = $parsed;
                    $this->matches[]       = ':' . $name;
                    continue;
                }

                $this->bindings[$name] = null;
            }

            $this->matches[] = $fragment;
        }

        $this->matchesLen = count($this->matches);
        $this->text       = implode('/', $this->matches);
    }

    /**
     * Match this route against the given path.
     *
     * @param string $against The path to match this route against.
     * @return mixed|false A list of bindings for the match if successful;
     *                     otherwise false is returned.
     */
    public function match(string $against)
    {
        $elements = explode('/', $against);
        if (count($elements) != $this->matchesLen) {
            return false;
        }

        $combined = array_combine($this->matches, $elements);
        foreach (array_keys($combined) as $key) {
            if (! $this->fragmentMatch($combined[$key], $key)) {
                return false;
            }
        }

        return $this->bindings($combined);
    }

    /**
     * Returns the textual representation of this route.
     *
     * @return string The textual representation of this route.
     */
    public function text()
    {
        return $this->text;
    }

    /**
     * Compute any bindings for this match.
     *
     * A binding is an associated array where the key is the name of the
     * variable and the value is its binding.
     *
     * @param array $groups The data from which bindings are extracted.
     * @return array An array of bindings.
     * @private
     */
    private function bindings(array $groups)
    {
        if (count($groups) > 0) {
            $result = array();

            foreach (array_keys($this->bindings) as $key) {
                $value = $groups[':' . $key];

                if ($this->bindings[$key] !== null) {
                    if (! $this->bindings[$key]['filter']->validate($value)) {
                        // TODO: Exception here!
                        echo "FILTER FAILED!";
                        return false;
                    }
                }

                $result[$key] = $groups[':' . $key];
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
    private function fragmentMatch(string $what, string $with)
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