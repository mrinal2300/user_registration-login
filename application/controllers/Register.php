<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

	public function index(){

		$this->load->model('account_model');
		$this->load->library('form_validation');
		$this->form_validation->set_message('unique_email','Email already exist!');
		$this->form_validation->set_rules('first_name', 'Firstname', 'required|min_length[3]|max_length[50]');
		$this->form_validation->set_rules('last_name', 'Lastname', 'required|min_length[3]|max_length[50]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|max_length[50]');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|min_length[6]|max_length[50]|matches[password]');
        //$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_unique_email');

        $this->form_validation->set_rules(
	        'email', 'Email',
	        array(
	                'required',
	                array($this->account_model, 'unique_email')
	        )
		);
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('header');
			$this->load->view('register');
		} else {
			$data = array(
			   'first_name' => $this->input->post('first_name') ,
			   'last_name' => $this->input->post('last_name') ,
			   'password' => md5($this->input->post('password')),
			   'email' => $this->input->post('email'),
			   'created_at' => date('Y-m-d H:i:s')
			);

			$this->account_model->register($data);
			redirect('register/success');
		}	
	}

	public function success(){
		$this->load->view('header');
		$this->load->view("register_success");
		$this->load->view('footer');
	}

}