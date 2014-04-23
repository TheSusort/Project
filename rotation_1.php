<?php
// Rotation image with path $_POST['name'] on angle $_POST['angle']
	if(!empty($_POST['angle'])&!empty($_POST['name'])){
		$angle = $_POST['angle'];
		$fileName = substr($_POST['name'], 7);
		$d_s = DIRECTORY_SEPARATOR;
		$imgURL = __DIR__ . $d_s . 'Bilder' . $d_s . $fileName;
		// $imgTempURL = __DIR__ . $d_s . 'Bilder' . $d_s . 'temp' . $d_s . 'temp.jpg';
		// $rot = RotateImage($imgURL, '', $angle, $imgTempURL);
		$rot = RotateImage($imgURL, '', $angle);
		if($rot){
			RotateImage(__DIR__ . $d_s . 'Bilder'. $d_s .'thumbs' . $d_s . $fileName, '', $angle, '');
		}
	}
	
	function rotateImage($url, $outUrl, $angle){
		$image = new Imagick();
		$image->readImage($url);  //__DIR__ . DIRECTORY_SEPARATOR .
		$angle = (int)$angle; 
		if (empty($outUrl)){
			$outUrl = $url;
		}
		if($image->rotateimage("#415050", $angle)){ // rotate 180 degrees 

			// Now that it's manuel-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image! 
			$image->setImageOrientation(imagick::ORIENTATION_TOPLEFT); 
			$image->writeImage($outUrl); //__DIR__ . DIRECTORY_SEPARATOR .
			
			$image->clear(); 
			$image->destroy();
			return TRUE;
		}else{
			$image->clear(); 
			$image->destroy();
			return FALSE;
		}
	}
	
	function autoRotateImage($url) { 
		$angle = getImageOrientation($url);
		$size = getimagesize($url);
		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		$icfunc = "imagecreatefrom" . $format;
		if (!function_exists($icfunc)) return false;
		$source = $icfunc($url);
		$rotate = imagerotate($source, $angle, '0x415050');
		$func = 'image'.$format;
		$func($rotate, $url, 100);
		imagedestroy($rotate);
		return $url;
	}
	
	function getImageOrientation($path){
		$exif = read_exif_data_quick($path);
		$angle = 0;
		if (isset($exif['Orientation'])){
			$orientation = $exif['Orientation'];
			switch($orientation) { 
				case 3: 
					$angle=180; // rotate 180 degrees 
				break; 

				case 6: 
					$angle=90; // rotate 90 degrees CW 
				break; 

				case 8: 
					$angle=-90; // rotate 90 degrees CCW 
				break; 
			} 
		}
		print_r($angle);
	}
	
	function read_exif_data_quick($path) {
		$tmpfile = "Bilder/tmp/read_exif_data_quick.tmp_file";
		$in = fopen($path, "r");
		$out = fopen($tmpfile,"w");
		fwrite( $out, fread( $in, 150000 ) );
		fclose($in);
		fclose($out);
		return read_exif_data($tmpfile);
   }
   
   $url = 'Bilder/svard.jpg';
   $size = getimagesize($url);
   $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
   echo ($format);
   echo autoRotateImage($url);
?>