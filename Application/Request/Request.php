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

/**
 * Request class.
 *
 * @package MiniworX
 */
class Request
{
    /** @var array Variable bindings. */
    private $bindings = array();
    
    /** @var array Request body. */
    private $body = array();

    /** @var array Cookie data. */
    private $cookies = array();
    
    /** @var array Headers. */
    private $headers = array();    
    
    /** @var array Output headers. */
    private $outputHeaders = array();
    
    /** @var string The request method. */
    private $method = null;

    /** @var array Parameters passed in via the URL. */
    private $params = array();
    
    /** @var Transport protocol (HTTP, HTTPS et al). */
    private $protocol = null;
   
    /** @var array The URI in array form. */
    private $segments = array();
    
    /** @var string The path and parameters URI components. */
    private $uri = null;
    
    /** @var int HTTP status code. */
    private $status = 0;

    /**
     * Cloning disabled.
     *
     * @return void Nothing.
     *
     * @SuppressWarnings(UnusedPrivateMethod)
     */
    private function __clone()
    {
    }

    /**
     * Serialisation disabled.
     *
     * @return void Nada.
     *
     * @SuppressWarnings(UnusedPrivateMethod)
     */
    private function __sleep()
    {
    }

    /**
     * Deserialisation disabled.
     *
     * @return void Zilch.
     *
     * @SuppressWarnings(UnusedPrivateMethod)
     */
    private function __wakeup()
    {
    }

    /**
     * Constructor method.
     *
     * @SuppressWarnings(SuperGlobals)
     * @SuppressWarnings(StaticAccess)
     */
    public function __construct()
    {
        $this->uri      = $_SERVER['REQUEST_URI'];
        $this->protocol = getenv('SERVER_PROTOCOL');
        
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
    public function expose()
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
    public function uri()
    {
        return $this->uri;
    }

    /**
     * Returns the request URI as an array of path segments.
     *
     * @return array An array containing the URI path segments.
     */
    public function uriSegments()
    {
        return $this->segments;
    }

    /**
     * Returns the request method.
     *
     * @return string The request method.
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * Returns the request body.
     *
     * @return array The request body.
     */
    public function body()
    {
        return $this->body;
    }
    
    /**
     * Set variable bindings forr this request.
     *
     * @param array $bindings An array of key/value binding pairs.
     * @return void Nothing.
     */
    public function setBindings(&$bindings)
    {
        $this->bindings = $bindings;
    }
    
    /**
     * Get variable bindings for this request.
     *
     * @return array An array of key/value binding pairs.
     */
    public function bindings()
    {
        return $this->bindings;
    }

    /**
     * Add an output header to this request.
     *
     * @param string $header The header name.
     * @param mixed  $value  The header value.
     * @return void Nothing.
     */
    public function addHeader($header, $value)
    {
        $this->outputHeaders[$header] = $value;
    }
    
    /**
     * Return the output headers for this request.
     *
     * @return array The array of key/value header pairs.
     */
    public function outputHeaders()
    {
        return $this->outputHeaders;
    }

    /**
     * Parses the URI into both an array of path segments and an array of
     * parameters.
     *
     * @return void Side effects, baby!
     *
     * @SuppressWarnings(StaticAccess);
     */
    private function explodeUri()
    {
        if (!isset($this->uri)) {
            return;
        }

        $split          = explode('?', $this->uri);
        $this->segments = explode('/', rtrim(ltrim($split[0], '/'), '/'));
        $this->uri      = $split[0];

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
     * @return void We love side effects.
     */
    private function validateRequestMethod(string &$input)
    {
        static $methods;

        if (!isset($methods)) {
            $methods = array(
                'GET', 'POST', 'PUT', 'DELETE', 'HEAD'
            );
        }

        if (empty($input)) {
            throw new \InvalidArgumentException(
                "No input method given."
            );
        }

        if (!in_array($input, $methods)) {
            throw new \InvalidArgumentException(
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
     * @return void Side effects are great.
     */
    private function setPostParameters()
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
     * @return void Side effects are great.
     */
    private function setCookieParameters()
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
     * @return void Uses side effects.
     */
    private function getHeaders()
    {
        $this->headers = apache_request_headers();
    }
    
    /**
     * Set the request's HTTP status code.
     *
     * @param int $code The HTTP status code to set.
     * @return int The requested HTTP status code.
     */
    private function setStatus($code)
    {
        return ($this->status = $code);
    }

    /**
     * Return the current HTTP status code.
     *
     * @return int The request's HTTP status code.
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * Set the request's HTTP status code to '200' (OK).
     *
     * @return int The requested HTTP status code.
     */
    public function ok()
    {
        return $this->setStatus(200);
    }
}

/* Request.php ends here. */
