<?php
/**
 * PHP version 7
 *
 * Filter/constraint parser.
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

/**
 * Filter/constraint parser.
 *
 * @package MiniworX
 */
class Parser
{
    /**
     * Parse the given text for a constraint.
     *
     * @param string $text The text to parse.
     * @return Constraint A newly-created constraint.
     * @throws InvalidConstraintException should the parsed exception be
     *         invalid.
     *
     * @SuppressWarnings(StaticAccess)
     */
    private static function parseConstraint(&$text)
    {
        $process = explode('=', $text);

        // For constraints that take no arguments.
        if (!isset($process[1])) {
            $process[1] = "";
        }

        return ConstraintFactory::makeConstraint($process[0], $process[1]);
    }

    /**
     * Parse the given text for a filter.
     *
     * @param string $text The text to parse.
     * @return Filter|null A newly-created filter; or null if no filter type
     *         is present.
     *
     * @SuppressWarnings(StaticAccess)
     */
    private static function parseFilter(&$text)
    {
        if (!$text || isset($text[0])) {
            return null;
        }

        return FilterFactory::makeFilter($text);
    }

    /**
     * Parse the given text for either a filter or a constraint or both.
     *
     * @param string $text The text to parse.
     * @return array An array containing variable name, filter, and constraint
     *               if any were parsed from the text.
     *
     * @SuppressWarnings(StaticAccess)
     * @SuppressWarnings(GotoStatement) -- I KNOW WHAT I AM DOING.
     */
    public static function parse(&$text)
    {
        if (substr($text, 1, 1)     !== '{'
            && substr($text, -1, 1) !== '}'
        ) {
            return false;
        }

        $commas = substr_count($text, ',');
        if ($commas == 0) {
            return false;
        }

        $parts      = explode(',', substr($text, 1, -1));
        $variable   = $parts[0];
        $constraint = null;
        $filter     = null;

        /* Save effort if there's no constraint or filter. */
        if (!isset($parts[1])) {
            goto end;
        }

        if (isset($parts[1])) {
            $filter = self::parseFilter($parts[1]);
        }

        if (isset($parts[2])) {
            $joined     = implode(',', array_slice($parts, 2));
            $constraint = self::parseConstraint($joined);
        }

        end:
        return array(
            'variable'   => $variable,
            'constraint' => $constraint,
            'filter'     => $filter
        );
    }
}

/* Parser.php ends here. */
