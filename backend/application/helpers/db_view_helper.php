<?php

if(!function_exists('db_load_view')) {
    function db_load_view($path, $data = array()) {
        $CI = &get_instance();

        $CI->load->view('layout/header', $data);
        $CI->load->view($path, $data);
        $CI->load->view('layout/footer', $data);
    }
}