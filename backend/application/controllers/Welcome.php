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

		$per_page = (!empty($this->input->get('per_page')) ? $this->input->get('per_page') : 10);
		$order = (!empty($this->input->get('order')) ? strtolower($this->input->get('order')): 'desc');

		if ($per_page < 1) {
			$per_page = 1;
		}

		if ($order != 'desc' && $order != 'asc') {
			$order = 'desc';
		}

		$this->load->model('Kilometer_model');

		$kilometers = $this->Kilometer_model->get_kilometers($per_page, $offset, $order);

		$total = 0;

		$this->load->library('pagination');

		$config['base_url'] = base_url('welcome/page');
		$config['total_rows'] = $this->Kilometer_model->count_kilometers();
		$config['per_page'] = $per_page;
		$config['prev_tag_open'] = '<li class="page-item">';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li class="page-item">';
		$config['next_tag_close'] = '</li>';
		$config['num_tag_open'] = '<li class="page-item">';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="page-item"><a class="page-link">';
		$config['cur_tag_close'] = '</a></li>';
		$config['attributes'] = array('class' => 'page-link');
		$config['suffix'] = '?per_page='.$per_page.'&order='.$order;
		$config['first_url'] = base_url('welcome/page?per_page='.$per_page.'&order='.$order);

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
			'pagination' => $this->pagination->create_links(),
			'order' => $order,
			'per_page' => $per_page
		));
	}
}
