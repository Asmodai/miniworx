<?php
/**
 * PHP version 7
 *
 * Integer data type filter.
 *
 * @category Classes
 * @package Classes
 * @author Paul Ward <asmodai@gmail.com>
 * @copyright 2018 Paul Ward <asmodai@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://www.github.com/...
 *
 * Created:    04 Aug 2018 04:58:48
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

namespace miniworx\Route\Filter;

/**
 * Integer data type filter.
 *
 * @category Classes
 * @package Classes
 * @author Paul Ward <asmodai@gmail.com>
 * @copyright 2018 Paul Ward <asmodai@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://www.github.com/...
 */
class IntegerFilter extends \miniworx\Route\Filter
{
    /**
     * Filter validation function.
     *
     * @param mixed $value Value to validate against the filter.
     * @return boolean True if filter validated; otherwise false.
     */
    public function validate($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
}

/* IntegerFilter.php ends here. */
