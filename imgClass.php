<?php
// require_once('exif.php');
require_once('PEL/src/PelDataWindow.php');
require_once('PEL/src/PelJpeg.php');
require_once('PEL/src/PelTiff.php');

class img{
	public	$url;
	private	$exif_objekt;
	private	$ifd0;
	private $marker;
	public 	$comment;
	public	$tag;
	public	$rate;
	public	$orientation;
	
	public function __construct($url){
		$this->url 			= $url;
		$this->get_EXIF($url);
		$this->comment 		= $this->get_Marker('XP_COMMENT');
		$this->tag 			= $this->get_Marker('XP_KEYWORDS');
		// $this->rate 		= $this->get_Marker('');
	}
	
	public function refrash(){
		$this->comment 		= $this->get_Marker('XP_COMMENT');
		$this->tag 			= $this->get_Marker('XP_KEYWORDS');
		// $this->rate 		= $this->get_Marker('');
	}
	private function get_EXIF($url){

		/* We typically need lots of RAM to parse TIFF images since they tend
		 * to be big and uncompressed. */
		ini_set('memory_limit', '124M');
		 
		/* The input file is now read into a PelDataWindow object.  At this
		 * point we do not know if the file stores JPEG or TIFF data, so
		 * instead of using one of the loadFile methods on PelJpeg or PelTiff
		 * we store the data in a PelDataWindow. */
		$data = new PelDataWindow(file_get_contents($this->url));
		/* The static isValid methods in PelJpeg and PelTiff will tell us in
		 * an efficient maner which kind of data we are dealing with. */
		if (PelJpeg::isValid($data)) {
		  /* The data was recognized as JPEG data, so we create a new empty
		   * PelJpeg object which will hold it.  When we want to save the
		   * image again, we need to know which object to same (using the
		   * getBytes method), so we store $jpeg as $file too. */
			$jpeg = $this->exif_objekt = new PelJpeg();

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
				// echo('No APP1 section found, added new.<br/>');

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
				// echo('Found existing APP1 section.<br/>');
				$tiff = $exif->getTiff();
			}
		} elseif (PelTiff::isValid($data)) {
		  /* The data was recognized as TIFF data.  We prepare a PelTiff
		   * object to hold it, and record in $file that the PelTiff object is
		   * the top-most object (the one on which we will call getBytes). */
			$tiff = $this->exif_objekt = new PelTiff();
		  /* Now load the data. */
			$tiff->load($data);
		} else {
		  /* The data was not recognized as either JPEG or TIFF data.
		   * Complain loudly, dump the first 16 bytes, and exit. */
			echo('Unrecognized image format! The first 16 bytes follow:<br/>');
			PelConvert::bytesToDump($data->getBytes(0, 16));
			exit(1);
		}
		
		/* TIFF data has a tree structure much like a file system.  There is a
		 * root IFD (Image File Directory) which contains a number of entries
		 * and maybe a link to the next IFD.  The IFDs are chained together
		 * like this, but some of them can also contain what is known as
		 * sub-IFDs.  For our purpose we only need the first IFD, for this is
		 * where the image description should be stored. */
		$this->ifd0 = $tiff->getIfd();

		if ($this->ifd0 == null) {
		  /* No IFD in the TIFF data?  This probably means that the image
		   * didn't have any Exif information to start with, and so an empty
		   * PelTiff object was inserted by the code above.  But this is no
		   * problem, we just create and inserts an empty PelIfd object. */
		  // echo('No IFD found, adding new.<br/>');
		  $this->ifd0 = new PelIfd(PelIfd::IFD0);
		  $tiff->setIfd($this->ifd0);
		}
	}

	public function get_Marker($mrkr){
		/* Each entry in an IFD is identified with a tag.  This will load the
		 * ImageDescription entry if it is present.  If the IFD does not
		 * contain such an entry, null will be returned. */
		$marker = $this->ifd0->getEntry(constant('PelTag::'. $mrkr));

		/* We need to check if the image already had a Marker stored. */
		if ($marker == null) {
			/* The was no $marker in the image. */
			// echo("Added new empty Marker $mrkr, bat not stored in file.<br/>");

			/* In this case we simply create a new PelEntryAscii object to hold
			 * the Marker.  The constructor for PelEntryAscii needs to know
			 * the tag and contents of the new entry. */
			$marker = new PelEntryAscii(constant('PelTag::'. $mrkr), '');

			/* This will insert the newly created entry with the Marker
			 * into the IFD. */
			$this->ifd0->addEntry($marker);
			return '';
		}else {
			echo($marker.': '.$marker->getValue().'<br/>');
			return($marker_value = $marker->getValue());
		}
	}
	
	public function set_to_Marker($mrkr, $value){
		$marker = $this->ifd0->getEntry(constant('PelTag::'. $mrkr));
		$marker->setValue($value);
		$this->exif_objekt->saveFile($this->url);
	}
	
	public function add_to_Marker($mrkr, $value){
		$old_value = $this->get_Marker($mrkr);
		$marker = $this->ifd0->getEntry(constant('PelTag::'. $mrkr));
		$marker->setValue($old_value.$value);
		$this->exif_objekt->saveFile($this->url);
	}
	
	public function getRotation(){
		$orientation = $this->get_Marker('ORIENTATION');
		if (isset($orientation)){
			$orientation = (int)$orientation ;
		}else{
			$orientation = 0;
		}
		
		switch($orientation) {
            case 1:
            case 2:
                return 0;
            break;
            case 3:
            case 4:
                return 180;
            break;
            case 5:
            case 6:
                return 90;
            break;
            case 7:
            case 8:
                return 270;
            break;
            default:
                return 0;
            break;
        }
	}
	
	public function turnOn($turnOn){
		if(!isset($turnOn)){
			$turnOn =  $this->getRotation();
		}
		echo('orientation: '.$turnOn.'<br>');
		if ($turnOn > 0) {
			$imagick = new Imagick();
			$imagick->readImage(__DIR__ . DIRECTORY_SEPARATOR . $this->url); 
			$imagick->rotateImage(new ImagickPixel('none'), $turnOn); 
			$imagick->writeImage(__DIR__ . DIRECTORY_SEPARATOR . $this->url); 
			$imagick->clear(); 
			$imagick->destroy();
		}
	}
}

?>