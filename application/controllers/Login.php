<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Login extends CI_Controller {

	public function index(){

		if ($this->session->userdata('is_loggedin')) {
			redirect('/');
		}
		$this->load->library('form_validation');
		$this->load->model('account_model');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|max_length[50]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $g_recaptcha = $this->account_model->activate_recaptcha();
        $data['g_recaptcha'] = $g_recaptcha;
        if($g_recaptcha){
       
	        $this->form_validation->set_rules('g-recaptcha-response', /* Field */
	        'Captcha',              /* Label */
	         array(                 /* Rules */
	                'required',
	                array('my_recaptcha', array($this->account_model, 'recaptcha'))
	              ),
	        array(                 /* Error lists */
	              'my_recaptcha' => 'Invalid Recaptcha'
	             )
			);
		}
        

		if ($this->form_validation->run() == FALSE) {
			$this->load->view('header');
			$this->load->view('login', $data);
			$this->load->view('footer');

		} else {
			$user = $this->account_model->check_login($this->input->post('email'), $this->input->post('password'));
			if(!is_null($user)){
				if($user->status == 0){
					$this->session->set_flashdata('error_message', 'Inactive account');
					redirect('login');
				} else {
					$this->account_model->clear_login_attempts();
					$this->session->set_userdata(array('id' => $user->id, 'is_loggedin' => TRUE));
					redirect('/');
				}
			} else {
				$this->account_model->invalid_login($this->input->post('email'));
				$this->session->set_flashdata('error_message', 'Invalid login');
				redirect('login');
			}			
		}
	}

	public function logout(){

		$this->session->sess_destroy();
		redirect('login');
	}
}