<?php

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
	
	function autoRotateImage($url, $outUrl) { 

		$image = new Imagick();
		if(!$image->readImage($url)){  //__DIR__ . DIRECTORY_SEPARATOR .
			$image->clear(); 
			$image->destroy();
			return FALSE;
		}
		if (empty($outUrl)){
			$outUrl = $url;
		}
		$orientation = $image->getImageOrientation(); 

		$angle = 0;
		switch($orientation) { 
			case imagick::ORIENTATION_BOTTOMRIGHT: 
				$angle=180; // rotate 180 degrees 
			break; 

			case imagick::ORIENTATION_RIGHTTOP: 
				$angle=90; // rotate 90 degrees CW 
			break; 

			case imagick::ORIENTATION_LEFTBOTTOM: 
				$angle=-90; // rotate 90 degrees CCW 
			break; 
		} 
		if($image->rotateimage("#415050", $angle)){ // rotate 180 degrees 

			// Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image! 
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
?>