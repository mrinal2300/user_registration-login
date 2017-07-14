<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Account_old extends CI_Controller {

	public function index(){

		if (!$this->session->userdata('is_loggedin')) {
			redirect('account/login');
		}
		$this->load->view("account");
	}

	public function register(){

		$this->load->model('account_model');
		$this->load->library('form_validation');
		$this->form_validation->set_message('unique_email','Email already exist!');
		$this->form_validation->set_rules('first_name', 'Firstname', 'required|min_length[3]|max_length[50]');
		$this->form_validation->set_rules('last_name', 'Lastname', 'required|min_length[3]|max_length[50]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|max_length[50]');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|min_length[6]|max_length[50]|matches[password]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_unique_email');

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
			redirect('account/register_success');
		}	
	}

	public function unique_email($email){

		$this->load->model('account_model');
		if ($this->account_model->unique_email($email) == 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function register_success(){
		$this->load->view('header');
		$this->load->view("register_success");
		$this->load->view('footer');
	}

	public function login(){

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
			$user = $this->check_login($this->input->post('email'), $this->input->post('password'));
			if(!is_null($user)){
				if($user->status == 0){
					$this->session->set_flashdata('error_message', 'Inactive account');
					redirect('account/login');
				} else {
					$this->account_model->clear_login_attempts();
					$this->session->set_userdata(array('id' => $user->id, 'is_loggedin' => TRUE));
					redirect('/');
				}
			} else {
				$this->account_model->invalid_login($this->input->post('email'));
				$this->session->set_flashdata('error_message', 'Invalid login');
				redirect('account/login');
			}			
		}
	}


	private function check_login($email, $password){

		$query = $this->db->select('id, status')->where('email', $email)->where('password', md5($password))->get('users', 1);
		if($query->num_rows() == 0){
			return null;
		} else {
			return $query->row();
			
		}
	}

	public function logout(){

		$this->session->sess_destroy();
		redirect('account/login');
	}
/////////////////////////////////////////////////////////////////////////////

	public function forget_password(){

		$this->load->library('form_validation');
		$this->load->model('account_model');
		$this->form_validation->set_message('check_email','Invalid email');
	    $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email');

	    if ($this->form_validation->run() == FALSE) {
	    	$this->load->view('header');
			$this->load->view('forget_password');

		} else {

			$token = md5(uniqid(rand(), true));

			$this->load->library('quiz');
			$email_send = $this->quiz->send_email('reset password',$this->input->post('email'), 'forget_password',array('link'=>base_url('account/reset_password/'.$token)));
			
			if($email_send){

				$data = array(
					'password_reset_token' => $token
				);

				$this->account_model->forget_password($data);
				redirect('account/reset_password_email_success');

			} else {
				show_error('error');
			}
			
		}
		
	}

	public function reset_email_success(){
		$this->load->view('header');
		$this->load->view('reset_email_success');
		$this->load->view('footer');
	}

	public function check_email($email){
		//check email exist to reset password

		$this->load->model('account_model');
		if ($this->account_model->unique_email($email) == 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}


	public function reset_password($token = NULL){

		$this->load->model('account_model');
		if (is_null($token)) {
			show_404();
		}
		
		if($this->account_model->check_token($token) == 0){
			redirect('account/error_reset_password');
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
				$email_send = $this->quiz->send_email('reset password done',$this->input->post('email'), 'forget_password',array('link'=>base_url('account/login')));
			
				
				if($email_send){
					
					redirect('account/reset_password_success');

				} else {
					show_error($this->email->print_debugger());
				}

		}

		
	}

	public function reset_password_success(){
		$this->load->view('header');
		$this->load->view('reset_password_success');
		$this->load->view('footer');
	}

	public function error_reset_password(){
		$this->load->view('header');
		$this->load->view('error_reset_password');
		$this->load->view('footer');
	}

}





