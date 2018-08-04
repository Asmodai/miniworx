<?php
/**
 * PHP version 7
 *
 * Filter/constraint parser.
 *
 * @category Classes
 * @package Classes
 * @author Paul Ward <asmodai@gmail.com>
 * @copyright 2018 Paul Ward <asmodai@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://www.github.com/...
 *
 * Created:    04 Aug 2018 04:37:12
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
 * Filter/constraint parser.
 *
 * @category Classes
 * @package Classes
 * @author Paul Ward <asmodai@gmail.com>
 * @copyright 2018 Paul Ward <asmodai@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://www.github.com/...
 */
class Parser
{
    /**
     * Parse the given text for a constraint.
     *
     * @param string $text The text to parse.
     * @return miniworx\Route\Constraint A newly-created constraint.
     * @throws miniworx\Route\InvalidConstraintException should the parsed
     *         exception be invalid.
     *
     * @SuppressWarnings(StaticAccess)
     */
    private static function parseConstraint($text)
    {
        $second  = strpos($text, ',', strpos($text, ',') + 1);
        $process = explode('=', substr($text, $second + 1, -1));

        if (count($process) <= 1) {
            throw new \Exception("Invalid constraint '${text}'.");
        }

        return ConstraintFactory::makeConstraint(
            $process[0],
            $process[1]
        );
    }

    /**
     * Parse the given text for a filter.
     *
     * @param string $text The text to parse.
     * @return miniworx\Route\Filter A newly-created filter.
     *
     * @SuppressWarnings(StaticAccess)
     */
    private static function parseFilter($text)
    {
        $first  = strpos($text, ',');
        $second = strpos($text, ',', $first + 1);

        return FilterFactory::makeFilter(
            substr(
                $text,
                $first + 1,
                $second ? abs($first - $second) - 1 : -1
            )
        );
    }

    /**
     * Parse the given text for either a filter or a constraint or both.
     *
     * @param string $text The text to parse.
     * @return array An array containing variable name, filter, and constraint
     *               if any were parsed from the text.
     *
     * @SuppressWarnings(StaticAccess)
     */
    public static function parse($text)
    {
        if (substr($text, 1, 1)     !== '{'
            && substr($text, -1, 1) !== '}'
        ) {
            return false;
        }

        $commas = substr_count($text, ',');
        if ($commas == 0 || $commas > 2) {
            return false;
        }

        $variable   = substr($text, 1, strpos($text, ',') - 1);
        $constraint = null;
        $filter     = null;

        // Pay attention to the lack of `break'!
        // Also, PHP indentation totally sucks.
        switch ($commas) {
            case 2:
                $constraint = self::parseConstraint($text);
                /* Fallthrough */

            case 1:
                $filter = self::parseFilter($text);
                break;
        }

        return array(
            'variable'   => $variable,
            'constraint' => $constraint,
            'filter'     => $filter
        );
    }
}

/* Parser.php ends here. */
