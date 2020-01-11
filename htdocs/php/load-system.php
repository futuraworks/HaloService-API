<?php
	include_once 'php/siteconfig.php';
	include_once 'php/koneksi.php';
	include_once 'php/load-array.php';
	if(@$_SESSION['errorlog'] == null){
		hideError();
	}
	if(@$_SESSION['refreshCount'] == null){
		$_SESSION['refreshCount'] = 0;
	}
	if(@$_SESSION['refreshCount'] > 0){
		$_SESSION['refreshCount'] = 0;
	} else {
		$_SESSION['refreshCount']++;
	}
	if(@$_SESSION['refreshCount'] == 1){		
		$_SESSION['lookerror'] = '';
	}
	function namaBulan($nomor){
		$namabulan = array('Januari',
						   'Februari',
						   'Maret',
						   'April',
						   'Mei',
						   'Juni',
						   'Juli',
						   'Agustus',
						   'September',
						   'Oktober',
						   'November',
						   'Desember');
		return $namabulan[$nomor-1];
	}
	/* Config Ruang Ujian PHP */
	if(@$_GET['go'] == 'ruangujian' && @$_GET['confirm'] == true){
		unset($_SESSION['jawaban_user']); // Reset Sesi DATA JAWABAN sebelumnya
		$_SESSION['jawaban_user'] = array(); // Buat ARRAY pada sesi
		for ($i=0; $i < $jmlsoal; $i++) { 
			$_SESSION['jawaban_user'][$i] = "-"; // Atur jawaban peserta dengan strip pada awal masuk ruang ujian
		}
		$npm = @$_SESSION['npm'];
		$idk = @$_SESSION['kursus'];
		$url = "?go=ruangujian";
		// Cek lembar jawab praktikan
		$bscq = "SELECT * FROM tb_lembarjawab WHERE npm='$npm' && id_kursus='$idk'";
		$bscsql = new myFetch($bscq);
		if($bscsql->row == 0){
			/* Batasi Soal */
			$dipakeSoal = $jmlsoal;
			/* */
			/* Cek total soal kursus */
			$totalSoalQ = "SELECT id_soal AS id FROM tb_soal";
			$totalSoalSQL = new myFetch($totalSoalQ);
			$totalSoal = $totalSoalSQL->row;
			$mulaiAcak = rand(0,($totalSoal-$dipakeSoal)); // 66 - 30 = 36
			/* Acak soal untuk ujian */
			$soalAcakQ = "SELECT id_soal AS id FROM tb_soal ORDER BY RAND() LIMIT $mulaiAcak,$dipakeSoal";
			$soalAcakSQL = new myFetch($soalAcakQ);
			$encryptArray = "";
			$semuaSoalDanJawaban = array();
			/* Masukkan ID soal ke dalam ARRAY */
			while($soalAcakF = $soalAcakSQL->sql->fetch_array(MYSQLI_ASSOC)){
				$id_soal = $soalAcakF['id'];
				$soalDanJawaban = array();
				array_push($soalDanJawaban, $id_soal);
				$ambilJawabanQ = "SELECT id_jawaban AS id FROM tb_jawaban WHERE id_soal='$id_soal' ORDER BY RAND()";
				$ambilJawabanSQL = new myFetch($ambilJawabanQ);
				while ($ambilJawabanF = $ambilJawabanSQL->sql->fetch_array(MYSQLI_ASSOC)) {
					array_push($soalDanJawaban, $ambilJawabanF['id']);
				}
				array_push($semuaSoalDanJawaban, $soalDanJawaban);
			}
			/* Acak data pada ARRAY */
			$encryptArray = serialize($semuaSoalDanJawaban);
			/* Buat lembar jawab praktikan */
			$tanggal_skrg = date("Y-m-d H:i:s");
			$bsq = "INSERT INTO tb_lembarjawab VALUES('$npm','$idk','$encryptArray',NULL,NULL,'$tanggal_skrg',0,0,0)";
			$bssql = new myFetch($bsq);
		} else {
			myRedirect($url);
		}
		/* UPDATE status praktikan */
		$updateDataQ = "UPDATE tb_jadwalpraktikan SET kondisi='progress' WHERE npm='$npm' AND id_kursus='$idk'";
		$updateDataSQL = new myFetch($updateDataQ);
		if($updateDataSQL->sql){
			myRedirect($url);
		} else {
			createError("Gagal memasuki ruang ujian, <b>silahkan coba kembali</b>",
						"user",
						"warning",
						"yes");	
			myRedirect("./");
		}
	}
	/* Config Review */	
	if(@$_GET['go'] == 'review' && @$_GET['confirm'] == true){
		$idk = @$_SESSION['kursus'];
		$npm = $_SESSION['npm'];
		$url = "?go=finish";
		/* Update praktikan */
		$updateDataQ = "UPDATE tb_jadwalpraktikan SET kondisi='finish' WHERE npm='$npm' AND id_kursus='$idk'";
		$updateDataSQL = new myFetch($updateDataQ);
		/* Kumpulkan jawaban dari sesi ke database */
		$jawaban = serialize($_SESSION['jawaban_user']);
		/* Update lembar jawab praktikan */
		$tanggal_skrg = date("Y-m-d H:i:s");
		$updateJawabanQ = "UPDATE tb_lembarjawab SET jawab = '$jawaban',tgl_jawab='$tanggal_skrg' WHERE npm = '$npm' AND id_kursus='$idk'";
		$updateJawabanSQL = new myFetch($updateJawabanQ);
		/* Ambil soal dari lembar jawab praktikan */
		$ambilSoalQ = "SELECT soal AS q FROM tb_lembarjawab WHERE npm='$npm' AND id_kursus='$idk'";
		$ambilSoalSQL = new myFetch($ambilSoalQ);
		$ambilSoalF = $ambilSoalSQL->sql->fetch_array(MYSQLI_ASSOC);
		$soalpraktikan = unserialize($ambilSoalF['q']);
		/* --- */
		$jawabanbenar = 0;
		$jawabansalah = 0;
		$nilai = 0;
		/* -- */
		/* Hitung jawaban yang benar dan salah */
		for($j=0; $j < count($soalpraktikan); $j++){
			$soal = $soalpraktikan[$j][0];
			$jawab = $_SESSION['jawaban_user'][$j];
			$ambilSoalQ = "SELECT jawaban as a FROM tb_soal WHERE id_soal = '$soal'";
			$ambilSoalSQL = new myFetch($ambilSoalQ);
			$ambilSoalF = $ambilSoalSQL->sql->fetch_array(MYSQLI_ASSOC);
			$jawaban = $ambilSoalF['a'];
			if($jawaban == $jawab){
				$jawabanbenar++;
			} else {
				$jawabansalah++;
			}
			$nilai = round(($jawabanbenar/$jmlsoal)*100);
			/* -- */
		}
		/* Update lembar jawab praktikan */
		$updateJawabanQ = "UPDATE tb_lembarjawab SET nilai='$nilai',benar='$jawabanbenar',salah='$jawabansalah' WHERE npm = '$npm' AND id_kursus='$idk'";
		$updateJawabanSQL = new myFetch($updateJawabanQ);
		if($updateDataSQL->sql){
			myRedirect($url);
		} else {
			myRedirect("?go=review");
		}
	}
	/* Bagian Notifikasi */
	function hideError(){
		$_SESSION['errorlog'] = "";
		$_SESSION['errorlogid'] = "";
		$_SESSION['errorcolor'] = "";
		$_SESSION['lookerror'] = "";
	}
	function createError($log,
						 $id,
						 $color,
						 $show){
		@$_SESSION['errorlog'] = $log;
		@$_SESSION['errorlogid'] = $id;
		@$_SESSION['errorcolor'] = $color;
 		@$_SESSION['lookerror'] = $show;
 		@$_SESSION['refreshCount'] = -1;
	}
	/* Config Logout */
	if(@$_GET['get'] == 'logout'){
		if(@$_SESSION['login']){
			createError($_SESSION['namalengkap']." berhasil keluar..",
						"user",
						"success",
						"yes");
			$_SESSION['npm'] = '';
			$_SESSION['namalengkap'] = '';
			$_SESSION['nopc'] = '';
			$_SESSION['login'] = false;
			myRedirect("./");
		}
	}
	/* Config Untuk Cek Sesi saat terakhir login */
	if(@$_SESSION['login']){
		$idk = $_SESSION['kursus'];
		$npm = $_SESSION['npm'];
		$query = "SELECT kondisi as k FROM tb_jadwalpraktikan WHERE npm='$npm' AND id_kursus='$idk'";
		$sql = new myFetch($query);
		$fetch = $sql->sql->fetch_array(MYSQLI_ASSOC);
		$stat = $fetch['k'];
		if($stat == 'progress'){
			if(@$_GET['go'] == 'ruangujian' OR @$_GET['go'] == 'review' OR @$_GET['go'] == 'finish'){

			} else {
				myRedirect("?go=ruangujian");
			}
		} else if($stat == 'finish'){
			if(@$_GET['go'] == 'finish'){} else {
			myRedirect("?go=finish");
			}
		}
	}
	/* Config Login */
	if(@$_GET['get'] == 'login'){
		$npm = $_POST['npm'];
		$namalengkap = $_POST['namalengkap'];
		$nopc = $_POST['nopc'];
		$q = "SELECT * FROM tb_praktikan
			  WHERE npm='$npm' 
			  ORDER BY namalengkap 
			  ASC";
		$sql = new myFetch($q);
		if ($sql->row !== 0){
			/* cek apakah praktikan login sesuai jadwal */
			$tglskrg = date('Y-m-d');
			$ckursusq = "SELECT * FROM tb_jadwalpraktikan INNER JOIN 
						 tb_kursus ON tb_jadwalpraktikan.id_kursus = tb_kursus.id_kursus WHERE tb_jadwalpraktikan.npm='$npm'";
			$ckursussql = new myFetch($ckursusq);
			if($ckursussql->row !== 0){
				while ($ckursusf = $ckursussql->sql->fetch_array(MYSQLI_ASSOC)) {
					$mulai = $ckursusf['tanggal_mulai']; 
					$selesai = $ckursusf['tanggal_selesai'];
					if($tglskrg >= $mulai AND $tglskrg <= $selesai){
						$kursus = $ckursusf['id_kursus'];
						$fetch = $sql->sql->fetch_array(MYSQLI_ASSOC);
						if($ckursusf['nopc'] == 0){
							$cekpcq = "SELECT * FROM tb_jadwalpraktikan WHERE nopc='$nopc' AND id_kursus='$kursus'";
							$cekpcsql = new myFetch($cekpcq);
							if($cekpcsql->row == 0){
								$_SESSION['npm'] = $npm;
								$_SESSION['namalengkap'] = $namalengkap;
								$_SESSION['nopc'] = $nopc;
								$_SESSION['kursus'] = $kursus;
								$_SESSION['login'] = true;
								$q2 = "UPDATE tb_jadwalpraktikan 
									   SET nopc='$nopc' 
									   WHERE npm='$npm' AND id_kursus='$kursus'";
								$sql2 = new myFetch($q2);
								createError($_SESSION['namalengkap']." Berhasil login",
											"user",
											"success",
											"yes");	
							} else {
								createError("PC sudah ada yang menempati, <b>silahkan hubungi asisten</b>",
											"user",
											"warning",
											"yes");					
							}
						} elseif($ckursusf['nopc'] == $nopc){
							$_SESSION['npm'] = $npm;
							$_SESSION['namalengkap'] = $namalengkap;
							$_SESSION['nopc'] = $nopc;
							$_SESSION['kursus'] = $kursus;
							$_SESSION['login'] = true;
							createError('<b>'.$_SESSION['namalengkap']."</b> Berhasil login",
										"user",
										"success",
										"yes");							
						} else {
							createError("<b>Anda login di PC yang berbeda</b>, silahkan login dengan PC yang sudah terdaftar",
										"user",
										"success",
										"yes");
						}
					} else {
						createError("Cek kembali jadwal praktek atau <b>silahkan hubungi asisten</b>",
									"user",
									"warning",
									"yes");
					}
				}
			} else {
				createError("Tidak ada kursus yang terdaftar, <b>silahkan hubungi asisten</b>",
							"user",
							"warning",
							"yes");
			}
		} else {
			createError("Praktikan belum terdaftar pada sistem, <b>silahkan hubungi asisten</b>",
						"user",
						"warning",
						"yes");						
			$sql->sql->close();
		}		
		myRedirect('./');
		$koneksi->mysqli->close();
	}
	/* Bagian profil */
	if(@$_GET['go'] == 'profil' && @$_GET['type'] == 'edit' && @$_GET['get'] == 'edit'){
		$npmold = $_SESSION['npm'];
		$cekq = "SELECT * FROM tb_praktikan WHERE npm='$npmold'";
		$ceksql = new myFetch($cekq);
		if($ceksql->row !== 0){
			$npm = $_POST['npm'];
			$nama = $_POST['nama'];
			$cekq = "SELECT * FROM tb_praktikan WHERE npm='$npm'";
			$ceksql = new myFetch($cekq);
			if($ceksql->row == 0 OR $npmold == $npm){
				$q = "UPDATE tb_praktikan SET npm='$npm',namalengkap='$nama' WHERE npm='$npmold'";
				$sql = new myFetch($q);
				$_SESSION['npm'] = $npm;
				$_SESSION['namalengkap'] = $nama;
				if($sql){
					createError("Profil berhasil diubah..",
								"user",
								"success",
								"yes");
					myRedirect("?go=profil");			
				} else {
					createError("Profil gagal diubah..",
								"user",
								"warning",
								"yes");
					myRedirect("?go=profil&type=edit");						
				}
			} else {				
				createError("NPM sudah ada..",
							"user",
							"warning",
							"yes");
				myRedirect("?go=profil&type=edit");
			}
		} else {				
			createError("NPM tidak ditemukan..",
						"user",
						"warning",
						"yes");
			myRedirect("?go=profil&type=edit");			
		}
	}
	/* Bagian daftarujian.php */
	if(@$_GET['go'] == 'daftarujian' && 
	   @$_GET['get'] == 'register' && 
   	   @$_GET['id'] !== null){
		$id = @$_GET['id'];
		$npm = @$_SESSION['npm'];
		$accept = 'konfirmasi';
		$cdq = "SELECT * FROM tb_mapel_reg 
				WHERE npm='$npm' && 
					  stat='$accept'";
		$cdsql = new myFetch($cdq);
		if($cdsql->row == 0){
			$caq = "SELECT * FROM tb_mapel 
					WHERE tersedia='ya'";
			$casql = new myFetch($caq);
			if($casql->row == 1){
				$q = "INSERT INTO tb_mapel_reg 
					  VALUES('$id',
					  		 '$npm',
					  	 	 '$accept')";
				$sql = new myFetch($q);
				createError("Ujian berhasil didaftarkan..",
							"user",
							"success",
							"yes");			
			} else {
				createError("Ujian tidak tersedia..",
							"user",
							"danger",
							"yes");
			}
			myRedirect("?go=daftarujian");
		}
	}
	/*-------------------------*/
	/* Bagian ujian.php */
	if(@$_GET['go'] == 'ujian' && 
	   @$_GET['get'] == 'cancel' && 
	   @$_GET['id'] !== null){
		$id = @$_GET['id'];
		$npm = @$_SESSION['npm'];
		$cdq = "SELECT * FROM tb_mapel_reg 
				WHERE npm='$npm'";
		$cdsql = new myFetch($cdq);
		if($cdsql->row == 1){
			$q = "DELETE FROM tb_mapel_reg 
				  WHERE npm='$npm'";
			$sql = new myFetch($q);
			createError("Ujian berhasil dibatalkan..",
						"user",
						"success",
						"yes");			
			myRedirect("?go=ujian");
		}
	}
	/*------------------*/
	function myRedirect($url){
		// header("Location: $url");
		echo "<script>window.location.replace('$url')</script>";
	}  
?>