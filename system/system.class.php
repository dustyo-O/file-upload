<?php
/**
 * Created by PhpStorm.
 * User: dusty
 * Date: 23.01.17
 * Time: 20:57
 */

class System
{
    public static function post($key = NULL, $default = NULL)
    {
        if ($key !== NULL)
        {
            if (isset($_POST[$key])) return $_POST[$key];

            return $default;
        }

        return $_POST;
    }

    public static function setMessage($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function getMessage($key)
    {
        $result = NULL;
        if (isset($_SESSION[$key]))
        {
            $result = $_SESSION[$key];

            unset($_SESSION[$key]);
        }

        return $result;
    }
}