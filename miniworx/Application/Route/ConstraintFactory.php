<?php
/**
 * PHP version 7
 *
 * Constraint factory class.
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
 
namespace miniworx\Application\Route;

/**
 * Constraint factory class.
 *
 * @package MiniworX
 */
class ConstraintFactory
{
    /**
     * Constraint factory method.
     *
     * @param string $type     The constraint type.
     * @param mixed  $criteria The constraint criteria.
     * @return Constraint A newly-created constraint instance.
     */
    public static function makeConstraint(string &$type, &$criteria)
    {
        $prefix = '\\miniworx\\Application\\Route\\Constraint\\';
        $class  = $prefix . ucfirst(strtolower($type)) . 'Constraint';

        if (class_exists($class, true)) {
            return new $class($criteria);
        }

        throw new InvalidConstraintException(
            "Invalid constraint type '${type}'"
        );
    }
}

/* ConstraintFactory.php ends here. */
