<?php

require_once __DIR__.'/../classes/Backend_controller.php';

class Batch extends Backend_controller {
    public function index() {
        $action = $this->input->post('action');
        $referer = (!empty($this->input->post('referer')) ? $this->input->post('referer') : base_url());

        if (empty($action)) {
            redirect($referer);
            return;
        }

        switch($action) {
            case 'pay':
                $this->pay($referer);
                break;

            case 'not_pay':
                $this->not_pay($referer);
                break;

            case 'delete':
                $this->delete($referer);
                break;

            default:
                redirect($referer);
                break;
        }
    }

    private function pay($referer) {
        $routes = $this->input->post('routes');
        $this->load->model("Kilometer_model");

        foreach($routes as $key => $value) {
            $this->Kilometer_model->pay_kilometer($key, 1);
        }

        redirect($referer);
    }

    private function not_pay($referer) {
        $routes = $this->input->post('routes');
        $this->load->model("Kilometer_model");

        foreach($routes as $key => $value) {
            $this->Kilometer_model->pay_kilometer($key, 0);
        }

        redirect($referer);
    }

    private function delete($referer) {
        $routes = $this->input->post('routes');
        $this->load->model("Kilometer_model");

        foreach($routes as $key => $value) {
            $this->Kilometer_model->delete_kilometer($key);
        }

        redirect($referer);
    }
}
