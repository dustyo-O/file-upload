<?php
/**
 * Created by PhpStorm.
 * User: Dusty
 * Date: 13.01.2017
 * Time: 20:25
 */
error_reporting(E_ALL);
session_start();
require_once "system/core.php";

$route = isset($_GET['path']) ? $_GET['path'] : 'main/index';
$route_array = explode('/',$route);

$controller_class = controller_name($route_array[0]);
$controller_method = controller_method_name($route_array[1]);
$id = isset($route_array[2]) ? $route_array[2] : NULL;

$controller = new $controller_class();
if ($id === NULL)
{
    $controller->$controller_method();
}
else
{
    $controller->$controller_method($id);
}