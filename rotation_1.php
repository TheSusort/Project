<?php
// include_once 'imgEXIF.php';

include_once 'image.php';
// Rotation image with path $_POST['name'] on angle $_POST['angle']
	if(isset($_POST['angle'])&isset($_POST['name'])){
		$angle = $_POST['angle'];
		$fileName = substr($_POST['name'], 7);
		$d_s = DIRECTORY_SEPARATOR;
		$imgPath = 'Bilder' . $d_s . $fileName;
		rotateImage($imgPath, $angle);
	}

// Delete image
	if (isset($_POST['delete'])&isset($_POST['name'])){
		$fileName = substr($_POST['name'], 7);
		$d_s = DIRECTORY_SEPARATOR;
		$imgPath = 'Bilder' . $d_s . $fileName;
		delImage($imgPath);
	}
?>