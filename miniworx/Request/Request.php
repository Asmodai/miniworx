<?php
/**
 * PHP version 7
 *
 * Request class.
 *
 * @category Classes
 * @package Classes
 * @author Paul Ward <asmodai@gmail.com>
 * @copyright 2018 Paul Ward <asmodai@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT The MIT License
 * @link https://github.com/vivi90/miniworx
 *
 * Created:    04 Aug 2018 21:53:41
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

namespace miniworx\Request;

/**
 * Request class.
 *
 * @category Classes
 * @package Classes
 * @author Paul Ward <asmodai@gmail.com>
 * @copyright 2018 Paul Ward <asmodai@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License
 * @link https://github.com/vivi90/miniworx
 */
class Request
{
    /** @var string The request method. */
    private $method = null;

    /** @var string The path and parameters URI components. */
    private $uri = null;

    /** @var array The URI in array form. */
    private $segments = array();

    /** @var array Parameters passed in via the URL. */
    private $params = array();

    /** @var array POST data. */
    private $data = array();

    /** @var array Cookie data. */
    private $cookies = array();

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
        $this->validateRequestMethod($_SERVER['REQUEST_METHOD']);
        $this->validateRequestUri($_SERVER['REQUEST_URI']);
        $this->setPostParameters();
        $this->setCookieParameters();
        $this->explodeUri();

        // Sanitize the URI now.
        echo "Hmm, URL at: " . $this->uri . PHP_EOL;
        $this->uri = \miniworx\Utils\Sanitation::sanitizeUrl($this->uri);
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
     * Returns the request POST data.
     *
     * @return array The request POST data.
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Parses the URI into both an array of path segments and an array of
     * parameters.
     *
     * @return void Side effects, baby!
     */
    private function explodeUri()
    {
        if (!isset($this->uri)) {
            return;
        }

        $split          = explode('?', $this->uri);
        $this->segments = explode('/', rtrim($split[0], '/'));
        $this->uri      = $split[0];

        if (isset($split[1])) {
            $list = explode('&', $split[1]);

            foreach ($list as $elem) {
                $pair = explode('=', $elem);

                if (isset($pair[1])) {
                    $this->params[$pair[0]] = $pair[1];
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
     * Validate a request URI.
     *
     * @param string $input The URI to validate.
     * @return void Nothing.
     *
     * @SuppressWarnings(StaticAccess)
     */
    private function validateRequestUri(string &$input)
    {
        $this->uri = rtrim(substr($input, 1), '/');
    }

    /**
     * Set POST parameters.
     *
     * If the app is run via the CLI, then it takes the contents of the
     * `HTTP_POST` environment variable, which should be a list of parameters
     * in the form 'var1=value1&varN=valueN&...'.
     *
     * @return void Side effects are great.
     */
    private function setPostParameters()
    {
        switch (php_sapi_name()) {
            case 'cli':
                $env   = getenv('HTTP_POST');
                $split = explode('&', $env);
                $array = array();

                foreach ($split as $thing) {
                    $vals = explode('=', $thing);

                    if (isset($vals[1])) {
                        $array[$vals[0]] = $vals[1];
                    }
                }

                $params = filter_var_array($array);
                break;

            default:
                $params = filter_input_array(INPUT_POST);
                break;
        }

        if (!isset($params)) {
            $this->data = (object)array();
            return;
        }

        $this->data = $params;
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
            echo "No cookies!" . PHP_EOL;
            $this->cookies = (object)array();
            return;
        }

        $this->cookies = $params;
    }
}

/* Request.php ends here. */
