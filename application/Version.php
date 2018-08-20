<?php
/**
 * PHP version 7
 *
 * Version information.
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

namespace miniworx\Application;

/**
 * Version information.
 *
 * @package MiniworX
 */
final class Version
{
    /**
     * Major version number.
     */
    const MAJOR_VERSION = 0;

    /**
     * Minor version number.
     */
    const MINOR_VERSION = 1;

    /**
     * Patch version number.
     */
    const PATCH_VERSION = 0;

    /**
     * Private constructor.
     */
    private function __construct()
    {
    }

    /**
     * Returns the singleton instance for this class.
     *
     * @return Version
     */
    public static function instance()
    {
        static $inst = null;

        if ($inst === null) {
            $inst = new Version();
        }

        return $inst;
    }

    /**
     * Return the current version number as an array.
     *
     * @return array An array.
     */
    public function version()
    {
        static $version = null;

        if ($version === null) {
            $version = [
                'major' => self::MAJOR_VERSION,
                'minor' => self::MINOR_VERSION,
                'patch' => self::PATCH_VERSION
            ];
        }

        return $version;
    }

    /**
     * Return the current version number as a string.
     *
     * @return string A string.
     */
    public function __toString()
    {
        static $str = null;

        if ($str === null) {
            $str = implode('.', $this->version());
        }

        return $str;
    }
}

/* Version.php ends here. */
