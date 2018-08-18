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

namespace miniworx;

/**
 * Application class.
 *
 * @package MiniworX
 */
class Application
{
    public $routeManager = null;

    /**
     * Constructor method.
     */
    public function __construct()
    {
        $this->setup();
        $this->routeManager = new \miniworx\Application\Route\Manager();
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
        $request = new Application\Request\Request();
        $this->routeManager->resolve($request);
    }
}

/* Application.php ends here. */
