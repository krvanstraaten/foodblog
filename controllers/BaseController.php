<?php

use RedBeanPHP\R;

require_once '../seeder.php';

class BaseController extends \RedBeanPHP\SimpleModel
{
    public function getBeanById($typeOfBean, $queryStringKey)
    {
        $bean  = R::findOne($typeOfBean, ' id = ? ', [$queryStringKey]);
        return $bean;
    }

    public function authorizeUser()
    {
        //  check if user is logged in and limit access for anonymous users
        if (!isset($_SESSION['user_id']) || is_null($_SESSION['user_id'])) {
            header("Location: http://localhost/user/login");
            exit;
        }
    }
}
