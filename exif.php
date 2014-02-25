<?php
require_once('PEL/src/PelJpeg.php');
require_once('PEL/src/PelTiff.php');

$file = 'Bilder/Repin_Cossacks.jpg';

/* We typically need lots of RAM to parse TIFF images since they tend
 * to be big and uncompressed. */
ini_set('memory_limit', '32M');
 
$data = new PelDataWindow(file_get_contents($file));
 
if (PelJpeg::isValid($data)) {
  $img = new PelJpeg();
} elseif (PelTiff::isValid($data)) {
  $img = new PelTiff();
} else {
  print("Unrecognized image format! The first 16 bytes follow:\n");
  PelConvert::bytesToDump($data->getBytes(0, 16));
  exit(1);
}

/* Try loading the data. */
$img->load($data);
 
								$exif = $img->getSection(PelJpegMarker::APP1);
						$tiff = $exif->getTiff();
				$ifd0 = $tiff->getIfd();
		$entry = $ifd0->getEntry(PelTag::DATE_TIME);
$time = $entry->getValue();


  // $jpeg = new PelJpeg('Bilder/cute.jpg');
  // $ifd0 = $jpeg->getExif()->getTiff()->getIfd();
  // $entry = $ifd0->getEntry(PelTag::IMAGE_DESCRIPTION);
  // $entry->setValue('Edited by PEL');
  // $jpeg->saveFile('Bilder/ex.jpg');
?>