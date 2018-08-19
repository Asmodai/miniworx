<?php
/**
 * PHP version 7
 *
 * Exception raised for route errors.
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
 * Exception raised for route errors.
 *
 * @package MiniworX
 */
class RouteException extends \Exception
{
    /**
     * Route path.
     *
     * @var array
     */
    private $path = array();
    
    /**
     * Child exceptions.
     *
     * @var array
     */
    private $exceptions = array();

    /**
     * Constructor method.
     *
     * @param string $path The route path..
     */
    public function __construct($path)
    {
        $this->path       = $path;
        $this->exceptions = array();
    }

    /**
     * Returns the route path.
     *
     * @return string
     */
    public function path()
    {
        return '/' . implode('/', $this->path);
    }
    
    /**
     * Add a child exception.
     *
     * @param Exception $exception Child exception
     * @return $this
     */
    public function addException(Exception $exception)
    {
        $this->exceptions[] = $exception;
        
        return $this;
    }

    /**
     * Set the child exceptions.
     *
     * @param array $exceptions The child exceptions.
     * @return $this
     */
    public function setExceptions(array $exceptions)
    {
        $this->exceptions = $exceptions;
        
        return $this;
    }

    /**
     * Return the child exceptions.
     *
     * @return array
     */
    public function exceptions()
    {
        return $this->exceptions;
    }
}

/* RouteException.php ends here. */
