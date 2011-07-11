<?php
class SecureStream {
	public $playlist;
	public $stream_id;
	public $email_address;
	public $full_name = '';
	public $track_link_name = 'track.mp3';
	public $error_msg = null;
	public $logged_in = false;
	private $login_details = null;
	private $include_file;
	
	public function __construct($ss_stream_id,$ss_email_address,$ss_password,$ss_playlist,$include_file='./path/to/core/core.php') {
		$this->playlist = $ss_playlist;
		$this->stream_id = $ss_stream_id;
		$this->email_address = $ss_email_address;
		
		$this->include_file = $include_file;
		
		if ($this->verifyLogin($ss_password)) {
			$this->logged_in = true;
		}
	}

	public function verifyLogin($ss_password) {
		include($this->include_file);
		$login_details = getSecureStreamLoginInformation($this->email_address,$ss_password,$this->stream_id);
		if (is_array($login_details)) {
			$this->login_details = $login_details;
			$this->full_name = $login_details['first_name'] . ' ' . $login_details['last_name'];
			return true;
		} else {
			$this->error_msg = "Incorrect email/password combination.";
			return false;
		}
	}
	
	public function buildLimitString() {
		if ($this->login_details) {
			$limited_logins = false;
			$limit_string = '';
			if ($this->login_details['allowed_logins'] > -1) { 
				$limit_string .= ($this->login_details['allowed_logins'] - $this->login_details['total_logins'] - 1) . ' log-ins remaining';
				$limited_logins = true;
			}
			if ($this->login_details['date_expires'] > -1) { 
				if ($limited_logins) {
					$limit_string .=  ' before ';
				} else {
					$limit_string .=  'Login expires ';
				}
				$limit_string .= date('M j',$this->login_details['date_expires']);
			}
			return $limit_string;
		} else {
			return false;
		}
	}
	
	public function buildLinkString() {
		$link_string = '';
		foreach ($this->playlist['data'] as $key => $track) {
			$link_string .= '<a href="track.mp3?w=' . $key . '">' . $track['title'] . '</a>';
		}
		return $link_string;
	}
}
?>