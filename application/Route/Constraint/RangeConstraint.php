<?php
/**
 * PHP version 7
 *
 * Range constraint.
 *
 * @category Constraints
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

namespace miniworx\Application\Route\Constraint;

/**
 * Range constraint.
 *
 * @package MiniworX
 */
class RangeConstraint extends \miniworx\Application\Route\Constraint
{
    /** {@inheritdoc} */
    protected $type = 'range';

    /**
     * Constraint validation function.
     *
     * @param mixed $value The value to validate against the constraint.
     * @return boolean True if the constraint is validated; otherwise false.
     */
    public function validate(&$value)
    {
        return ($value >= $this->criteria[0]
                && $value <= $this->criteria[1]);
    }

    /**
     * Generate starting point.
     *
     * @param mixed $text The text to parse.
     * @return void Nothing.
     *
     * @SuppressWarnings(StaticAccess)
     */
    private function generateStart(&$text)
    {
        $inclusive = false;
        $number    = 0;

        switch (substr($text, 0, 1)) {
            case '(':
                $inclusive = true;
                /* Fallthrough. */
            case '[':
                $value  = substr($text, 1);
                $number = \miniworx\Application\Utils\Types::toNumber($value);
                                                          
                break;
            default:
                throw new \InvalidArgumentException(
                    "'${text}' is not a valid 'range' argument.  A 'range' " .
                    "should be delimited by '[' / ']' for `exclusive` or by " .
                    "'(' / ')' for `inclusive'`.  For example, the range " .
                    "'(1..4]' is the range '1, 2, 3'."
                );
        }

        $this->criteria[0] = $inclusive ? $number : $number + 1;
    }

    /**
     * Generate starting point.
     *
     * @param mixed $text The text to parse.
     * @return void Nothing.
     *
     * @SuppressWarnings(StaticAccess)
     */
    private function generateEnd(&$text)
    {
        $inclusive = false;
        $number    = 0;

        switch (substr($text, -1, 1)) {
            case ')':
                $inclusive = true;
                /* Fallthrough. */
            case ']':
                $value  = substr($text, 0, -1);
                $number = \miniworx\Application\Utils\Types::toNumber($value);
                break;
            default:
                throw new \InvalidArgumentException(
                    "'${text}' is not a valid 'range' argument.  A 'range' " .
                    "should be delimited by '[' / ']' for `exclusive` or by " .
                    "'(' / ')' for `inclusive'`.  For example, the range " .
                    "'(1..4]' is the range '1, 2, 3'."
                );
        }

        $this->criteria[1] = $inclusive ? $number : $number - 1;
    }

    /**
     * Configure a constraint.
     *
     * @param string $text The configuration.
     * @return void Empty.
     *
     * @SuppressWarnings(StaticAccess)
     * @SuppressWarnings(GotoStatement) - Reread Dijkstra's paper, damn you.
     */
    protected function parse(string &$text)
    {
        $range = explode('..', $text);
        if (count($range) !== 2) {
            throw new \InvalidArgumentException(
                "'${text}' is not a valid 'range' argument.  A 'range' " .
                "should have its start and end points delimited by '..', " .
                "e.g. '[1..100]'."
            );
        }

        $this->generateStart($range[0]);
        $this->generateEnd($range[1]);
    }
}

/* RangeConstraint.php ends here. */
