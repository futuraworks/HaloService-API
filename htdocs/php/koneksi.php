<?php
	date_default_timezone_set("Asia/Jakarta");  
	class koneksi{		
		public $db_host = "mariadb.infra-futuraworks.svc.cluster.local";
		public $db_user = "root";
		public $db_pass = "bad455";
		public $db_name = "futuraworks";
		public $mysqli;
		public function __construct(){				
			$this->mysqli = mysqli_connect($this->db_host,$this->db_user,$this->db_pass,$this->db_name);
		}
	}	
	$koneksi = new koneksi();

	class myFetch{
		public $sql;
		public $row;
		public $fetch;
		public function __construct($q){
			$koneksi = new koneksi();
			$this->sql = $koneksi->mysqli->query($q);
			@$this->row = $this->sql->num_rows;
		}
	}
?>
