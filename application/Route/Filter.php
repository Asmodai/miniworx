<?php
/**
 * PHP version 7
 *
 * Base filter class.
 *
 * @category Filters
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
 * Base filter class.
 *
 * @package MiniworX
 */
abstract class Filter
{
    /**
     * Filter type.
     *
     * @var string
     */
    protected $type = "";

    /**
     * Filter validation function.
     *
     * @param mixed $value Value to validate against the filter.
     * @return boolean True if filter validated; otherwise false.
     */
    abstract public function validate(&$value);

    /**
     * Constructor method.
     *
     * @param string $type The type of the filter.
     */
    public function __construct(string &$type)
    {
        $this->type = $type;
    }

    /**
     * Coerce the filter to a string value.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->type;
    }
    
    /**
     * Return a JSON representation of the filter.
     *
     * @return array
     */
    public function toJson()
    {
        $value = \miniworx\Application\Utils\Types::toString($this->type);
        
        return $value;
    }
    
    /**
     * Return the type of the filter.
     *
     * @return string The filter type.
     */
    public function type()
    {
        return $this->type;
    }
}

/* Filter.php ends here. */
