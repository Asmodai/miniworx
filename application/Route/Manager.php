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

use \miniworx\Application\Exceptions;

/**
 * Dispatch manager.
 *
 * @package MiniworX
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
            if (in_array('miniworx\Application\Interfaces\RouteInterface',
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

        $exception = (new Exceptions\NotImplementedException())
            ->setSourcePath($request->uri())
            ->setSourceMethod($phpMethod)
            ->setDetail(
                'The method \'' . $request->method() . '\' is not implemented.'
            );

        throw $exception;
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

        if (is_object($output) && method_exists($output, 'jsonSerialize')) {
            $request->log("jsonSerializable object.");
            $out = json_encode($output);
            goto write;
        }

        if (!\miniworx\Application\Utils\Types::isString($output)) {
            if (is_object($output)) {
                $request->log("non-serializable object.");
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
     * Find the request's route in the route tree.
     *
     * @param \miniworx\Application\Request\Request $request The request
     * @return Route
     */
    private function findRoute(&$request)
    {
        $segments = $request->uriSegments();
        $route    = $this->tree->search($segments);

        if ($route === false) {
            $exception = (new Exceptions\NotFoundException())
                ->setSourcePath($request->uri())
                ->setDetail('Could not find ' . $request->uri());

            throw $exception;
        }

        return $route;
    }

    /**
     * Handle RouteException-derived exceptions.
     *
     * @param \miniworx\Application\Request\Request $request   The request.
     * @param Exceptions\RouteException             $exception The exception.
     * @return bool
     */
    private function handleRouteException(&$request, $exception)
    {
        $errs = array();
        $out  = array();
        $code = 0;

        foreach ($exception->exceptions() as $ex) {
            $ex->setSourcePath($exception->path());
            
            if ($code === 0) {
                $code = $ex->getStatus();
            }
            
            $errs[] = $ex->getJson();
        }

        if ($code === 0) {
            $code = 500;
        }
        
        $out['errors'][] = $errs;

        $request->setStatus($code);
        return $this->emit($out, $request);
    }

    /**
     * Handle Exception-derived exceptions.
     *
     * @param \miniworx\Application\Request\Request $request   The request.
     * @param Exceptions\Exception                  $exception The exception.
     * @return bool
     */
    private function handleException(&$request, $exception)
    {
        $out  = array();
        $code = $exception->getStatus();

        $out['errors'][] = $exception->getJson();

        $request->setStatus($code);
        return $this->emit($out, $request);
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
            $route    = $this->findRoute($request);
            $segments = $request->uriSegments();
            $bindings = $route->bindings($segments);
            $method   = $this->getMethod($request, $route->instance());
            $request->setBindings($bindings);

            $out = $route->instance()->$method($request);

            if ($request->method() === 'HEAD') {
                $out = array();
            }

            return $this->emit($out, $request);
        } catch (Exceptions\RouteException $e) {
            return $this->handleRouteException($request, $e);
        } catch (Exceptions\Exception $e) {
            return $this->handleException($request, $e);
        }
    }
}

/* Manager.php ends here. */
