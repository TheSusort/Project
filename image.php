<?php
include_once("mysql.php");
require_once('PEL/src/PelJpeg.php');
require_once('PEL/src/PelJpeg.php');

	function rotateImage($url, $angle){
		$size = getimagesize($url);
		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		switch ($format){
			case 'jpeg':
				$quality = 100;
			break;
			case 'png':
				$quality = 0;
			break;
			case 'gif':
				$quality = 100;
			break;
			default:
				$quality = 100;
			break;
		}
		if($format == 'jpeg') 
			$data = read_exif($url);
	
		$icfunc = "imagecreatefrom" . $format;
		if (!function_exists($icfunc)) return false;
		$source = $icfunc($url);
		$rotate = imagerotate($source, $angle, 0);
		$func = 'image'.$format;	//save image function name
		$func($rotate, $url, $quality);	//save image to url
		// imagedestroy($rotate);
		
		if ($format == 'jpeg') write_exif_data($url, $data);
		
		$oldWidth = $size[1];
		$oldHeight = $size[0];
		$newHeight = 150; //thumb
        $newWidth = floor($oldWidth * ($newHeight / $oldHeight));
		
		$thumb = imagecreatetruecolor($newWidth, $newHeight);
		//	Resize
		imagecopyresized($thumb, $rotate, 0, 0, 0, 0, $newWidth, $newHeight, $oldWidth, $oldHeight);
		// $rotate = imagerotate($thumb, $angle, '0x415050');
		$thumbPath='';
		$urlArr = split('[/\\]', $url);
		$length = count($urlArr);
		for($i=0; $i<($length-1); $i++){
			$thumbPath .= $urlArr[$i].'/';
		}
		$thumbPath .= 'thumbs/'.$urlArr[$length-1];
		
		$func = 'image'.$format;	//save image function name
		$func($thumb, $thumbPath, $quality);	//save image to url
		imagedestroy($thumb);
		imagedestroy($rotate);
		imagedestroy($source);
	}
	
	/* Rotate image automatic.  
	*	return FALSE if no ORIENTATION in the image
	*		or if ORIENTATION is 0
	*	return TRUE if image was rotated.
	*/
	function autoRotateImage($url) { 
		$angle = getImageOrientation($url);
		// echo($url.'----'.$angle);
		if ($angle != 0){
			rotateImage($url, $angle);
			setMetaTag_PEL($url, PelTag::ORIENTATION , '0');
			return true;
		}
		return false;
	}
	
	function createThumbs($inn, $out, $maxH=150, $content=FALSE){
		$size = getimagesize($inn);
		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		switch ($format){
			case 'jpeg':
				$quality = 100;
			break;
			case 'png':
				$quality = 0;
			break;
			case 'gif':
				$quality = 100;
			break;
			default:
				$quality = 100;
			break;
		}
		$oldWidth = $size[0];
		$oldHeight = $size[1];
		$newWidth = $maxH;
        $newHeight = floor($oldHeight * ($newWidth / $oldWidth));
		if(!$content){
			$icfunc = "imagecreatefrom" . $format;
			if (!function_exists($icfunc)) return false;
			$content = $icfunc($inn);
		}
		$thumb = imagecreatetruecolor($newWidth, $newHeight);
		imagecopyresized($thumb, $content, 0, 0, 0, 0, $newWidth, $newHeight, $oldWidth, $oldHeight);
		$func = 'image'.$format;	//save image function name
		$func($thumb, $out, 100);	//save image to path
		imagedestroy($thumb);
		imagedestroy($content);
	}
	
	function delImage($path){
		if (@fopen($path, "r")) {
			unlink($path);
		} 
		$arr = split('[/\]',$path);
		$length = count($arr);
		$name = $arr[($length-1)];
		$thumbPath = '';
		for ($i=0; $i<$length-1; $i++){
			$thumbPath .= $arr[$i].'/';
		}
		$thumbPath .= 'thumbs/'.$name;
		if (@fopen($thumbPath, "r")) {
			unlink($thumbPath);
		}
		db_delete('file_liste', 'filename', $name);
	}
	
	/* Return image orientation.  
	*	return angle in degrees
	*/
	function getImageOrientation($path){
		$size = getimagesize($path);
		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		if($format == 'jpeg'){
			$exif = read_exif_data_quick($path);
			// print_r($exif);
			$angle = 0;
			if (isset($exif['Orientation'])){
				$orientation = $exif['Orientation'];
				switch($orientation) { 
					case 3: 
						$angle=180; // rotate 180 degrees 
					break; 

					case 6: 
						$angle=270; // rotate 90 degrees CW 
					break; 

					case 8: 
						$angle=90; // rotate 90 degrees CCW 
					break; 
				} 
			}
			return($angle);
		}else{
			return(0);
		}
	}
	
	/* Create a temp file .  
	*	return path to temp file
	*/
	function getImgPiece($path){
		$tmpfile = "Bilder/temp/read_exif_data_quick.tmp_file";
		$in = fopen($path, "r");
		$out = fopen($tmpfile,"w");
		fwrite( $out, fread( $in, 100000 ) );
		fclose($in);
		fclose($out);
		return $tmpfile;
	}
	
	function read_exif_data_quick($path) {
		$tmpfile = getImgPiece($path);
		return read_exif_data($tmpfile);
	}
	
	function read_exif($path) {
		$resalt = array('data'=>'', 'type'=>'');
		
		ini_set('memory_limit', '124M');

		$data = new PelDataWindow(file_get_contents($path));

		if (PelJpeg::isValid($data)) {
			$resalt['type']='PelJpeg';
			$jpeg = $file = new PelJpeg();

			$jpeg->load($data);

			$exif = $jpeg->getExif();

			if ($exif == null) {
				$exif = new PelExif();
				$jpeg->setExif($exif);
				
				$tiff = new PelTiff();
				$exif->setTiff($tiff);
			} else {
				$tiff = $exif->getTiff();
			}
			$resalt['data']=$exif;
		} elseif (PelTiff::isValid($data)) {
			$resalt['type']='PelTiff';
			
			$tiff = $file = new PelTiff();
			$tiff->load($data);
			
			$resalt['data']=$tiff;
		} else {
			PelConvert::bytesToDump($data->getBytes(0, 16));
			exit(1);
		}
		return $resalt;
		// $jpeg = new PelJpeg($path);
		// $exif = &$jpeg->getExif();
		// return $exif;
	}
	
	function write_exif_data($path, $data) {
		switch($data['type']) { 
			case 'PelJpeg': 
				$NewJpeg = new PelJpeg($path);
				$NewJpeg->setExif($data['data']);
				$NewJpeg->saveFile($path);
			break; 
			case 'PelTiff': 
				$tiff = new PelTiff();
				$tiff->setTiff($tiff);
				$tiff->saveFile($path);
			break;
		}
		// $NewJpeg = new PelJpeg($path);
		// $NewJpeg->setExif($exif);
		// $NewJpeg->saveFile($path);
	}

	function getXMP(){
	
	}
	
	function setMetaTag_PEL($input, $tag, $value){
		/* We typically need lots of RAM to parse TIFF images since they tend
		 * to be big and uncompressed. */
		ini_set('memory_limit', '124M');

		/* The input file is now read into a PelDataWindow object.  At this
		 * point we do not know if the file stores JPEG or TIFF data, so
		 * instead of using one of the loadFile methods on PelJpeg or PelTiff
		 * we store the data in a PelDataWindow. */
		$data = new PelDataWindow(file_get_contents($input));

		/* The static isValid methods in PelJpeg and PelTiff will tell us in
		 * an efficient maner which kind of data we are dealing with. */
		if (PelJpeg::isValid($data)) {
			/* The data was recognized as JPEG data, so we create a new empty
			* PelJpeg object which will hold it.  When we want to save the
			* image again, we need to know which object to same (using the
			* getBytes method), so we store $jpeg as $file too. */
			$jpeg = $file = new PelJpeg();

			/* We then load the data from the PelDataWindow into our PelJpeg
			* object.  No copying of data will be done, the PelJpeg object will
			* simply remember that it is to ask the PelDataWindow for data when
			* required. */
			$jpeg->load($data);

			/* The PelJpeg object contains a number of sections, one of which
			* might be our Exif data. The getExif() method is a convenient way
			* of getting the right section with a minimum of fuzz. */
			$exif = $jpeg->getExif();

			if ($exif == null) {
				/* Ups, there is no APP1 section in the JPEG file.  This is where
				 * the Exif data should be. */

				/* In this case we simply create a new APP1 section (a PelExif
				 * object) and adds it to the PelJpeg object. */
				$exif = new PelExif();
				$jpeg->setExif($exif);

				/* We then create an empty TIFF structure in the APP1 section. */
				$tiff = new PelTiff();
				$exif->setTiff($tiff);
			} else {
			/* Surprice, surprice: Exif data is really just TIFF data!  So we
			 * extract the PelTiff object for later use. */
				$tiff = $exif->getTiff();
			}
		} elseif (PelTiff::isValid($data)) {
			/* The data was recognized as TIFF data.  We prepare a PelTiff
			* object to hold it, and record in $file that the PelTiff object is
			* the top-most object (the one on which we will call getBytes). */
			$tiff = $file = new PelTiff();
			/* Now load the data. */
			$tiff->load($data);
		} else {
			/* The data was not recognized as either JPEG or TIFF data.
			* Complain loudly, dump the first 16 bytes, and exit. */
			PelConvert::bytesToDump($data->getBytes(0, 16));
			exit(1);
		}

		/* TIFF data has a tree structure much like a file system.  There is a
		 * root IFD (Image File Directory) which contains a number of entries
		 * and maybe a link to the next IFD.  The IFDs are chained together
		 * like this, but some of them can also contain what is known as
		 * sub-IFDs.  For our purpose we only need the first IFD, for this is
		 * where the image description should be stored. */
		$ifd0 = $tiff->getIfd();

		if ($ifd0 == null) {
		  /* No IFD in the TIFF data?  This probably means that the image
		   * didn't have any Exif information to start with, and so an empty
		   * PelTiff object was inserted by the code above.  But this is no
		   * problem, we just create and inserts an empty PelIfd object. */
		  $ifd0 = new PelIfd(PelIfd::IFD0);
		  $tiff->setIfd($ifd0);
		}

		/* Each entry in an IFD is identified with a tag.  This will load the
		 * ImageDescription entry if it is present.  If the IFD does not
		 * contain such an entry, null will be returned. */
		// $desc = $ifd0->getEntry(PelTag::IMAGE_DESCRIPTION);//----------------------------------
		$desc = $ifd0->getEntry($tag);

		/* We need to check if the image already had a description stored. */
		if ($desc == null) {
			/* The was no description in the image. */

			/* In this case we simply create a new PelEntryAscii object to hold
			* the description.  The constructor for PelEntryAscii needs to know
			* the tag and contents of the new entry. */
			$desc = new PelEntryAscii($tag, $value);	//-------------------------------------

			/* This will insert the newly created entry with the description
			* into the IFD. */
			$ifd0->addEntry($desc);
		} else {
			/* An old description was found in the image. */

			/* The description is simply updated with the new description. */
			$desc->setValue($value);
		}

		/* At this point the image on disk has not been changed, it is only
		 * the object structure in memory which represent the image which has
		 * been altered.  This structure can be converted into a string of
		 * bytes with the getBytes method, and saving this in the output file
		 * completes the script. */
		$file->saveFile($input);
	}
	
?>