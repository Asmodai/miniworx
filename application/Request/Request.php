<?php
/**
 * PHP version 7
 *
 * Request class.
 *
 * @category Request
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

namespace miniworx\Application\Request;

use \miniworx\Application\Exceptions;

/**
 * Request class.
 *
 * @package MiniworX
 */
class Request
{
    /**
     * The parent application object.
     *
     * @var \miniworx\Application\Application
     */
    private $application = null;

    /**
     * Variable bindings.
     *
     * @var array
     */
    private $bindings = array();

    /**
     * Request body.
     *
     * @var array
     */
    private $body = array();

    /**
     * Cookie data.
     *
     * @var array
     */
    private $cookies = array();

    /**
     * Server headers.
     *
     * @var array
     */
    private $headers = array();

    /**
     * Output headers.
     *
     * @var array
     */
    private $outputHeaders = array();

    /**
     * Request method.
     *
     * @var string
     */
    private $method = null;

    /**
     * Parameters passed in via the URL.
     *
     * @var array
     */
    private $params = array();

    /**
     * Transport protocol (HTTP, HTTPS et al).
     *
     * @var string
     */
    private $protocol = null;

    /**
     * The URI in array form.
     *
     * @var array
     */
    private $segments = array();

    /**
     * The path and parameters URI components.
     *
     * @var string
     */
    private $uri = null;

    /**
     * HTTP status code.
     *
     * @var int
     */
    private $status = 0;

    /**
     * Cloning disabled.
     *
     * @return void
     *
     * @SuppressWarnings(UnusedPrivateMethod)
     */
    private function __clone()
    {
    }

    /**
     * Serialisation disabled.
     *
     * @return void
     *
     * @SuppressWarnings(UnusedPrivateMethod)
     */
    private function __sleep(): void
    {
    }

    /**
     * Deserialisation disabled.
     *
     * @return void
     *
     * @SuppressWarnings(UnusedPrivateMethod)
     */
    private function __wakeup(): void
    {
    }

    /**
     * Constructor method.
     *
     * @param \miniworx\Application\Application $app The parent application.
     *
     * @SuppressWarnings(SuperGlobals)
     * @SuppressWarnings(StaticAccess)
     */
    public function __construct(\miniworx\Application\Application $app = null)
    {
        $this->application = $app;
        $this->uri         = $_SERVER['REQUEST_URI'];
        $this->protocol    = getenv('SERVER_PROTOCOL');

        $this->validateRequestMethod($_SERVER['REQUEST_METHOD']);
        $this->setPostParameters();
        $this->setCookieParameters();
        $this->explodeUri();
        $this->getHeaders();

        // Sanitize the URI now.
        $this->uri
            = \miniworx\Application\Utils\Sanitation::sanitizeUrl($this->uri);
    }

    /**
     * Expose this object as an associated array that can be JSON-encoded.
     *
     * @return array An array of key/value pairs.
     */
    public function expose(): array
    {
        return [
            'bindings' => $this->bindings,
            'body'     => $this->body,
            'cookies'  => $this->cookies,
            'headers'  => $this->headers,
            'method'   => $this->method,
            'params'   => $this->params,
            'protocol' => $this->protocol,
            'uri'      => $this->uri
        ];
    }

    /**
     * Returns the request URI.
     *
     * @return string The request URI.
     */
    public function uri(): string
    {
        return $this->uri;
    }

    /**
     * Returns the request URI as an array of path segments.
     *
     * @return array An array containing the URI path segments.
     */
    public function uriSegments(): array
    {
        return $this->segments;
    }

    /**
     * Returns the request method.
     *
     * @return string The request method.
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * Returns the request body.
     *
     * @return array The request body.
     */
    public function body(): array
    {
        return $this->body;
    }

    /**
     * Set variable bindings forr this request.
     *
     * @param array $bindings An array of key/value binding pairs.
     * @return Request
     */
    public function setBindings(&$bindings): Request
    {
        $this->bindings = $bindings;

        return $this;
    }

    /**
     * Get variable bindings for this request.
     *
     * @return array An array of key/value binding pairs.
     */
    public function bindings(): array
    {
        return $this->bindings;
    }

    /**
     * Add an output header to this request.
     *
     * @param string $header The header name.
     * @param mixed  $value  The header value.
     * @return Request
     */
    public function addHeader($header, $value): Request
    {
        $this->outputHeaders[$header] = $value;

        return $this;
    }

    /**
     * Return the output headers for this request.
     *
     * @return array The array of key/value header pairs.
     */
    public function outputHeaders(): array
    {
        return $this->outputHeaders;
    }

    /**
     * Remove the application's path prefix from the URI.
     *
     * @param string $path The URI from which the prefix is removed.
     * @return string
     */
    private function removePrefix($path): string
    {
        $prefix = $this->application->pathPrefix();

        if (empty($prefix)) {
            return $path;
        }

        return str_replace($prefix, '', $path);
    }

    /**
     * Parses the URI into both an array of path segments and an array of
     * parameters.
     *
     * @return void
     *
     * @SuppressWarnings(StaticAccess);
     */
    private function explodeUri(): void
    {
        if (!isset($this->uri)) {
            return;
        }

        $path           = $this->removePrefix($this->uri);
        $split          = explode('?', $path);
        $this->segments = explode('/', rtrim(ltrim($split[0], '/'), '/'));

        if (isset($split[1])) {
            $list = explode('&', $split[1]);

            foreach ($list as $elem) {
                $pair = explode('=', $elem);

                if (isset($pair[1])) {
                    $this->params[$pair[0]]
                        = \miniworx\Application\Utils\Sanitation::sanitizeUrl(
                            $pair[1]
                        );
                }
            }
        }
    }

    /**
     * Validate a request method.
     *
     * @param string $input The method to validate.
     * @return void
     */
    private function validateRequestMethod(string &$input): void
    {
        static $methods;

        if (!isset($methods)) {
            $methods = array(
                'GET', 'POST', 'PUT', 'DELETE', 'HEAD'
            );
        }

        if (empty($input)) {
            throw new Exceptions\InvalidArgumentException(
                "No input method given."
            );
        }

        if (!in_array($input, $methods)) {
            throw new Exceptions\InvalidArgumentException(
                "${input} is not a valid HTTP method."
            );
        }

        $this->method = $input;
    }

    /**
     * Set POST parameters.
     *
     * If the app is run via the CLI, then it takes the contents of the
     * `HTTP_POST` environment variable, which should be a list of parameters
     * in the form `var1=value1&varN=valueN&...`.
     *
     * @return void
     */
    private function setPostParameters(): void
    {
        $params = filter_input_array(INPUT_POST);

        if (!isset($params)) {
            $this->body = (object)array();
            return;
        }

        $this->body = $params;
    }

    /**
     * Set cookie data.
     *
     * @return void
     */
    private function setCookieParameters(): void
    {
        $params = filter_input_array(INPUT_COOKIE);

        if (!isset($params)) {
            $this->cookies = (object)array();
            return;
        }

        $this->cookies = $params;
    }

    /**
     * Get request headers.
     *
     * @return void
     */
    private function getHeaders(): void
    {
        $this->headers = apache_request_headers();
    }

    /**
     * Set the request's HTTP status code.
     *
     * @param int $code The HTTP status code to set.
     * @return Request
     */
    public function setStatus($code): Request
    {
        $this->status = $code;

        return $this;
    }

    /**
     * Return the current HTTP status code.
     *
     * @return int The request's HTTP status code.
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * Print a message to the log in debug mode.
     *
     * @param mixed $message The message to print.
     * @return Request
     */
    public function log($message): Request
    {
        if (isset($this->application)) {
            $this->application->log($message);
        }

        return $this;
    }
}

/* Request.php ends here. */
