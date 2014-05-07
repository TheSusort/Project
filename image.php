<?php
include_once("mysql.php");
require_once('PEL/src/PelJpeg.php');
require_once('PEL/src/PelJpeg.php');
$Toolkit_Dir = "./PHP_JPEG_Metadata_Toolkit_1.12/";
include_once $Toolkit_Dir . 'Toolkit_Version.php';          // Change: added as of version 1.11
include_once $Toolkit_Dir . 'JPEG.php';                     // Change: Allow this example file to be easily relocatable - as of version 1.11
include_once $Toolkit_Dir . 'JFIF.php';
include_once $Toolkit_Dir . 'PictureInfo.php';
include_once $Toolkit_Dir . 'XMP.php';
include_once $Toolkit_Dir . 'Photoshop_IRB.php';
include_once $Toolkit_Dir . 'EXIF.php';

	function get_Rating_exif($url){
		$header_data = get_jpeg_header_data( $url );
		$xmpText = get_XMP_text( $header_data );
		$xmpArr = read_XMP_array_from_text( $xmpText );
		$r = search_tag($xmpArr, 'xmp:Rating');
		if (isset($r['value'])){
			return $r['value'];
		}else{
			return false;
		}
	}
	
	function get_Rating_DB($url){
		$fileName = substr($url, 7);
		$img = db_select('file_liste', 'rating', 'WHERE filename = \''.$fileName.'\' ', 'rating');
		return $img[0];
	}
	
	function set_Rating_exif($path, $value){
		$header_data = get_jpeg_header_data( $path );
		$xmpText = get_XMP_text( $header_data );
		$xmpArr = read_XMP_array_from_text( $xmpText );
			$xmpArr = checkXMP($xmpArr);
			
		$i=0;
		$value_prsnt = strval(((int)$value - 1) * 24 + 1);
		
		if (!set_XMP_tag($xmpArr, 'xmp:Rating', $value)){
			$i = count($xmpArr[0]['children'][0]['children']);
			
			$xmpArr[0]['children'][0]['children'][$i]['tag'] = 'rdf:Description';
			$xmpArr[0]['children'][0]['children'][$i]['attributes']['xmlns:xmp'] = 'http://ns.adobe.com/xap/1.0/';
			$xmpArr[0]['children'][0]['children'][$i]['children'][0]['tag'] = 'xmp:Rating';
			$xmpArr[0]['children'][0]['children'][$i]['children'][0]['value'] = $value;
		}
		
		if (!set_XMP_tag($xmpArr, 'MicrosoftPhoto:Rating', $value_prsnt)){
			if (isset($xmpArr[0]['children'][0]['children'][1]['attributes']['xmlns:MicrosoftPhoto'])){
				$xmpArr[0]['children'][0]['children'][1]['children'][0]['tag'] = 'MicrosoftPhoto:Rating';
				$xmpArr[0]['children'][0]['children'][1]['children'][0]['value'] = $value_prsnt;
			}else{
				$i = count($xmpArr[0]['children'][0]['children']);
				
				$xmpArr[0]['children'][0]['children'][$i]['tag'] = 'rdf:Description';
				$xmpArr[0]['children'][0]['children'][$i]['attributes']['rdf:about'] = 'uuid:faf5bdd5-ba3d-11da-ad31-d33d75182f1b';
				$xmpArr[0]['children'][0]['children'][$i]['attributes']['xmlns:MicrosoftPhoto'] = 'http://ns.microsoft.com/photo/1.0/';
				$xmpArr[0]['children'][0]['children'][$i]['children'][0]['tag'] = 'xmp:Rating';
				$xmpArr[0]['children'][0]['children'][$i]['children'][0]['value'] = $value;
			}
		}
		$newXMP = write_XMP_array_to_text( $xmpArr );
		$header_data = put_XMP_text( $header_data, $newXMP );
		put_jpeg_header_data( $path, $path, $header_data );
	}

	function set_Rating_DB($path, $value){
		global $db;
		$fileName = substr($path, 7);
		$query = "UPDATE file_liste SET rating='$value' WHERE filename='$fileName'";
		$result = $db->query($query);
		return $result;
	}
	
	function get_Comment_exif($path){
		$size = getimagesize($path);
		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		if($format == 'jpeg'){
			$exif = read_exif_data_quick($path);
			if (isset($exif['Comments'])){
				return $exif['Comments'];
			}
		}
		return FALSE;
	}
	
	function get_Comment_exif_PEL($path){
		$size = getimagesize($path);
		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		if($format == 'jpeg'){
			$comment = getMetaTag_PEL($path, PelTag::XP_COMMENT);
			if (isset($comment)){
				return $comment;
			}
		}
		return FALSE;
	}
	
	function get_Comment_DB($url){
		$fileName = substr($url, 7);
		$img = db_select('file_liste', 'commentary', 'WHERE filename = \''.$fileName.'\' ', 'commentary');
		return $img[0];
	}
	
	function set_Comment_exif($path, $value){
		setMetaTag_PEL($path, PelTag::XP_COMMENT  , $value);
	}
	
	function set_Comment_DB($path, $value){
		$fileName = getFileName($path);
		$query = "UPDATE file_liste SET commentary = '$value' WHERE filename = '$fileName'";
		return db_do_query($query);
	}
	
	function get_Tag_exif($path){
		$size = getimagesize($path);
		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		if($format == 'jpeg'){
			$tags_XMP = get_KeyWord($path);
				if(!empty($tags_XMP)){
					$tags_XMP = implode(';', $tags_XMP);
				}
			return $tags_XMP;
		}
		return FALSE;
	}
	
	function get_Tags_DB($url){
		$fileName = substr($url, 7);
		$img = db_select('tag', 'tags', 'INNER JOIN file_liste ON tag.fileid = file_liste.fileid WHERE file_liste.filename=\''.$fileName.'\' ORDER BY tags', 'tags');
		if (empty($img)){
			return array("");
		}
		return $img;
	}
	
	function add_Tag_exif($path, $newTag){
		$oldTags = get_Tag_exif($path);

			$tags =  $oldTags.";".$newTag;
		
		add_KeyWord($newTag, $path);
	}
	
	function set_Tag_exif($path, $tags){
		$tags = explode(';',$tags);
		foreach($tags as $tag){
				if(!empty($tag)){
					add_KeyWord($tag, $path);
				}
		}
	}
	
	function del_Tags_exif($path, $delTag){
		$oldTags = explode(';', get_Tag_exif($path));
		if (!is_array($oldTags)){
			$oldTags = array($oldTags);
		}
		$key = array_search($delTag, $oldTags);
		if ($key !== null){
			unset($oldTags[$key]);
		}
		if(!empty($oldTags)){
			$tags = implode(";",$oldTags);
			setMetaTag_PEL($path, PelTag::XP_KEYWORDS, $tags);
			del_KeyWord($delTag, $path);
		}else{
			del_KeyWord($delTag, $path);
			setMetaTag_PEL($path, PelTag::XP_KEYWORDS, ' ');
		}
	}
	
	function get_date_of_shooting_exif_PEL($path){
		$size = getimagesize($path);
		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		if($format == 'jpeg'){
			$exif = read_exif_data_quick($path);
			if (isset($exif['DateTimeOriginal'])){
				return $exif['DateTimeOriginal'];
			}
		}
		return FALSE;
	}
	
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
		//get metadata---------------------
		if($format == 'jpeg') 
			$data = read_exif($url);
		$tags = get_Tag_exif($url);
		$rate = get_Rating_exif($url);
		$icfunc = "imagecreatefrom" . $format;
		if (!function_exists($icfunc)) return false;
		$source = $icfunc($url);
		ini_set('memory_limit', '-1');
		$rotate = imagerotate($source, $angle, 0);
		$func = 'image'.$format;	//save image function name
		$func($rotate, $url, $quality);	//save image to url
		//set metadata---------------------
		if ($format == 'jpeg') 
			write_exif_data($url, $data);
		set_Tag_exif($url, $tags);
		set_Rating_exif($url, $rate);
		//---------------------------------
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
		if ($angle != 0){
			rotateImage($url, $angle);
			setMetaTag_PEL($url, PelTag::ORIENTATION , '1');
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
		ini_set('memory_limit', '-1');
		$oldWidth = $size[0];
		$oldHeight = $size[1];
		$newHeight = $maxH;
        $newWidth = floor($oldWidth * ($newHeight / $oldHeight));
		if(!$content){
			$icfunc = "imagecreatefrom" . $format;
			if (!function_exists($icfunc)) return false;
			$content = $icfunc($inn);
		}
		$thumb = imagecreatetruecolor($newWidth, $newHeight);
		imagecopyresized($thumb, $content, 0, 0, 0, 0, $newWidth, $newHeight, $oldWidth, $oldHeight);
		$func = 'image'.$format;	//save image function name
		$func($thumb, $out, $quality);	//save image to path
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
		
		ini_set('memory_limit', '-1');

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
	}
	
	// get EXIF data
	function get_EXIF($file)
	{
		$data = array(	
				'rating' =>'',
				'commentary' =>'',
				'tag' =>'',
				'date_of_addition' =>'');
				
		$data['rating'] = get_Rating_exif($file);
		
		$data['commentary'] = get_Comment_exif_PEL($file);
		
		if($tag = get_Tag_exif($file)){
			$data['tag'] = explode(';', $tag);
		}
		
		if (file_exists($file)) {
			$data['date_of_addition'] = date("Y-m-d H:i:s", filectime($file));
		}else{$data['date_of_addition'] = date('Y-m-d H:i:s');}
		
		$data['date_of_shooting'] = get_date_of_shooting_exif_PEL($file);
		
		return $data;
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
	}
	
	function setMetaTag_PEL($input, $tag, $value){
		/* We typically need lots of RAM to parse TIFF images since they tend
		 * to be big and uncompressed. */
		ini_set('memory_limit', '-1');

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
		$desc = $ifd0->getEntry($tag);

		/* We need to check if the image already had a description stored. */
		if ($desc == null) {
			/* The was no description in the image. */

			/* In this case we simply create a new PelEntryAscii object to hold
			* the description.  The constructor for PelEntryAscii needs to know
			* the tag and contents of the new entry. */
			
			if ($tag == PelTag::XP_COMMENT){
				$desc = new PelEntryWindowsString($tag, $value);
			}else{
				$desc = new PelEntryAscii($tag, $value);
			}
		
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
		// file_put_contents('test.jpg', $file->getBytes());
	}
	
	function search_tag(&$arr, $tag){
		for ($i=0; $i<count($arr); $i++){
			if(isset($arr[$i]['tag'])){
				if ($arr[$i]['tag'] == $tag){
					return $arr[$i];
				}else{
					if (isset($arr[$i]['children'])){
						$resalt = &search_tag($arr[$i]['children'], $tag);
						if ($resalt){
							return $resalt;
						}
					}
				}
			}
		}
	}
	
	function getMetaTag_PEL($input, $tag){
		ini_set('memory_limit', '-1');

		$data = new PelDataWindow(file_get_contents($input));
		if (PelJpeg::isValid($data)) {
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
		} elseif (PelTiff::isValid($data)) {
			$tiff = $file = new PelTiff();
			$tiff->load($data);
		} else {
			PelConvert::bytesToDump($data->getBytes(0, 16));
			exit(1);
		}
		$ifd0 = $tiff->getIfd();

		if ($ifd0 == null) {
		  $ifd0 = new PelIfd(PelIfd::IFD0);
		  $tiff->setIfd($ifd0);
		}
		
		$desc = $ifd0->getEntry($tag);

		if ($desc == null) {
			$desc = new PelEntryAscii($tag, '');

			$ifd0->addEntry($desc);
			$file->saveFile($input);
		} else {
			return $desc->getValue();
		}
		return False;
	}
	
	function set_XMP_tag(&$arr, $tag, $value){
		for ($i=0; $i<count($arr); $i++){
			if(isset($arr[$i]['tag'])){
				if ($arr[$i]['tag'] == $tag){
					$arr[$i]['value']=$value;
					return $arr[$i];
				}else{
					if (isset($arr[$i]['children'])){
						$resalt = set_XMP_tag($arr[$i]['children'], $tag, $value);
						if ($resalt){
							return $resalt;
						}
					}
				}
			}
		}
	}

	function checkXMP($xmp){
		if(empty($xmp) | !isset($xmp[0]['children'][0]['children'])){
			$xmp = array();
			$xmp[0]=array(	'tag'=>'x:xmpmeta', 
							'attributes'=>array('xmlns:x'=>'adobe:ns:meta/'), 
							'children'=>array());
							
			$xmp[0]['children'][0] = array('tag'=>'rdf:RDF', 
										'attributes'=>array('xmlns:rdf'=>'http://www.w3.org/1999/02/22-rdf-syntax-ns#'), 
										'children'=>array());
										
			$xmp[0]['children'][0]['children'][0] = array(	'tag'=>'rdf:Description', 
															'attributes'=>array('rdf:about'=>'uuid:faf5bdd5-ba3d-11da-ad31-d33d75182f1b',
																				'xmlns:xmp'=>'http://ns.adobe.com/xap/1.0/'	)
															);
															
			$xmp[0]['children'][0]['children'][1] = array(	'tag'=>'rdf:Description', 
															'attributes'=>array('rdf:about'=>'uuid:faf5bdd5-ba3d-11da-ad31-d33d75182f1b',
																				'xmlns:MicrosoftPhoto'=>'http://ns.microsoft.com/photo/1.0/')
															);
			
		}
		return $xmp;
	}

	function get_KeyWord($url){
		$header_data = get_jpeg_header_data( $url );
		$xmpText = get_XMP_text( $header_data );
		$xmpArr = read_XMP_array_from_text( $xmpText );
		$i=0;
		$keys = array('');
		if ($r = search_tag($xmpArr, 'dc:subject')){
			if (isset($r['children'][0]['children'])){
				$keyWords = $r['children'][0]['children'];
				for ($i=0; $i<count($keyWords); $i++){
					$keys[$i] = $keyWords[$i]['value'];
				}
			}
		}
		return $keys;
	}

	function add_KeyWord($value, $url){
		$header_data = get_jpeg_header_data( $url );
		$xmpText = get_XMP_text( $header_data );
		$xmpArr = read_XMP_array_from_text( $xmpText );
			$xmpArr = checkXMP($xmpArr);
		$i=0;
		if (!set_key($xmpArr, 'dc:subject', $value)){
			$i = count($xmpArr[0]['children'][0]['children']);
			if(isset($xmpArr[0]['children'][0]['children'][$i]['attributes']['xmlns:dc'])){
				if(isset($xmpArr[0]['children'][0]['children'][$i]['children'])){
					$j = count($xmpArr[0]['children'][0]['children'][$i]['children']);
					$xmpArr[0]['children'][0]['children'][$i]['children'][$j]['tag'] = 'dc:subject';
					$xmpArr[0]['children'][0]['children'][$i]['children'][$j]['children'][0]['tag'] = 'rdf:Bag';
					$xmpArr[0]['children'][0]['children'][$i]['children'][$j]['children'][0]['attributes']['xmlns:rdf'] = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
					$xmpArr[0]['children'][0]['children'][$i]['children'][$j]['children'][0]['children'][0]['tag'] = 'rdf:li';
					$xmpArr[0]['children'][0]['children'][$i]['children'][$j]['children'][0]['children'][0]['value'] = $value;
				}else{
					$xmpArr[0]['children'][0]['children'][$i]['children'] = array();

					$xmpArr[0]['children'][0]['children'][$i]['children'][0]['tag'] = 'dc:subject';
					$xmpArr[0]['children'][0]['children'][$i]['children'][0]['children'][0]['tag'] = 'rdf:Bag';
					$xmpArr[0]['children'][0]['children'][$i]['children'][0]['children'][0]['attributes']['xmlns:rdf'] = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
					$xmpArr[0]['children'][0]['children'][$i]['children'][0]['children'][0]['children'][0]['tag'] = 'rdf:li';
					$xmpArr[0]['children'][0]['children'][$i]['children'][0]['children'][0]['children'][0]['value'] = $value;
				}
			}else{
				$xmpArr[0]['children'][0]['children'][$i]['tag']='rdf:Description';
				$xmpArr[0]['children'][0]['children'][$i]['attributes']['xmlns:dc']='http://purl.org/dc/elements/1.1/';
				$xmpArr[0]['children'][0]['children'][$i]['children'][0]['tag'] = 'dc:subject';
				$xmpArr[0]['children'][0]['children'][$i]['children'][0]['children'][0]['tag'] = 'rdf:Bag';
				$xmpArr[0]['children'][0]['children'][$i]['children'][0]['children'][0]['attributes']['xmlns:rdf'] = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
				$xmpArr[0]['children'][0]['children'][$i]['children'][0]['children'][0]['children'][0]['tag'] = 'rdf:li';
				$xmpArr[0]['children'][0]['children'][$i]['children'][0]['children'][0]['children'][0]['value'] = $value;
			}
	}
	$newXMP = write_XMP_array_to_text( $xmpArr );
	$header_data = put_XMP_text( $header_data, $newXMP );
	put_jpeg_header_data( $url, $url, $header_data );
}

	function del_KeyWord($value, $url){
		$header_data = get_jpeg_header_data( $url );
		$xmpText = get_XMP_text( $header_data );
		$xmpArr = read_XMP_array_from_text( $xmpText );
		$i=0;
		del_key($xmpArr, $value);
		$newXMP = write_XMP_array_to_text( $xmpArr );
		$header_data = put_XMP_text( $header_data, $newXMP );
		put_jpeg_header_data( $url, $url, $header_data );
	}

	
	function set_key(&$arr, $tag, $value){
		for ($i=0; $i<count($arr); $i++){
			if(isset($arr[$i]['tag'])){
				if ($arr[$i]['tag'] == $tag){
					$keyWords = &$arr[$i]['children'][0]['children'];
					$j = count($keyWords);
					$keyWords[$j]['tag'] = 'rdf:li';
					$keyWords[$j]['value'] = strval($value);
					return $arr[$i];
				}else{
					if (isset($arr[$i]['children'])){
						$resalt = set_key($arr[$i]['children'], $tag, $value);
						if ($resalt){
							return $resalt;
						}
					}
				}
			}
		}
	}

	function del_key(&$arr, $value){
		$tag = 'dc:subject';
		for ($i=0; $i<count($arr); $i++){
			if(isset($arr[$i]['tag'])){
				if ($arr[$i]['tag'] == $tag){
					$keyWords = &$arr[$i]['children'][0]['children'];
					$l = count($keyWords);
					$new_keyWords=array();
					for ($j=0; $j<$l; $j++){
						if ($keyWords[$j]['value'] != $value){
							$new_keyWords[] = array('tag'=>'rdf:li', 'value'=>$keyWords[$j]['value']);
						}
					}
					$keyWords = $new_keyWords;
					return $arr[$i];
				}else{
					if (isset($arr[$i]['children'])){
						$resalt = del_key($arr[$i]['children'], $value);
						if ($resalt){
							return $resalt;
						}
					}
				}
			}
		}
	}

	function set_tag(&$arr, $tag, $value){
		for ($i=0; $i<count($arr); $i++){
			if(isset($arr[$i]['tag'])){
				if ($arr[$i]['tag'] == $tag){
					$arr[$i]['value']=$value;
					return $arr[$i];
				}else{
					if (isset($arr[$i]['children'])){
						$resalt = set_tag($arr[$i]['children'], $tag, $value);
						if ($resalt){
							return $resalt;
						}
					}
				}
			}
		}
	}
	
	function checkImg($imgName){
		if(preg_match("/\.jp.?g$|\.ti.?f$/i", $imgName))
		{
			return TRUE;
        }
		return FALSE;
	}
	
	function getFileName($path){
		$pathArr = split('[/\]', $path);
		$lenght = count($pathArr);
		return $pathArr[$lenght-1];
	}
?>