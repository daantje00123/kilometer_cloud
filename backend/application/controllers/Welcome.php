<?php

require_once __DIR__.'/../classes/Backend_controller.php';

class Welcome extends Backend_controller {

	public function index()
	{
		$this->load->model('Kilometer_model');

		$kilometers = $this->Kilometer_model->get_kilometers();
		$total = 0;

		foreach($kilometers as $i => $kilometer) {
			if ($kilometer['betaald'] != 1) {
				$total += $kilometer['kms'];
			}
		}

		db_load_view('welcome', array(
			'kilometers' => $kilometers,
			'total' => $total
 		));
	}
}
