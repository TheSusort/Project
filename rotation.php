<?php
	include_once('image.php');
	
	if(!empty($_POST['angle'])&!empty($_POST['name'])){
		$angle = $_POST['angle'];
		$fileName = substr($_POST['name'], 7);
		$d_s = DIRECTORY_SEPARATOR;
		$imgURL = __DIR__ . $d_s . 'Bilder' . $d_s . $fileName;
		$imgTempURL = __DIR__ . $d_s . 'Bilder' . $d_s . 'temp' . $d_s . 'temp.jpg';
		$rot = RotateImage($imgURL, '', $angle, $imgTempURL);
		if($rot){
			RotateImage(__DIR__ . $d_s . 'Bilder'. $d_s .'thumbs' . $d_s . $fileName, '', $angle, '');
		}
	}
?>