<?php
	require_once('PEL/src/PelJpeg.php');
	require_once('funksjoner.php');
	
	// $url = 'test.jpg';
	$url = 'Bilder/IMG_2893.jpg';
	
	$header_data = get_jpeg_header_data( $url );
	$xmpText = get_XMP_text( $header_data );
	$xmpArr = read_XMP_array_from_text( $xmpText );
	print_r($xmpArr);
	
	// $xmpArr = checkXMP($xmpArr);
	// print_r($xmpArr);
	
	// del_KeyWord('e', $url);
	
	// $header_data = get_jpeg_header_data( $url );
	// $xmpText = get_XMP_text( $header_data );
	// $xmpArr = read_XMP_array_from_text( $xmpText );
	// print_r($xmpArr);
	
	// echo('aa'.getMetaTag_PEL($url, PelTag::XP_COMMENT));
	
	//==TEST=========================================================================
			// $data1 = new PelDataWindow(file_get_contents($url));
			// $jpeg1 = new PelJpeg();
			// $jpeg1->load($data1);
			// $exif1 = $jpeg1->getExif();
			// $tiff1 = $exif1->getTiff();
			// $ifd01 = $tiff1->getIfd();
			// echo(getMetaTag_PEL($url, PelTag::XP_COMMENT));
		//===============================================================================
 ?>