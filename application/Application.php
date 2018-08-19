<?php
/**
 * PHP version 7
 *
 * Base filter class.
 *
 * @category Application
 * @package MiniworX
 * @author Vivien Richter <vivien-richter@outlook.de>
 * @copyright 2018 Vivien Richter <vivien-richter@outlook.de>
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

namespace miniworx\Application;

/**
 * Application class.
 *
 * @package MiniworX
 */
class Application
{
    /**
     * The route manager object.
     *
     * @var \miniworx\Application\Route\Manager
     */
    private $routeManager = null;
    
    /**
     * The object that bootstrapped the application.
     *
     * @var \miniworx\Application\Bootstrap
     */
    private $bootstrap = null;

    /**
     * Constructor method.
     *
     * @param Application\Bootstrap $bootstrap The bootstrap object.
     */
    public function __construct(&$bootstrap)
    {        
        $this->bootstrap    = $bootstrap;
        $this->routeManager = new \miniworx\Application\Route\Manager();
        
        $this->setup();
    }

    /**
     * Perform pre-flight setup..
     *
     * @return void Nothing.
     */
    private function setup()
    {
        // Custom setup here.
    }

    /**
     * Starts the application.
     *
     * @return void Nothing
     */
    public function run()
    {
        $request = new Request\Request();
        $this->routeManager->resolve($request);
    }
    
    /**
     * Print a message to the log in debug mode.
     *
     * @param mixed $message The message to print.
     * @return void Nothing.
     */
    public function log($message)
    {
        $this->bootstrap->log($message);
    }
}

/* Application.php ends here. */
