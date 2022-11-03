<?php

use RedBeanPHP\R;

class UserController extends BaseController
{
    public function login()
    {
        // show login form
        if (isset($_SESSION['message'])) {
            render('login/login.twig', array(
                'message' => $_SESSION['message'],
            ));
            unset($_SESSION['message']);
        } else {
            render('login/login.twig');
        }
    }

    public function loginPost()
    {
        // check if user exists
        $username  = $_POST['name'];
        $user = R::findOne('user', 'username = ? ', [$username]);
        if ($user) {
            // check if users password is the same
            $password = password_verify($_POST['pws'], $user['pws']);
            if ($password) {
                // if true -> save id in session, redirect to recipelist
                $_SESSION['user_id'] = $user['id'];
                header("Location: http://localhost/");
                exit();
            }
        } else {
            // if false -> redirect to login screen and give error
            $error = "Invalid username or password";
            $_SESSION['message'] = $error;
            header("Location: http://localhost/user/login");
            exit();
        }
    }

    public function register()
    {
        if (isset($_SESSION['message'])) {
            render('login/register.twig', array(
                'message' => $_SESSION['message'],
            ));
            unset($_SESSION['message']);
        } else {
            render('login/register.twig');
        }
    }

    public function registerPost()
    {
        // check if all fields are filled
        if (empty($_POST["name"]) || empty($_POST["psw"]) || empty($_POST["confpsw"])) {
            // no
            $error = "All fields are required";
        } else {
            // yes
            // check for unique username
            $username  = $_POST['name'];
            $user = R::findOne('user', 'username = ? ', [$username]);
            if ($user) {
                // no
                $error = "Username is already in use";
            } else {
                // yes
                // check if passwords are the same
                if ($_POST['psw'] === $_POST['confpsw']) {
                    // yes
                    // add user to database
                    $NewUser = R::dispense('user');
                    $NewUser->username = $_POST['name'];
                    $NewUser->password = password_hash($_POST['psw'], PASSWORD_DEFAULT);
                    $id = R::store($NewUser);

                    // login user, start session
                    // redirect to recipe page

                    $_SESSION['user_id'] = $id;
                    header("Location: http://localhost/");
                    exit();
                } else {
                    // no
                    $error = "Passwords did not match";
                }
            }
        }
        if (isset($error)) {
            $_SESSION['message'] = $error;
            header("Location: http://localhost/user/register");
            exit();
        }
    }

    public function logout()
    {
        // log out
        session_destroy();
        header("Location: http://localhost/user/login");
        exit();
    }
}
