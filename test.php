<?php
	require_once('PEL/src/PelJpeg.php');
	require_once('funksjoner.php');
	
	$url = 'Bilder/svard.jpg';
	
	$exif = get_Tag_exif($url);
	print_r($exif);
	
 ?>