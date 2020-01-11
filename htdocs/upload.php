<div class="container mt-3">
	<form method="post" action="?go=upload" enctype="multipart/form-data">
  		<div align="right" class="form-group d-flex justify-content-between align-middle">
		  	<h5>Upload Gambar</h5>
			<button type="submit" name="submit" title="Upload gambar" type="button" class="btn btn-primary ml-2"><i class="fas fa-save"></i></button></a>
    	</div>
    	<nav aria-label="breadcrumb">
  		  <ol class="breadcrumb">
    	        <li class="breadcrumb-item"><a href="./"><i class="fas fa-home"></i></a></li>
  		    	<li class="breadcrumb-item active">Upload Gambar</li>
  		  </ol>
  		</nav>
		<div class="form-group">
			<label for="gambar">Pilih Gambar:</label>
			<input class="form-control-file" type="file" name="file[]" multiple>
		</div>
	</form>
</div>
<?php 
if(isset($_POST['submit'])){
	$up_success = 0;
	$up_fail = 0;
	$uploaddir = "img/";

	 // Count total files
	$namafile = $_FILES['file']['name'];
	$tmpfile = $_FILES['file']['tmp_name'];
	$totalfile = count($namafile);
	$lanjutUpload = true;
	
 	// Looping all files
 	for($i=0;$i<$totalfile;$i++){
	   // Cek Ukuran File
	   $ukuranMax = 1024; // Max 1MB
	   $ukuran = intval($_FILES['file']['size'][$i])/1024;
	   if($ukuran > $ukuranMax){
		   	$lanjutUpload = false;
			echo '<script>alert("Terdapat file gambar melebihi ukuran 1MB");</script>';
			echo '<script>window.location.replace("?go=upload");</script>';
	   }
	   // Cek Jenis File
	   $allowFileType = array(".jpg",".jpeg",".svg",".png");
	   $filetype = substr($namafile[$i],strrpos($namafile[$i],"."));
	   if(strlen(array_search($filetype,$allowFileType)) == 0){
			$lanjutUpload = false;
			echo '<script>alert("Terdapat file yang bukan gambar");</script>';
			echo '<script>window.location.replace("?go=upload");</script>';
	   }
	 }
	 for($i=0;$i<$totalfile;$i++){
		if($lanjutUpload){
			// Upload file
			$status_upload = move_uploaded_file($tmpfile[$i],$uploaddir.$namafile[$i]);
			if($status_upload){
				$up_success++;
	 		} else {
				$up_fail--;
			}
		}
	 }
	 echo '<script>alert("Upload Selesai dengan sukses: '.$up_success.' dan gagal: '.$up_fail.' ");</script>';
	 echo '<script>window.location.replace("./");</script>';
	 // window.location.replace("./");
} 
?>
