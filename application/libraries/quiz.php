<?php
class Quiz {

	function __construct(){
		$this->CI = & get_instance();
	}
	
	function send_email($subject,$to,$type,$data = array()){

		$this->CI->load->library('email');
		$this->CI->load->library('parser');
		$html = $this->CI->parser->parse('emails/account/'.$type, $data, TRUE);

		$this->CI->email->from('xxx@zzz.com'); 
		$this->CI->email->to($to);
		$this->CI->email->subject($subject);
		$this->CI->email->message($html);

		if($this->CI->email->send()){
			
			return TRUE;

		} else {
			return FALSE;
		}

	}
}