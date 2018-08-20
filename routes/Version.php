<?php
/**
 * PHP version 7
 *
 * Server version route.
 *
 * @category VersionRoute
 * @package Routes
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

namespace miniworx\Routes;

use \miniworx\Application\Interfaces;
use \miniworx\Application\Document;

/**
 * REST server version route.
 *
 * @package MiniworX
 */
class Version implements Interfaces\RouteInterface
{
    /**
     * Return the route path.
     *
     * @return string
     */
    public function route(): string
    {
        return '/version';
    }

    /**
     * Handle the HTTP `GET` verb for this route.
     *
     * @param \miniworx\Application\Request\Request $request The request.
     * @return array
     */
    public function get(&$request)
    {
        /**
         * @api {get} /version
         * @apiName GetVersion
         * @apiGroup Version
         *
         * @apiDescription Returns the current version of the MiniworX RESTful
         *                 server.
         *
         * @apiSuccess {Array} version Version number data.
         * @apiSuccessExample Success-Response:
         *    HTTP/1.1 200 OK
         *    {
         *      "version": {
         *        "major": 0,
         *        "minor": 1,
         *        "patch": 0
         *      }
         *    }
         */
        $result = [
            'version' => \miniworx\Application\Version::instance()->version(),
        ];

        $request->setStatus(200);

        return new Document\Document($result);
    }
}

/* Version.php ends here. */
