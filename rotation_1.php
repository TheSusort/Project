<?php
// include_once 'imgEXIF.php';

include_once 'image.php';
// Rotation image with path $_POST['name'] on angle $_POST['angle']
	if(!empty($_POST['angle'])&!empty($_POST['name'])){
		$angle = $_POST['angle'];
		$fileName = substr($_POST['name'], 7);
		$d_s = DIRECTORY_SEPARATOR;
		$imgPath = 'Bilder' . $d_s . $fileName;
		$rot = RotateImage($imgPath, $angle);
	}
	
?>