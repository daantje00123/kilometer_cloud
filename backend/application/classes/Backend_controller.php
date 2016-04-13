<?php

require_once __DIR__.'/../../system/core/Controller.php';

class Backend_controller extends CI_Controller {
    public function __construct() {
        parent::__construct();

        db_user_loggedin(true);

        if (db_get_user()->get_role() != 'ROLE_ADMIN') {
            redirect('');
            die();
        }
    }
}