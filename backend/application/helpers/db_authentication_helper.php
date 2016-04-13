<?php

require_once __DIR__.'/../classes/User_obj.php';

if (!function_exists('db_user_loggedin')) {
    function db_user_loggedin($redirect = false) {
        require_once(__DIR__.'/../classes/User_obj.php');

        if (
            !isset($_SESSION['loggedin']) ||
            !isset($_SESSION['user']) ||
            empty($_SESSION['loggedin']) ||
            empty($_SESSION['user']) ||
            $_SESSION['loggedin'] != true
        ) {
            if ($redirect) {
                $_SESSION['login_referer'] = current_url();
                redirect('authentication');
            }

            return false;
        }

        $user = unserialize($_SESSION['user']);

        if(empty($user->get_id_user())) {
            if ($redirect) {
                $_SESSION['login_referer'] = current_url();
                redirect('authentication');
            }

            return false;
        }

        return true;
    }
}

if (!function_exists('db_get_user')) {
    function db_get_user() {
        if (!db_user_loggedin()) {
            return new User_obj(array());
        }

        return unserialize($_SESSION['user']);
    }
}