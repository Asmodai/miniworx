<?php
/**
 * PHP version 7
 *
 * Base constraint class.
 *
 * @category Classes
 * @package Classes
 * @author Paul Ward <asmodai@gmail.com>
 * @copyright 2018 Paul Ward <asmodai@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://www.github.com/...
 *
 * Created:    04 Aug 2018 05:00:52
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
 * Base constraint class.
 *
 * @category Classes
 * @package Classes
 * @author Paul Ward <asmodai@gmail.com>
 * @copyright 2018 Paul Ward <asmodai@gmail.com>
 * @license https://opensource.org/licenses/MIT MIT
 * @link https://www.github.com/...
 */
abstract class Constraint
{
    /** @var mixed Constraint criteria. */
    protected $criteria;

    /** @var string Constraint type. */
    protected $type;

    /**
     * Constraint validation function.
     *
     * @param mixed $value The value to validate against the constraint.
     * @return boolean True if the constraint is validated; otherwise false.
     */
    abstract public function validate($value);

    /**
     * Constructor method.
     *
     * @param string $type     The constraint type.
     * @param mixed  $criteria The critera for the constraint.
     */
    public function __construct(string $type, mixed $criteria)
    {
        $this->type     = $type;
        $this->criteria = $criteria;
    }

    /**
     * Return the constraint type.
     *
     * @return string The constraint type.
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * Return the constraint criteria.
     *
     * @return mixed The constraint criteria.
     */
    public function criteria()
    {
        return $this->criteria;
    }
}

/* Constraint.php ends here. */
