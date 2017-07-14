<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Forgot extends CI_Controller {

	public function index(){

		$this->load->library('form_validation');
		$this->load->model('account_model');
	     $this->form_validation->set_rules(
	        'email', 'Email',
	        array(
	                'required',
	                array('email', array($this->account_model, 'check_email'))
	                

	        ),array(                 /* Error lists */
	              'email' => 'Invalid email'
	             )
		);

	    if ($this->form_validation->run() == FALSE) {
	    	$this->load->view('header');
			$this->load->view('forget_password');

		} else {

			$token = md5(uniqid(rand(), true));

			$this->load->library('quiz');
			$email_send = $this->quiz->send_email('reset password',$this->input->post('email'), 'forget_password',array('link'=>base_url('forgot/reset_password/'.$token)));
			
			if($email_send){

				$data = array(
					'password_reset_token' => $token
				);

				$this->account_model->forget_password($data);
				redirect('forgot/emailsent');

			} else {
				show_error('error');
			}
			
		}
		
	}

	public function reset_password($token = NULL){

		$this->load->model('account_model');
		if (is_null($token)) {
			show_404();
		}
		
		if($this->account_model->check_token($token) == 0){
			redirect('error_reset_password');
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|max_length[50]');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|min_length[6]|max_length[50]|matches[password]');
        if ($this->form_validation->run() == FALSE) {

			$data = array('token' => $token);
			$this->load->view('header');
			$this->load->view('reset_password', $data);

		} else {
				
				$data = array(
					'password' => md5($this->input->post('password')),
					'password_reset_token' => ''
				);

				$user = $this->account_model->update_password($token, $data);

				$this->load->library('quiz');
				$email_send = $this->quiz->send_email('reset password done',$this->input->post('email'), 'forget_password',array('link'=>base_url('login')));
			
				
				if($email_send){
					
					redirect('forgot/errorreset');

				} else {
					show_error($this->email->print_debugger());
				}

		}

		
	}

	public function emailsent(){
		$this->load->view('header');
		$this->load->view('reset_email_success');
		$this->load->view('footer');
	}

	public function errorreset(){
		$this->load->view('header');
		$this->load->view('error_reset_password');
		$this->load->view('footer');
	}


}