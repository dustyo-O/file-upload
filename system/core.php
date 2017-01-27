<?php
/**
 * Created by PhpStorm.
 * User: dusty
 * Date: 23.01.17
 * Time: 19:35
 */
require_once "system/system.class.php";
require_once "system/template.class.php";
require_once "system/controller.class.php";
require_once "system/model.class.php";

function controller_name($route)
{
    return ucfirst($route).'Controller';
}

function controller_method_name($route)
{
    return 'action'.ucfirst($route);
}


function core__autoload($name)
{
    if (file_exists('controller/'.$name.'.php'))
    {
        require_once 'controller/'.$name.'.php';
    }
    else
    {
        if (file_exists('model/'.$name.'.php'))
        {
            require_once 'model/'.$name.'.php';
        }
    }
}

spl_autoload_register('core__autoload');

require_once "vendor/autoload.php";