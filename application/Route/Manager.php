<?php
/**
 * PHP version 7
 *
 * Dispatch manager.
 *
 * @category Classes
 * @package Classes
 * @author Paul Ward <asmodai@gmail.com>
 * @copyright 2018 Paul Ward <asmodai@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT The MIT License
 * @link https://www.github.com/...
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

use \miniworx\Application\Exceptions;

/**
 * Dispatch manager.
 *
 * @package Vendor/Project
 */
class Manager
{
    /** @var \miniworx\Application\Route\Tree Route tree. */
    private $tree = null;

    /** @var array Array of route instances. */
    private $instances = array();

    /**
     * Constructor method.
     */
    public function __construct()
    {
        $this->tree = new Tree();
        $this->start();
    }

    /**
     * Starts the route manager.
     *
     * @return void Nothing.
     */
    private function start()
    {
        foreach (get_declared_classes() as $class) {
            if (in_array('miniworx\Application\Route\RoutableInterface',
                         class_implements($class))
            ) {
                $inst              = new $class();
                $path              = $inst->route();
                $route             = new Route($path, $inst);
                $this->instances[] = $route;

                $this->tree->insert($route->path(), $route);
            }
        }
    }

    /**
     * Determine whether the given routeable class has a method that handles
     * the HTTP method of the given request.
     *
     * @param \miniworx\Application\Request\Request $request The request.
     * @param mixed                                 $class   The class.
     * @return callable|false The method if found; otherwise false.
     */
    public function getMethod($request, $class)
    {
        $phpMethod = strtolower($request->method());

        // We hack HEAD support later on.
        if ($phpMethod === 'head') {
            $phpMethod = 'get';
        }

        if (method_exists($class, $phpMethod)) {
            return $phpMethod;
        }

        return false;
    }

    /**
     * Emit a response to the client.
     *
     * @param mixed                                 $output  The output.
     * @param \miniworx\Application\Request\Request $request The request.
     * @return bool
     */
    private function emit(&$output, &$request)
    {
        $out = $output;

        if (!\miniworx\Application\Utils\Types::isString($output)) {
            if (is_object($output)) {
                $out = json_encode(get_object_vars($output));
                goto write;
            }

            $out = json_encode($output);
        }

        write:

        // TODO: Validation!
        foreach ($request->outputHeaders() as $key => $val) {
            header($key . ':' . $val);
        }

        http_response_code($request->status());
        header('Content-Type: application/vnd.api+json');
        header('Content-Length: ' . strlen($out));
        echo $out;

        return true;
    }

    /**
     * Resolve a request.
     *
     * @param \miniworx\Application\Request\Request $request The request
     * @return bool
     */
    public function resolve(&$request)
    {
        try {
            $segments = $request->uriSegments();
            $route    = $this->tree->search($segments);

            if ($route === false) {
                $request->setStatus(404);
                $msg = "404";
                return $this->emit($msg, $request);
            }

            $bindings = $route->bindings($segments);
            $method   = $this->getMethod($request, $route->instance());
            $request->setBindings($bindings);

            if (!$method) {
                $request->setStatus(404);
                $msg = "404";
                return $this->emit($msg, $request);
            }

            $out = $route->instance()->$method($request);

            if ($request->method() === 'HEAD') {
                $out = array();
            }

            return $this->emit($out, $request);
        } catch (Exceptions\RouteException $e) {
            $errs = array();
            $out  = array();

            $out['path'] = $e->path();

            foreach ($e->exceptions() as $ex) {
                $errs[] = [
                    'message' => $ex->getMessage(),
                    'values'  => $ex->getJson()
                ];
            }

            $out['errors'] = $errs;

            $request->setStatus(500);
            return $this->emit($out, $request);
        }
    }
}

/* Manager.php ends here. */
