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
     * @param int    $status The HTTP status code.
     * @param string $title  The error title.
     * @param string $detail The error detail.
     * @param int    $code   The application-specific error code.
     */
    public function __construct(int    $status = 500,
                                string $title  = "",
                                string $detail = "",
                                int    $code   = 0)
    {
        parent::__construct($detail, $code);

        $this->json = array();

        $this->json['status'] = $status;
        $this->json['title']  = $title;
        $this->json['detail'] = $detail;

        if ($code !== 0) {
            $this->json['code'] = $code;
        }
    }

    /**
     * Set the exception title.
     *
     * @param string $title The exception title.
     * @return Exception
     */
    public function setTitle(string $title)
    {
        $this->json['title'] = $title;

        return $this;
    }

    /**
     * Set the exception detail.
     *
     * @param string $detail The exception detail.
     * @return Exception
     */
    public function setDetail(string $detail)
    {
        $this->json['detail'] = $detail;
        $this->message        = $detail;

        return $this;
    }

    /**
     * Set the exception code.
     *
     * @param int $code The exception code.
     * @return Exception
     */
    public function setCode(int $code)
    {
        $this->json['code'] = $code;
        $this->code         = $code;

        return $this;
    }

    /**
     * Returns the exception's HTTP status code.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->json['status'];
    }

    /**
     * Returns the exception's title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->json['title'];
    }

    /**
     * Add a JSON attribute.
     *
     * @param string $key   The attribute key.
     * @param mixed  $value The attribute value.
     * @return Exception
     */
    public function addAttribute(string $key, $value)
    {
        $this->json[$key] = $value;

        return $this;
    }

    /**
     * Add a JSON attribute to the exception's 'source' array.
     *
     * @param string $key   The attribute key.
     * @param mixed  $value The attribute value.
     * @return Exception
     */
    public function addSourceAttribute(string $key, $value)
    {
        if (!isset($this->json['source'])) {
            $this->json['source'] = array();
        }

        $this->json['source'][$key] = $value;
    }

    /**
     * Set the error source parameter.
     *
     * @param string $param The source parameter.
     * @return Exception
     */
    public function setSourceParameter(string $param)
    {
        if (!isset($this->json['source'])) {
            $this->json['source'] = array();
        }

        $this->json['source']['parameter'] = $param;

        return $this;
    }

    /**
     * Set the error source path.
     *
     * @param string $path The source path.
     * @return Exception
     */
    public function setSourcePath(string $path)
    {
        if (!isset($this->json['source'])) {
            $this->json['source'] = array();
        }

        $this->json['source']['path'] = $path;

        return $this;
    }

    /**
     * Set the error source pointer.
     *
     * @param string $pointer The source pointer.
     * @return Exception
     */
    public function setSourcePointer(string $pointer)
    {
        if (!isset($this->json['source'])) {
            $this->json['source'] = array();
        }

        $this->json['source']['pointer'] = $pointer;

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
