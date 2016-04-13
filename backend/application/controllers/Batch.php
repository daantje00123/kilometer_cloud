<?php

require_once __DIR__.'/../classes/Backend_controller.php';

class Batch extends Backend_controller {
    public function index() {
        $action = $this->input->post('action');

        if (empty($action)) {
            redirect('');
            return;
        }

        switch($action) {
            case 'pay':
                $this->pay();
                break;

            default:
                redirect('');
                break;
        }
    }

    private function pay() {
        $routes = $this->input->post('routes');
        $this->load->model("Kilometer_model");

        foreach($routes as $key => $value) {
            $this->Kilometer_model->pay_kilometer($key);
        }

        redirect('');
    }
}
