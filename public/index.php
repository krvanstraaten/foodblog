<?php

session_start();

try {
    require_once '../vendor/autoload.php';

    // take apart url
    if ($_REQUEST) {
        $paramsArr = explode("/", $_REQUEST['params']);
    }

    // check for controller or assign one
    if (isset($paramsArr[1])) {
        $controller = ucwords($paramsArr[1]) . 'Controller';
    } else {
        $controller = 'RecipeController';
    }

    // check for method or assign one
    if (isset($paramsArr[2])) {
        $method = $paramsArr[2];
    } else {
        $method = 'index';
    }

    // check if class exists
    if (class_exists($controller) && method_exists($controller, $method)) {
        // call controller method
        $call = new $controller();

        // check for id or assign one
        if (!isset($_REQUEST['id']) && $method !== "show") {
            return $call->$method();
        } elseif (isset($_REQUEST['id'])) {
            return $call->$method($_REQUEST['id']);
        } else {
            throw new Exception("No $paramsArr[1] ID specified");
        }
    } else {
        if (!class_exists($controller)) {
            throw new Exception("Controller '" . $controller . "' not found");
        } elseif (!method_exists($controller, $method)) {
            throw new Exception("Method '" . $method . "' not found");
        }
    }
} catch (Exception $e) {
    // call error page
    if (isset($_REQUEST['id'])) {
        $errorNumber = $_REQUEST['id'];
    } else {
        $errorNumber = null;
    }
    $errorMessage = $e->getMessage();
    return error($errorNumber, $errorMessage);
    die();
}
