<?php
/**
 * Dida Framework  -- A Rapid Development Framework
 * Copyright (c) Zeupin LLC. (http://zeupin.com)
 *
 * Licensed under The MIT License.
 * Redistributions of files must retain the above copyright notice.
 */

namespace Dida\Http;

class Ajax
{
    const VERSION = '20171128';


    public static function json($data)
    {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode($data);
    }


    public static function jsonp($data, $callback)
    {
        header('Content-Type:application/json; charset=utf-8');
        echo $callback . "(" . json_encode($data) . ");";
    }
}
