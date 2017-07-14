<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

	public function view(){

		$query = $this->db->get_where('users', array('id' => $this->session->userdata('id')), 1);

		$data = array(
			'user' => $query->row() 
			);

		$this->load->view('header');
		$this->load->view('view_profile', $data);
		$this->load->view('footer');
	}


}