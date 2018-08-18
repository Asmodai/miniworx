<?php
/**
 * PHP version 7
 *
 * Base filter class.
 *
 * @category Bootstrap
 * @package MiniworX
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
 
declare(strict_types=1);

namespace miniworx;

/**
 * Bootstrap class.
 *
 * @package MiniworX
 */
class Bootstrap
{
    /** @var bool Are we running in debug-mode? */
    private $debug = false;

    /** @var string The minimum required PHP version. */
    private $minimumPhpVersion = '7.1.0';

    /** @var int Error reporting level. */
    private $errorReporting = 0;

    /** @var int Display errors? */
    private $displayErrors = 0;

    /** @var int Display startup errors? */
    private $displayStartupErrors = 0;

    /** @var string Default time zone. */
    private $defaultTimeZone = 'UTC';

    /** @var \miniworx\Application Application object. */
    private $application = null;

    /**
     * Constructor method.
     *
     * @SuppressWarnings(ExitExpression)
     */
    public function __construct()
    {
        if (version_compare(PHP_VERSION, $this->minimumPhpVersion) < 0) {
            die(
                'MiniworX requires PHP ' . $this->minimumPhpVersion .
                ' or higher.'
            );
        }

        $this->application = null;

        $this->detectMode();
        $this->configure();
        $this->loadVendor();
        $this->preflight();
    }

    /**
     * Detect how PHP is running and set various flags.
     *
     * @return void Nothing.
     */
    private function detectMode()
    {
        switch (php_sapi_name()) {
            case 'cli':
            case 'cli-server':
                $this->debug                = true;
                $this->errorReporting       = E_ALL;
                $this->displayErrors        = 1;
                $this->displayStartupErrors = 1;
                break;

            default:
                $this->debug                = false;
                $this->errorReporting       = 0;
                $this->displayErrors        = 0;
                $this->displayStartupErrors = 0;
                break;
        }
    }

    /**
     * Configure PHP variables and settings.
     *
     * @return void Nothing.
     */
    private function configure()
    {
        error_reporting($this->errorReporting);

        ini_set('display_errors',         $this->displayErrors);
        ini_set('display_startup_errors', $this->displayStartupErrors);

        date_default_timezone_set($this->defaultTimeZone);
    }

    // Keep PHPCS_SecurityAudit quiet.
    // @codingStandardsIgnoreStart

    /**
     * Load vendor autoloader.
     *
     * @return void Nothing.
     */
    private function loadVendor()
    {
        include_once __DIR__ . '/../vendor/autoload.php';
    }

    /**
     * Load route classes.
     *
     * @return void Nothing.
     */
    private function preflight()
    {
        foreach (glob(__DIR__ . '/../routes/*.php') as $file) {
            include_once $file;
        }
    }

    // @codingStandardsIgnoreEnd

    /**
     * Return the default time zone.
     *
     * @return string The default time zone name.
     */
    public function defaultTimeZone()
    {
        return $this->defaultTimeZone;
    }

    /**
     * Are we currently running in debug mode?
     *
     * @return bool true if we are in debug mode; otherwise false.
     */
    public function isDebugMode()
    {
        return $this->debug;
    }

    /**
     * Print a message to the console.
     *
     * In debug mode, this will print out the message via the SAPI handler to
     * the console.  This will be a no-op in production code.
     *
     * @param string $message The message to display.
     * @return void Nothing.
     */
    public function log(string $message)
    {
        if ($this->debug) {
            error_log('DEBUG:' . $message, 4);
        }
    }

    /**
     * Start the application.
     *
     * @return void Nothing.
     */
    public function run()
    {
        $this->application = new Application();

        $this->application->run();
    }

    /**
     * Returns the current application object.
     *
     * @return \miniworx\Application The application object.
     */
    public function application()
    {
        return $this->application;
    }
}

/* bootstrap.php ends here. */
