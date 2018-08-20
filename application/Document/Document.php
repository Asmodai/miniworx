<?php
/**
 * PHP version 7
 *
 * JSON API subset document.
 *
 * @category Classes
 * @package Classes
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

namespace miniworx\Application\Document;

use JsonSerializable;

/**
 * JSON API subset document.
 *
 * @package MiniworX
 */
class Document implements JsonSerializable
{
    /**
     * Media type.
     */
    const MEDIA_TYPE = 'application/vnd.api+json';

    /**
     * Error data.
     *
     * @var array
     */
    protected $errors;

    /**
     * Document data.
     *
     * @var array
     */
    protected $data;

    /**
     * Constructor method.
     *
     * @param mixed $data Document data
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * Sets the document data.
     *
     * @param mixed $data The data.
     * @return $this
     */
    public function setData($data): Document
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Sets the document errors.
     *
     * @param mixed $errors The errors.
     * @return $this
     */
    public function setErrors($errors): Document
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Coerce to a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Coerce to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $document = [];

        if (!empty($this->data)) {
            $document['data'] = is_array($this->data)
                ? $this->data
                : $this->data->toArray();
        }

        if (!empty($this->errors)) {
            $document['errors'] = is_arrary($this->errors)
                ? $this->errors
                : $this->errors->toArray();
        }

        return $document;
    }

    /**
     * Returns serialized JSON.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

/* Document.php ends here. */
