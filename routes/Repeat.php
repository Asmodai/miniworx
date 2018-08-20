<?php
/**
 * PHP version 7
 *
 * An example route.
 *
 * @category RepeatRoute
 * @package Routes
 * @author Paul Ward <asmodai@gmail.com>
 * @copyright 2018 Paul Ward <asmodai@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT The MIT License
 * @link https://github.com/vivi90/miniworx
 *
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

/**
 * An example route that repeats the request as the response.
 *
 * @package MiniworX
 */
class Repeat implements Interfaces\RouteInterface
{
    /**
     * Return the route path.
     *
     * @return string
     */
    public function route(): string
    {
        return "/repeat";
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
         * @api {get} /repeat Repeat GET request back as response.
         * @apiName GetRepeat
         * @apiGroup Repeat
         *
         * @apiDescription Repeat back the contents of the HTTP request.
         *
         * @apiSuccess {String} data Data block
         * @apiSuccessExample Success-Response:
         *    HTTP/1.1 200 OK
         *    {
         *      "data": {
         *        "request": {
         *          "bindings": [],
         *          "body": {},
         *          "cookies": {},
         *          "headers": {
         *            "Host": "127.0.0.1",
         *          },
         *          "method": "GET",
         *          "params": [],
         *          "protocol": "HTTP/1.1",
         *          "uri": "/repeat"
         *        }
         *      }
         *    }
         */
        $request->setStatus(200);
        
        return [
            'data' => [
                'request' => $request->expose()
            ]
        ];
    }

    /**
     * Handle the HTTP `PUT` verb for this route.
     *
     * @param \miniworx\Application\Request\Request $request The request.
     * @return array
     */
    public function put(&$request)
    {
        /**
         * @api {put} /repeat Repeat PUT request back as response.
         * @apiName PutRepeat
         * @apiGroup Repeat
         *
         * @apiDescription Repeat back the contents of the HTTP request.
         *
         * @apiSuccess {String} data Data block
         * @apiSuccessExample Success-Response:
         *    HTTP/1.1 200 OK
         *    {
         *      "data": {
         *        "request": {
         *          "bindings": [],
         *          "body": {},
         *          "cookies": {},
         *          "headers": {
         *            "Host": "127.0.0.1",
         *          },
         *          "method": "PUT",
         *          "params": [],
         *          "protocol": "HTTP/1.1",
         *          "uri": "/repeat"
         *        }
         *      }
         *    }
         */
        return $this->get($request);
    }

    /**
     * Handle the HTTP `POST` verb for this route.
     *
     * @param \miniworx\Application\Request\Request $request The request.
     * @return array
     */
    public function post(&$request)
    {
        /**
         * @api {post} /repeat Repeat POST request back as response.
         * @apiName PostRepeat
         * @apiGroup Repeat
         *
         * @apiDescription Repeat back the contents of the HTTP request.
         *
         * @apiSuccess {String} data Data block
         * @apiSuccessExample Success-Response:
         *    HTTP/1.1 200 OK
         *    {
         *      "data": {
         *        "request": {
         *          "bindings": [],
         *          "body": {},
         *          "cookies": {},
         *          "headers": {
         *            "Host": "127.0.0.1",
         *          },
         *          "method": "POST",
         *          "params": [],
         *          "protocol": "HTTP/1.1",
         *          "uri": "/repeat"
         *        }
         *      }
         *    }
         */
        return $this->get($request);
    }
}

/* Repeat.php ends here. */
