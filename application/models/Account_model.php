<?php
class Account_model extends CI_Model {

	public function register($data = array()){

		$this->db->insert('users', $data);
	}

	public function unique_email($email){

		$query = $this->db->get_where('users',array('email'=>$email));
		if ($query->num_rows() == 0) {
			return TRUE;
		} else {
			return FALSE;
		}
		
	}

	public function check_email($email){
		//check email exist to reset password

		$query = $this->db->get_where('users',array('email'=>$email));
		if ($query->num_rows() == 0) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function forget_password($data = array()){

		$this->db->where('email', $this->input->post('email'));
		$this->db->update('users', $data);
	}

	public function check_token($token){

		$query = $this->db->get_where('users',array('password_reset_token'=>$token), 1);
		return $query->num_rows();
	}

	public function check_login($email, $password){

		$query = $this->db->select('id, status')->where('email', $email)->where('password', md5($password))->get('users', 1);
		if($query->num_rows() == 0){
			return null;
		} else {
			return $query->row();
			
		}
	}


	public function update_password($token , $data = array()){
		$query = $this->db->get_where('users',array('password_reset_token'=>$token), 1);
		$this->db->where('password_reset_token', $token);
		$this->db->update('users', $data);
		return $query->row();

	}

	public function recaptcha($recaptcha){

		$url = "https://www.google.com/recaptcha/api/siteverify";
		$secret_key = "6LcjxiUUAAAAABbx5gCUFo9ZnYCf43wIkGxEJMQv";
		$response = file_get_contents($url."?secret=".$secret_key."&response=".$recaptcha."&remoteip=".$this->input->ip_address());
		$data = json_decode($response);

		if($data->success){
			return TRUE;
		} else {
			$this->form_validation->set_message('g-recaptcha-response','Please tell us you are human');
			return FALSE;
		}
	}

	public function invalid_login($email){
		$data = array(
			'email' => $email,
			'ip' => $this->input->ip_address(),
			'created_at' => date('Y-m-d H:i:s')
			);
		$this->db->insert('login_attempts', $data);
		
	}

	public function activate_recaptcha(){

		$query = $this->db->get_where('login_attempts', array('ip' => $this->input->ip_address()));
		if($query->num_rows() >= 3){
			return true;
		} else {
			return false;
		}
	}

	public function clear_login_attempts(){

		$this->db->where('ip', $this->input->ip_address())->delete('login_attempts');
	}


}