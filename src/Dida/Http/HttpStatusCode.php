<?php
/**
 * Dida Framework  -- A Rapid Development Framework
 * Copyright (c) Zeupin LLC. (http://zeupin.com)
 *
 * Licensed under The MIT License.
 * Redistributions of files must retain the above copyright notice.
 */

namespace Dida\Http;

class HttpStatusCode
{
    const VERSION = '20171206';


    public static function validate($http_status_code)
    {
        if (!is_int($http_status_code)) {
            if (!is_numeric($http_status_code)) {
                return false;
            };

            $code = intval($http_status_code);
            if ("$code" !== "$http_status_code") {
                return false;
            }
        } else {
            $code = $http_status_code;
        }

        if ($code > 99 && $code < 700) {
            return true;
        } else {
            return false;
        }
    }


    public static function send($response_code)
    {
        http_response_code($response_code);
    }
}
