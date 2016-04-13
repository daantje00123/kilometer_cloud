<?php

require_once __DIR__.'/../classes/Backend_controller.php';

class Welcome extends Backend_controller {

	public function index()
	{
		redirect('welcome/page');
	}

	public function page($offset = 0) {
		if (empty($offset)) {
			$offset = 0;
		}

		$this->load->model('Kilometer_model');

		$kilometers = $this->Kilometer_model->get_kilometers(10, $offset);

		$total = 0;

		$this->load->library('pagination');

		$config['base_url'] = base_url('welcome/page');
		$config['total_rows'] = $this->Kilometer_model->count_kilometers();
		$config['per_page'] = 10;
		$config['prev_tag_open'] = '<li class="page-item">';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li class="page-item">';
		$config['next_tag_close'] = '</li>';
		$config['num_tag_open'] = '<li class="page-item">';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="page-item"><a class="page-link">';
		$config['cur_tag_close'] = '</a></li>';
		$config['attributes'] = array('class' => 'page-link');

		$this->pagination->initialize($config);

		foreach($kilometers as $i => $kilometer) {
			if ($kilometer['betaald'] != 1) {
				$total += $kilometer['kms'];
			}
		}

		db_load_view('welcome', array(
			'kilometers' => $kilometers,
			'total' => $this->Kilometer_model->total_kilometers(),
			'price' => $this->Kilometer_model->total_price(),
			'pagination' => $this->pagination->create_links()
		));
	}
}
