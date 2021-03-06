<?php
/**
 * PHP version 7
 *
 * Various type-based utilities and hacks.
 *
 * @category Utilities
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

namespace miniworx\Application\Utils;

define('TYPE_INTEGER', 1);
define('TYPE_FLOAT',   2);
define('TYPE_BOOLEAN', 3);

/**
 * Various type-based utilities and hacks.
 *
 * @package MiniworX
 */
class Types
{
    /**
     * Is the given value a string?
     *
     * @param mixed $value The value to test.
     * @return bool True if the value is a string; otherwise false.
     */
    public static function isString(&$value)
    {
        if (is_string($value)) {
            return true;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return true;
        }

        return false;
    }

    /**
     * Convert a given value to a string.
     *
     * @param mixed $value The value to coerce.
     * @return string The converted value.
     *
     * @throw \InvalidArgumentException Thrown when the given value cannot
     *        be coerced to a string.
     *
     */
    public static function toString(&$value)
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_object($value) && !method_exists($value, '__toString')) {
            throw \InvalidArgumentException(
                "The given value cannot be coerced to a string."
            );
        }
        
        if (is_array($value)) {
            return implode(',', $value);
        }

        return '' . $value;
    }

    /**
     * Convert a given value to a numeric value in a safer manner than
     * `intval'.
     *
     * @param mixed   $value The value to coerce.
     * @param integer $type  The required numeric data type for the result.
     * @return mixed The converted numeric value.
     *
     * @throw \InvalidArgumentException Thrown when the requested data type is
     *        not known.
     *
     * @throw \UnexpectedValueException Thrown when there was an issue
     *        coercing the value to a booelan value.
     */
    public static function toNumber(&$value, $type = TYPE_INTEGER)
    {
        $flags  = [];
        $filter = FILTER_DEFAULT;

        switch ($type) {
            case TYPE_INTEGER:
                $filter = FILTER_VALIDATE_INT;
                break;
            case TYPE_FLOAT:
                $filter = FILTER_VALIDATE_FLOAT;
                break;
            case TYPE_BOOLEAN:
                $flags[] = FILTER_NULL_ON_FAILURE;
                $filter  = FILTER_VALIDATE_BOOLEAN;
                break;
            default:
                throw new \InvalidArgumentException(
                    "The type '${type}' is not known."
                );
        }

        $value = filter_var($value, $filter, $flags);
        if ($value === false && $filter !== FILTER_VALIDATE_BOOLEAN) {
            throw new \UnexpectedValueException(
                "There was an unexpected error while coercing " .
                json_encode($value) . "."
            );
        }

        return $value;
    }
}


/* types.php ends here. */
