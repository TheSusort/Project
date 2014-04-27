<?php
	require_once('PEL/src/PelJpeg.php');
	
	$url = 'Bilder/svard.jpg';
	
	$jpeg = new PelJpeg($url);
	$exif = $jpeg->getExif();
	
	$size = getimagesize($url);
		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		$icfunc = "imagecreatefrom" . $format;
		if (!function_exists($icfunc)) return false;
		$source = $icfunc($url);
		$rotate = imagerotate($source, -90, '0x415050');
		$func = 'image'.$format;	//save image function name
		$func($rotate, $url, 100);	//save image to url
		imagedestroy($rotate);
	
	$NewJpeg = new PelJpeg($url);
	$NewJpeg->setExif($exif);
	$NewJpeg->saveFile($url);
	print_r($exif);
	
 ?>