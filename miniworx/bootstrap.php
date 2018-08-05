<?php
/**
 * PHP version 7
 *
 * Base filter class.
 *
 * @category Classes
 * @package Classes
 * @author Vivien Richter <vivien-richter@outlook.de>
 * @copyright 2018 Vivien Richter <vivien-richter@outlook.de>
 *
 * @license https://opensource.org/licenses/MIT The MIT License
 * @link https://github.com/vivi90/miniworx
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
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$requiredPhpVersion = '7.0.0';
if (version_compare(PHP_VERSION, $requiredPhpVersion) < 0) {
    die('Require PHP ' . $requiredPhpVersion . ' or above.');
}

date_default_timezone_set('UTC');

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);
spl_autoload_register('autoloader');
spl_autoload_extensions('.php');

/**
 * Autoloader hack.
 *
 * @param string $class The class to load.
 * @return void Nothing.
 */
function autoloader($class)
{
    $fname  = str_replace('\\', '/', $class) . '.php';
    $stream = stream_resolve_include_path($fname);
    if ($stream) {
        include_once $stream;
        return;
    }

    error_log("Cannot find class '${class}'.");
    error_log("Looking in ${fname}.");
    error_log("Looking at ${stream}.");
    error_log("Include path: '" . get_include_path() . "'");
}
