<?php
/**
 * PHP version 7
 *
 * Lesser-Than constraint.
 *
 * @category Classes
 * @package Classes
 * @author Paul Ward <asmodai@gmail.com>
 * @copyright 2018 Paul Ward <asmodai@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT The MIT License
 * @link https://github.com/vivi90/miniworx
 *
 * Created:    04 Aug 2018 17:58:51
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

namespace miniworx\Route\Constraint;

/**
 * Lesser-Than constraint.
 *
 * @category Classes
 * @package Classes
 * @author Paul Ward <asmodai@gmail.com>
 * @copyright 2018 Paul Ward <asmodai@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License
 * @link https://github.com/vivi90/miniworx
 */
class LTConstraint extends \miniworx\Route\Constraint
{
    /** {@inheritdoc} */
    protected $type = 'lesser-than';

    /**
     * Constraint validation function.
     *
     * @param mixed $value The value to validate against the constraint.
     * @return boolean True if the constraint is validated; otherwise false.
     */
    public function validate(&$value)
    {
        return ($value < $this->criteria);
    }

    /**
     * Configure a constraint.
     *
     * @param string $text The configuration.
     * @return void Empty.
     *
     * @SuppressWarnings(StaticAccess)
     */
    protected function parse(string &$text)
    {
        $this->criteria = \miniworx\Utils\Types::toNumber(
            $text,
            TYPE_INTEGER
        );
    }
}

/* LTConstraint.php ends here. */
