<?php

require_once __DIR__.'/../classes/Backend_controller.php';

class Settings extends Backend_controller {
    public function __construct() {
        parent::__construct();

        $this->load->model("Settings_model");
    }

    public function index() {
        db_load_view('settings/all', array('settings' => $this->Settings_model->get_settings()));
    }

    public function edit($key = '') {
        if (empty($key)) {
            redirect('settings');
            return;
        }

        $setting = $this->Settings_model->get_setting($key);

        if (empty($setting)) {
            redirect('settings');
            return;
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('value', 'waarde', 'required|max_length[100]');
        $this->form_validation->set_rules('desc', 'omschrijving', 'required');

        if ($this->form_validation->run() === false) {
            db_load_view('settings/edit', array('setting' => $setting));
            return;
        }

        $value = $this->input->post('value');
        $desc = $this->input->post('desc');

        $this->Settings_model->edit_setting($key, $value, $desc);

        redirect('settings');
    }
}