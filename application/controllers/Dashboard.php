<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function index(){

		if (!$this->session->userdata('is_loggedin')) {
			redirect('login');
		}

		$query = $this->db->get_where('users', array('id' => $this->session->userdata('id')), 1);

		$data = array(
			'user' => $query->row() 
			);

		$this->load->view('header');
		$this->load->view('dashboard', $data);
		$this->load->view('footer');
	}
}