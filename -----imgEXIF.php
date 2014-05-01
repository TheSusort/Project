<?php
/*
PHP_JPEG_Metadata_Toolkit_1.12
*/

$Toolkit_Dir = "./PHP_JPEG_Metadata_Toolkit_1.12/";
include_once $Toolkit_Dir . 'Toolkit_Version.php';          // Change: added as of version 1.11
include_once $Toolkit_Dir . 'JPEG.php';                     // Change: Allow this example file to be easily relocatable - as of version 1.11
include_once $Toolkit_Dir . 'JFIF.php';
include_once $Toolkit_Dir . 'PictureInfo.php';
include_once $Toolkit_Dir . 'XMP.php';
include_once $Toolkit_Dir . 'Photoshop_IRB.php';
include_once $Toolkit_Dir . 'EXIF.php';


// function get_Rating($url){
	// $header_data = get_jpeg_header_data( $url );
	// $xmpText = get_XMP_text( $header_data );
	// $xmpArr = read_XMP_array_from_text( $xmpText );
	// $r = search_tag($xmpArr, 'xmp:Rating');
	// if (isset($r['value'])){
		// return $r['value'];
	// }else{
		// return false;
	// }
// }

function set_Rating($value, $url){
	$header_data = get_jpeg_header_data( $url );
	$xmpText = get_XMP_text( $header_data );
	$xmpArr = read_XMP_array_from_text( $xmpText );
		$xmpArr = checkXMP($xmpArr);
		
	$i=0;
	$value_prsnt = strval(((int)$value - 1) * 24 + 1);
	
	if (!set_tag($xmpArr, 'xmp:Rating', $value) & !set_tag($xmpArr, 'MicrosoftPhoto:Rating', $value_prsnt)){
		$xmpArr[0]['children'][0]['children'][0]['children'][0]['tag'] = 'xmp:Rating';
		$xmpArr[0]['children'][0]['children'][0]['children'][0]['value'] = $value;
		
		$xmpArr[0]['children'][0]['children'][1]['children'][0]['tag'] = 'MicrosoftPhoto:Rating';
		$xmpArr[0]['children'][0]['children'][1]['children'][0]['value'] = $value_prsnt;
	}
	$newXMP = write_XMP_array_to_text( $xmpArr );
	$header_data = put_XMP_text( $header_data, $newXMP );
	put_jpeg_header_data( $url, $url, $header_data );
}

function get_KeyWord($url){
	$header_data = get_jpeg_header_data( $url );
	$xmpText = get_XMP_text( $header_data );
	$xmpArr = read_XMP_array_from_text( $xmpText );
	$i=0;
	$keys = array('');
	if ($r = search_tag($xmpArr, 'dc:subject')){
		$keyWords = $r['children'][0]['children'];
		for ($i=0; $i<count($keyWords); $i++){
			$keys[$i] = $keyWords[$i]['value'];
		}
	}
	return $keys;
}

function add_KeyWord($value, $url){
	$header_data = get_jpeg_header_data( $url );
	$xmpText = get_XMP_text( $header_data );
	$xmpArr = read_XMP_array_from_text( $xmpText );
	// print_r($xmpArr);
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
	// print_r($xmpArr);
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

function checkXMP($xmp){
	if(empty($xmp)){
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

// $value = '3';
// $url = 'Bilder/1497863_637766332932353_1505618577_o[1].jpg';
// $url = 'Bilder/Seal.jpg';
// $url = 'Bilder/Bilde095.jpg';
$url = 'Bilder/test.jpg';
// print_r(get_KeyWord($url));
// set_Rating($value, $url);
// del_KeyWord('q', $url);
// print_r(get_KeyWord($url));
 // add_KeyWord('q', $url);
// print_r(get_KeyWord($url));
	// $exif_data = get_EXIF_JPEG( $url );
	
	// $header_data = get_jpeg_header_data( $url );
	// $xmpText = get_XMP_text( $header_data );
	// $xmpArr = read_XMP_array_from_text( $xmpText );
	// del_key($xmpArr, $value);
	// $r = set_key($xmpArr, 'dc:subject', $value);
	// $r['value']='3';
	
	// print_r(search_tag($xmpArr, 'xmp:Rating'));
	// print_r($r);
	$header_data = get_jpeg_header_data( $url );
	$xmpText = get_XMP_text( $header_data );
	$xmpArr = read_XMP_array_from_text( $xmpText );
	print_r($xmpArr);
	
	add_KeyWord('qe', $url);
	
	$header_data = get_jpeg_header_data( $url );
	$xmpText = get_XMP_text( $header_data );
	$xmpArr = read_XMP_array_from_text( $xmpText );
	print_r($xmpArr);
	// print_r(checkXMP($xmpArr));
	// add_KeyWord('qqq', $url);
	// print_r(get_KeyWord($url));
?>