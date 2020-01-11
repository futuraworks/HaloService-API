<?php
	$anime_folder = "E:/Video/Anime";   
	$icon_folder = "E:/Download/Icon Folder";
	$scan = scandir($anime_folder);
	$id = @$_GET['id'];
	$judul = $scan[$id];
	$detail_folder = $anime_folder.'/'.$judul;
	$scan = scandir($detail_folder);
	$file_video = array();
	$file_sinopsis = array();
	/* Cari Sinopsis */
	foreach ($scan as $key => $val) {
		$val = strtolower($val);
		$txt = strpos($val, ".txt");
		if(strlen($txt) > 0){
			array_push($file_sinopsis, $val);
		}
	}
	/* Cari Video */
	foreach ($scan as $key => $val) {
		$val = strtolower($val);
		$mp4 = strpos($val, ".mp4");
		$mkv = strpos($val, ".mkv");
		if(strlen($mp4) > 0 OR strlen($mkv) > 0){
			array_push($file_video, $val);
		}
	}
?>
<div class="container mt-3">
  	<nav aria-label="breadcrumb">
  	  <ol class="breadcrumb">
  	    <li class="breadcrumb-item"><a href="./">Home</a></li>
  	    <li class="breadcrumb-item active" aria-current="page"><?php echo $judul; ?></li>
  	  </ol>
  	</nav>
	<div class="alert <?php echo "alert-".$_SESSION['errorcolor']; ?> fade show <?php if(@$_SESSION['lookerror'] !== 'yes'){ ?> invisible <?php } ?>" role="alert">
	  <?php echo $_SESSION['errorlog']; ?>
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    <span aria-hidden="true">&times;</span>
	  </button>
	</div>
	<div>
		<h2><?php echo $judul; ?></h2>
		<div> 
			<?php
				foreach ($file_sinopsis as $key => $val) {
					$fopen = fopen($detail_folder.'/'.$val, "r");
					echo fread($fopen, filesize($detail_folder.'/'.$val)); 
					fclose($fopen);
				} 
			?>
		</div>
		<div>
			<div class="card-columns">
				<?php foreach ($file_video as $key => $val) { ?>
				<div class="card">
				  <div class="card-body">
				  	<a href="file:///<?php echo $detail_folder.'/'.$val; ?>"><?php echo $val; ?></a>
				  </div>
				</div>
				<?php } ?>
			</div> 
		</div>
	</div>
</div>