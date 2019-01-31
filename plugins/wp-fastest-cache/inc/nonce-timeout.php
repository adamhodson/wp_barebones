<?php
	class WPFC_NONCE_TIMEOUT{
		private $file_path = "";
		private $file_name = "nonce_datas.txt";
		private $prev_nonce = false;
		private $current_nonce = false;
		
		public function __construct($cache_path){
			$this->file_path = $cache_path;
			$this->set_current_nonce();
			$this->set_prev_nonce();
		}

		public function verify_nonce(){
			$verified = false;

			if($this->prev_nonce){
				if(wp_verify_nonce($this->prev_nonce, 'wpfc')){
					$verified = true;
				}else{
					$this->write();
				}
			}else{
				$verified = true;
				
				$this->write();
			}

			return $verified;
		}

		public function write(){
			if(is_dir($this->file_path)){
				@file_put_contents($this->file_path."/".$this->file_name, $this->current_nonce);
			}
		}

		public function set_current_nonce(){
			$this->current_nonce = wp_create_nonce("wpfc");
		}

		public function set_prev_nonce(){
			if(file_exists($this->file_path."/".$this->file_name)){
				if($data = @file_get_contents($this->file_path."/".$this->file_name)){
					$this->prev_nonce = $data;
				}
			}
		}
	}
?>