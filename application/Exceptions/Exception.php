<?php
/**
 * PHP version 7
 *
 * Base exception.
 *
 * @category Exceptions
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

namespace miniworx\Application\Exceptions;

/**
 * Base exception.
 *
 * @package MiniworX
 * @note Ensure this matches the semantics of \Exception.
 */
class Exception extends \Exception
{
    /**
     * JSON data.
     *
     * @var array
     */
    protected $json = array();

    /**
     * Constructor method.
     *
     * @param string     $message  The exception message.
     * @param int        $code     The exception code.
     * @param \Throwable $previous The previous exception.
     */
    public function __construct(string     $message  = "",
                                int        $code     = 0,
                                \Throwable $previous = null)
    {
        $this->json = array();

        parent::__construct($message, $code, $previous);
    }

    /**
     * Add a JSON attribute.
     *
     * @param string $key   The attribute key.
     * @param mixed  $value The attribute value.
     * @return $this
     */
    public function addAttribute(string $key, $value)
    {
        $this->json[$key] = $value;

        return $this;
    }

    /**
     * Set the JSON atrributes to a given array.
     *
     * @param array $attributes The JSON attributes.
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->json = $attributes;

        return $this;
    }

    /**
     * Returns JSON data for this exception.
     *
     * @return array
     */
    public function getJson()
    {
        return $this->json;
    }
}

/* Exception.php ends here. */
