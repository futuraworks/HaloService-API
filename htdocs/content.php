<?php
	$user_dir = $_GET['dir'];
	$root_dir = "img/".$user_dir;
	$root_folder = scandir($root_dir);
	unset($root_folder[0]);
	unset($root_folder[1]);
	if(!is_dir($root_dir)){
		header('Location:./');
	}
	$folder_list = array();
	$folder_breadcrumb = array();
	$folder_link = array();
	$start = 0;
	$end = 0;
	for ($i=0; $i < strlen($user_dir); $i++) { 
		// echo substr($user_dir,$i,1)." ";
		if(substr($user_dir,$i,1) == '/'){
			$end = $i;
			if(count($folder_breadcrumb) > 0){
				$start = $start+1;
			}
			array_push($folder_breadcrumb, substr($user_dir, $start, $end-$start));
			array_push($folder_link, substr($user_dir, 0,$end));
			$start = $end;
		}
	}
	if($i == strlen($user_dir)){
		if($start < strlen($user_dir)){
			if(count($folder_breadcrumb) > 0){				
				array_push($folder_breadcrumb, substr($user_dir, $start+1));
			} else {				
				array_push($folder_breadcrumb, substr($user_dir, $start));
			}
		}
	}
	if(isset($_GET['search'])){
		/* Filter Search */
		$searchQ = $_GET['search'];
		$search_result = array();
		foreach ($root_folder as $key => $val) {
			$searchFound = strlen(strpos(strtolower($val), strtolower($searchQ)));
			if($searchFound > 0){
				array_push($search_result, $val);
			}
		}
		$root_folder = $search_result;
	}
	/* Filter Folder */
	foreach ($root_folder as $key => $val) {
		if(strlen($val) > 2){
			if(is_dir($root_dir.'/'.$val)){
				array_push($folder_list, $val);
			}
		}
	}
?>
<pre>
	<?php
		// echo var_dump($folder_list);
	?>
</pre>
<div class="container mt-3">
  	<form method="get" action="#">
  		<div align="right">
  			<div class="form-group d-flex justify-content-end">
			  	<div>
  					<input type="hidden" name="dir" value="<?php echo $_GET['dir']; ?>">  				
  					<input class="form-control" type="input" name="search" placeholder="Cari..." value="<?php echo $_GET['search']; ?>">
				</div>
			  	<div>
  					<button type="submit" title="Cari" class="btn btn-secondary ml-2"><i class="fas fa-search"></i></button>
					<a href="?go=upload"><button title="Upload gambar" type="button" class="btn btn-primary ml-2"><i class="fas fa-upload"></i></button></a>
				</div>
			</div>  
  		</div>			
  	</form>
  	<nav aria-label="breadcrumb">
  	  <ol class="breadcrumb">
  	  	<?php if(count($folder_breadcrumb) == 0 AND !isset($_GET['search'])){ ?>
  	    	<li class="breadcrumb-item active" aria-current="page"><i class="fas fa-home"></i></li>
  		<?php } else { ?>
  	    	<li class="breadcrumb-item"><a href="./"><i class="fas fa-home"></i></a></li>
  	    	<?php foreach ($folder_breadcrumb as $key => $val){ ?>
  	    		<?php if(count($folder_breadcrumb)-1 == $key){ ?>
  	    			<li class="breadcrumb-item active" aria-current="page"><?php echo $val; ?></li>
  	    		<?php } else { ?>
  	    			<li class="breadcrumb-item"><a href="?dir=<?php echo $folder_link[$key]; ?>"><?php echo $val; ?></a></li>
  	    		<?php } ?>
  			<?php } ?>
  		<?php } ?>
  	  </ol>
  	</nav>
  	<?php
  		$page = $_GET['page'];
  		if(!isset($page)){
  			$page = 1;
  		} 
  		$total_file = count($root_folder)-2;
  		$total_item = 30;
  		$total_page = ceil($total_file/$total_item);
  		$start_item = 0+(($page-1)*$total_item);
  		$root_folder = array_slice($root_folder, $start_item,$total_item);
  	?>
	<?php if(isset($_GET['search'])){?>
  		<nav aria-label="breadcrumb">
  			<ol class="breadcrumb">
			<?php 
  				if(isset($_GET['search'])){
					  echo "Pencarian ''".$_GET['search']."'' Total : ".count($search_result);
					} 
					?>
			</ol>
		</nav>
	<?php }?>
	<!-- Page Navigation -->
	<?php if($total_page > 1){?>	
  		<nav aria-label="breadcrumb">
  			<ol class="breadcrumb">
  				<?php for ($i=1; $i <= $total_page; $i++) { ?>
  				<a href="?dir=<?php echo $_GET['dir']; ?>&page=<?php echo $i; ?>"><button <?php if($i == $page){echo "disabled";} ?> class="btn btn-primary rounded-circle mr-1 ml-1 mb-1" style="width: 40px; height: 40px;"><?php echo $i; ?></button></a>
  				<?php } ?>
  			</ol>
  		</nav>
	<?php }?>
	<!--  -->
	<!-- Content -->
	<div>
		<?php if(count($root_folder) == 0 && count($folder_list) == 0){ ?>
			<div class="card">
				<div class="card-body">
					<i class="fas fa-folder"></i> Folder kosong.
				</div>
			</div>
		<?php } ?>
		<!-- Menampilkan Jumlah Folder -->
		<div class="row">
		<?php foreach ($folder_list as $k => $val) { ?>
			<div class="col-sm-4 mb-3">				
				<div class="card">
				  	<div class="card-body">
				  		<?php 
				  			if(empty($user_dir)){
				  				$link_folder = $val;
				  			} else {
				  				$link_folder = $user_dir.'/'.$val;
				  			}
				  		?>
				  		<i class="fas fa-folder"></i>
				    	<a href="?dir=<?php echo $link_folder; ?>">
				    		<?php echo $val; ?>
				    	</a>
				  	</div>
				</div>
			</div>
		<?php } ?>
		</div>
		<!-- Menampilkan Jumlah File -->
		<div class="row">
		<?php  foreach ($root_folder as $k => $val) {if(strlen($val) > 2){ ?>
		<?php if(is_dir($root_dir.'/'.$val)){ ?>
		<?php } else { ?>
			<div class="col-sm-4 mb-3">				
				<div class="card">
					<?php
						$imglist = array(".jpeg",".jpg",".png");	
						$filetype = substr($val,strrpos($val,"."));
					?>
					<div class="card-body"><?php echo $val; ?></div>
					<?php
						if(strlen(array_search($filetype,$imglist)) >= 1){  
					?>
					<img src="<?php echo $root_dir.'/'.$val; ?>" title="Perbesar Gambar" class="card-img-top" alt="<?php echo $val; ?>">
					<?php }?>
				</div>
			</div>
		<?php } ?>
		<?php  }} ?>
		</div>
	</div>
	<!--  -->	
	<!-- Page Navigation -->
	<?php if($total_page > 1){?>	
  		<nav aria-label="breadcrumb">
  			<ol class="breadcrumb">
  				<?php for ($i=1; $i <= $total_page; $i++) { ?>
  				<a href="?dir=<?php echo $_GET['dir']; ?>&page=<?php echo $i; ?>"><button <?php if($i == $page){echo "disabled";} ?> class="btn btn-primary rounded-circle mr-1 ml-1 mb-1" style="width: 40px; height: 40px;"><?php echo $i; ?></button></a>
  				<?php } ?>
  			</ol>
  		</nav>
	<?php }?>
	<!--  -->
</div>
<div class="modal fade bd-example-modal-lg" id="ModalImg" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ModalTitle" style="word-wrap: anywhere;">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img id="modal_img" src="#" width="100%">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
      </div>
    </div>
  </div>
</div>
