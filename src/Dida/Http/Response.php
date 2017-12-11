<?php
/**
 * Dida Framework  -- A Rapid Development Framework
 * Copyright (c) Zeupin LLC. (http://zeupin.com)
 *
 * Licensed under The MIT License.
 * Redistributions of files must retain the above copyright notice.
 */

namespace Dida\Http;

class Response
{
    const VERSION = '20171129';

    const HTML_TYPE = 'html';
    const TEXT_TYPE = 'text';
    const JSON_TYPE = 'json';
    const JSONP_TYPE = 'jsonp';

    protected static $cookie = [];

    protected static $session = [];

    protected static $data = [];

    protected static $content = [];


    public static function redirect($url, $refresh = null)
    {
        if (is_numeric($refresh)) {
            header("Cache-control: no-cache");
            header("Refresh: $refresh; url=$url");
        } else {
            header("Cache-control: no-cache");
            header("Location: $url", true, 307);
            exit();
        }
    }


    public static function json($data)
    {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode($data);
    }


    public static function jsonp($data, $callback)
    {
        header('Content-Type:application/json; charset=utf-8');
        echo "$callback(" . json_encode($data) . ");";
    }


    public static function download($srcfile, $name = null, $mime = false)
    {
        if (file_exists($srcfile)) {
            $realfile = $srcfile;
        } else {
            $realfile = iconv('UTF-8', 'GBK', $srcfile);
            if (!file_exists($realfile)) {
                return false;
            }
        }



        if (!is_string($name)) {
            $name = $srcfile;
        }
        $name = str_replace('\\', '/', $name);
        $basename = mb_strrchr($name, '/');
        if ($basename) {
            $name = mb_substr($basename, 1);
        }

        $name = rawurlencode($name);

        if ($mime) {
            $mimetype = mime_content_type($realfile);
        } else {
            $mimetype = 'application/force-download';
        }

        $filesize = filesize($realfile);

        header("Content-Type: $mimetype");
        header("Content-Disposition: attachment; filename*=\"$name\"");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header("Content-Length: $filesize");
        ob_clean();
        flush();
        readfile($realfile);
        exit();
    }
}
