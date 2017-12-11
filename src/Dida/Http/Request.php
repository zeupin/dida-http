<?php
/**
 * Dida Framework  -- A Rapid Development Framework
 * Copyright (c) Zeupin LLC. (http://zeupin.com)
 *
 * Licensed under The MIT License.
 * Redistributions of files must retain the above copyright notice.
 */

namespace Dida\Http;

class Request
{
    const VERSION = '20171206';

    protected static $initialized = false;

    protected static $urlinfo = null;

    protected static $post = [];
    protected static $get = [];
    protected static $cookie = [];
    protected static $session = [];
    protected static $server = [];
    protected static $headers = [];

    protected static $method = null;
    protected static $isAjax = null;
    protected static $clientIP = null;
    protected static $schema = null;


    public static function init()
    {
        self::$urlinfo = parse_url($_SERVER["REQUEST_URI"]);

        self::$urlinfo["path"] = rtrim(self::$urlinfo['path'], "/\\");

        self::initMethod();
        self::initIsAjax();
        self::initClientIP();
        self::initSchema();

        self::$post = $_POST;
        self::$get = $_GET;
        self::$cookie = $_COOKIE;
        self::$server = $_SERVER;

        self::$session = (isset($_SESSION)) ? $_SESSION : [];

        if (function_exists("apache_request_headers")) {
            $headers = apache_request_headers();
            if (is_array($headers)) {
                self::$headers = $headers;
            }
        }

        self::$initialized = false;
    }


    public static function path()
    {
        if (self::$urlinfo === false) {
            return false;
        }

        return isset(self::$urlinfo['path']) ? self::$urlinfo['path'] : null;
    }


    public static function queryString()
    {
        if (self::$urlinfo === false) {
            return false;
        }

        return isset(self::$urlinfo['query']) ? self::$urlinfo['query'] : null;
    }


    public static function fragment()
    {
        if (self::$urlinfo === false) {
            return false;
        }

        return isset(self::$urlinfo['fragment']) ? self::$urlinfo['fragment'] : null;
    }


    protected static function initMethod()
    {
        if (isset($_POST['DIDA_REQUEST_METHOD'])) {
            $method = strtolower($_POST['DIDA_REQUEST_METHOD']);
        } elseif (isset($_SERVER['REQUEST_METHOD'])) {
            $method = strtolower($_SERVER['REQUEST_METHOD']);
        }

        switch ($method) {
            case 'get':
            case 'post':
            case 'put':
            case 'patch':
            case 'delete':
            case 'head':
            case 'options':
                self::$method = $method;
                return;
            default:
                self::$method = false;
                return;
        }
    }


    public static function method()
    {
        return self::$method;
    }


    protected static function initIsAjax()
    {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            self::$isAjax = false;
            return;
        }

        self::$isAjax = (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }


    public static function isAjax()
    {
        return self::$isAjax;
    }


    protected static function initClientIP()
    {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_X_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_CLUSTER_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_X_CLUSTER_CLIENT_IP"];
        } elseif (isset($_SERVER["REMOTE_ADDR"])) {
            $ip = $_SERVER["REMOTE_ADDR"];
        } else {
            $ip = false;
        }

        self::$clientIP = $ip;
    }


    public static function clientIP()
    {
        return self::$clientIP;
    }


    protected static function initSchema()
    {
        if (isset($_SERVER['REQUEST_SCHEME'])) {
            self::$schema = $_SERVER['REQUEST_SCHEME'];
        } else {
            self::$schema = false;
        }
    }


    public static function schema()
    {
        return self::$schema;
    }


    public static function post($index = null)
    {
        if (is_null($index)) {
            return self::$post;
        }

        return self::arrayValue($index, self::$post);
    }


    public static function get($index = null)
    {
        if (is_null($index)) {
            return self::$get;
        }

        return self::arrayValue($index, self::$get);
    }


    public static function files($index = null)
    {
    }


    public static function cookie($index = null)
    {
        if (is_null($index)) {
            return self::$cookie;
        }

        return self::arrayValue($index, self::$cookie);
    }


    public static function session($index = null)
    {
        if (is_null($index)) {
            return self::$session;
        }

        return self::arrayValue($index, self::$session);
    }


    public static function server($index = null)
    {
        if (is_null($index)) {
            return self::$server;
        }

        return self::arrayValue($index, self::$server);
    }


    public static function headers($index = null)
    {
        if (is_null($index)) {
            return self::$headers;
        }

        return self::arrayValue($index, self::$headers);
    }


    public static function input($index = null)
    {
        if (is_null($index)) {
            return array_merge(self::$cookie, self::$get, self::$post);
        }

        if (array_key_exists($indx, self::$post)) {
            return self::$post[$index];
        } elseif (array_key_exists($indx, self::$get)) {
            return self::$get[$index];
        } elseif (array_key_exists($indx, self::$cookie)) {
            return self::$cookie[$index];
        } else {
            return null;
        }
    }


    public static function only($array, $indexN)
    {
        if (is_string($array)) {
            switch ($array) {
                case 'post':
                    $array = self::$post;
                    break;
                case 'get':
                    $array = self::$get;
                    break;
                case 'cookie':
                    $array = self::$cookie;
                    break;
                case 'session':
                    $array = self::$session;
                    break;
                case 'server':
                    $array = self::$server;
                    break;
                case 'headers':
                    $array = self::$headers;
                    break;
                default:
                    return false;
            }
        } elseif (!is_array($array)) {
            return false;
        }

        $result = [];

        $keys = [];
        $cnt = func_num_args();
        if ($cnt === 2) {
            if (is_array($indexN)) {
                $keys = $indexN;
            } elseif (is_string($indexN)) {
                $keys[] = $indexN;
            } else {
                return false;
            }
        } elseif ($cnt > 2) {
            for ($i = 1; $i < $cnt; $i++) {
                $index = func_get_arg($i);
                if (is_string($index) || is_int($index)) {
                    $keys[] = $index;
                } else {
                    return false;
                }
            }
        }

        foreach ($keys as $key) {
            $result[$key] = self::arrayValue($key, $array);
        }

        return $result;
    }


    public static function except($array, $indexN)
    {
        if (is_string($array)) {
            switch ($array) {
                case 'post':
                    $array = self::$post;
                    break;
                case 'get':
                    $array = self::$get;
                    break;
                case 'cookie':
                    $array = self::$cookie;
                    break;
                case 'session':
                    $array = self::$session;
                    break;
                case 'server':
                    $array = self::$server;
                    break;
                case 'headers':
                    $array = self::$headers;
                    break;
                default:
                    return false;
            }
        } elseif (!is_array($array)) {
            return false;
        }

        $result = $array;

        $keys = [];
        $cnt = func_num_args();
        if ($cnt === 2) {
            if (is_array($indexN)) {
                $keys = $indexN;
            } elseif (is_string($indexN)) {
                $keys[] = $indexN;
            } else {
                return false;
            }
        } elseif ($cnt > 2) {
            for ($i = 1; $i < $cnt; $i++) {
                $index = func_get_arg($i);
                if (is_string($index) || is_int($index)) {
                    $keys[] = $index;
                } else {
                    return false;
                }
            }
        }

        foreach ($keys as $key) {
            unset($result[$key]);
        }

        return $result;
    }


    public static function flashAll()
    {
    }


    public static function flashOnly()
    {
    }


    public static function flashExcept()
    {
    }


    protected static function arrayValue($key, array $array)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            return null;
        }
    }
}
