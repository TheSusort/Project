<?php
	include_once('image.php');
	
	if(!empty($_POST['angle'])&!empty($_POST['name'])){
		$angle = $_POST['angle'];
		$fileName = substr($_POST['name'], 7);
		$d_s = DIRECTORY_SEPARATOR;
		RotateImage(__DIR__ . $d_s . 'Bilder' . $d_s . $fileName, '', $angle);
		RotateImage(__DIR__ . $d_s . 'Bilder'. $d_s .'thumbs' . $d_s . $fileName, '', $angle);
	}
?>