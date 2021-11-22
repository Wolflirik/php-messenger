<?php

namespace system\helper;

class Cookie{

    /**
     * @param $cookie
     * @param $key
     * @return null
     */
    public static function get($cookie, $key)
    {
        if(isset($cookie[$key])) {
            return $cookie[$key];
        }
        return null;
    }

    /**
     * @param $key
     * @param $val
     * @param int $time
     */
    public static function set($key, $val, $path = '/', $time = 42000, $domain = '', $secure = true, $http_only = true)
    {
        setcookie($key, base64_encode(serialize($val)), time() + $time, $path, $domain, $secure, $http_only);
    }

    public static function delete($key, $path = '/', $domain = '')
    {
        if(isset($_COOKIE[$key])) {
            self::set($key, '', time() - 42000, $path, $domain);
            unset($_COOKIE[$key]);
        }
    }
}