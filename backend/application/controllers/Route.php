<?php

require_once __DIR__.'/../classes/Backend_controller.php';

class Route extends Backend_controller {
    public function __construct() {
        parent::__construct();

        $this->load->model('Kilometer_model');
    }

    public function view($id = 0) {
        if (empty($id)) {
            redirect('');
            return;
        }

        $kilometer = $this->Kilometer_model->get_kilometer($id);

        db_load_view('route/view', array(
            'kilometer' => $kilometer
        ));
    }

    public function edit($id = 0) {
        $id = (int) $id;

        if (empty($id)) {
            return null;
        }

        $kilometer = $this->Kilometer_model->get_kilometer($id);

        $this->load->library('form_validation');

        $this->form_validation->set_rules('omschrijving', 'omschrijving', 'required|max_length[100]');
        $this->form_validation->set_rules('betaald', 'betaald', 'required|in_list[0,1]');

        if ($this->form_validation->run() === false) {
            db_load_view('route/edit', array('kilometer' => $kilometer));
            return;
        }

        $omschrijving = $this->input->post('omschrijving');
        $betaald = $this->input->post('betaald');

        $this->Kilometer_model->edit_kilometer($id, $omschrijving, $betaald);

        redirect('');
    }

    public function delete($id = 0) {
        if (empty($id)) {
            redirect('');
            return;
        }

        $action = $this->input->post('action');

        if (empty($action)) {
            db_load_view('route/delete.php');
            return;
        }

        if ($action == "Ja") {
            $this->Kilometer_model->delete_kilometer($id);
        }

        redirect('');
    }
}