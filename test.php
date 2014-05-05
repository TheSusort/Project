<?php
	// require_once('PEL/src/PelJpeg.php');
	require_once('funksjoner.php');
	
	$url = 'Bilder/DSC_0025.jpg';
	
	$header_data = get_jpeg_header_data( $url );
	$xmpText = get_XMP_text( $header_data );
	$xmpArr = read_XMP_array_from_text( $xmpText );
	print_r($xmpArr);
	
	// add_KeyWord('qe', $url);
	
	// echo('aa'.getMetaTag_PEL($url, PelTag::XP_COMMENT));
	
 ?>