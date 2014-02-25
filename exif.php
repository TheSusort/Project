<?php
require_once('PEL/src/PelDataWindow.php');
require_once('PEL/src/PelJpeg.php');
require_once('PEL/src/PelTiff.php');

/*
	Create an object $file;
	return object $file.
*/
function get_EXIF($url){
	$file = new img('Bilder/Repin_Cossacks.jpg');

	/* We typically need lots of RAM to parse TIFF images since they tend
	 * to be big and uncompressed. */
	ini_set('memory_limit', '32M');
	 
	/* The input file is now read into a PelDataWindow object.  At this
	 * point we do not know if the file stores JPEG or TIFF data, so
	 * instead of using one of the loadFile methods on PelJpeg or PelTiff
	 * we store the data in a PelDataWindow. */
	$data = new PelDataWindow(file_get_contents($file->url));
	/* The static isValid methods in PelJpeg and PelTiff will tell us in
	 * an efficient maner which kind of data we are dealing with. */
	if (PelJpeg::isValid($data)) {
	  /* The data was recognized as JPEG data, so we create a new empty
	   * PelJpeg object which will hold it.  When we want to save the
	   * image again, we need to know which object to same (using the
	   * getBytes method), so we store $jpeg as $file too. */
		$jpeg = $file->pel_objekt = new PelJpeg();

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
			echo('No APP1 section found, added new.');

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
			echo('Found existing APP1 section.');
			$tiff = $exif->getTiff();
		}
	} elseif (PelTiff::isValid($data)) {
	  /* The data was recognized as TIFF data.  We prepare a PelTiff
	   * object to hold it, and record in $file that the PelTiff object is
	   * the top-most object (the one on which we will call getBytes). */
		$tiff = $file->pel_objekt = new PelTiff();
	  /* Now load the data. */
		$tiff->load($data);
	} else {
	  /* The data was not recognized as either JPEG or TIFF data.
	   * Complain loudly, dump the first 16 bytes, and exit. */
		echo('Unrecognized image format! The first 16 bytes follow:');
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
	  echo('No IFD found, adding new.');
	  $ifd0 = new PelIfd(PelIfd::IFD0);
	  $tiff->setIfd($ifd0);
	}
	$file->ifd0 = $ifd0;
	return $file;
}
 
/* Each entry in an IFD is identified with a tag.  This will load the
 * ImageDescription entry if it is present.  If the IFD does not
 * contain such an entry, null will be returned. */
$desc = $ifd0->getEntry(PelTag::IMAGE_DESCRIPTION);

/* We need to check if the image already had a description stored. */
if ($desc == null) {
  /* The was no description in the image. */
  echo('Added new IMAGE_DESCRIPTION entry with '. $description);

  /* In this case we simply create a new PelEntryAscii object to hold
   * the description.  The constructor for PelEntryAscii needs to know
   * the tag and contents of the new entry. */
  $desc = new PelEntryAscii(PelTag::IMAGE_DESCRIPTION, $description);

  /* This will insert the newly created entry with the description
   * into the IFD. */
  $ifd0->addEntry($desc);
} else {
  /* An old description was found in the image. */
  echo('Updating IMAGE_DESCRIPTION entry from "%s" to "%s".'.
		 ($description .= $desc->getValue()));

  /* The description is simply updated with the new description. */
  $desc->setValue($description);
}
/* At this point the image on disk has not been changed, it is only
 * the object structure in memory which represent the image which has
 * been altered.  This structure can be converted into a string of
 * bytes with the getBytes method, and saving this in the output file
 * completes the script. */
echo( $file_q."<br/>");
$file_q->saveFile($file);




if (isset($value)){
	echo $value;
}
?>