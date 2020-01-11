<!DOCTYPE html>
<html>
	<head>
		<title>
			Gallery Browser
		</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">	
		<?php 
			include_once 'php/load-css.php';
		?>
	</head>
	<body>
		<div id="hidden">
			<div id="body">
			<?php
				@session_start();
				include_once 'header.php';
				$home = @$_GET['go'];
				if(!isset($home)){
					$home = "content";
				}
				if(!@include_once $home.'.php'){
					include_once "errorpage/404.php";
				}
				include_once 'php/load-js.php';
				include_once 'php/load-system.php';
			?>
			</div>
		</div>
	</body>
</html>