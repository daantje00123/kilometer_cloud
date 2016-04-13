<?php

if (!function_exists('db_setting')) {
    function db_setting($key) {
        if (empty($key)) {
            return null;
        }

        $CI = &get_instance();

        $CI->load->model("Settings_model");

        return $CI->Settings_model->get_setting($key)['se_value'];
    }
}