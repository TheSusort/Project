<?php
$Toolkit_Dir = "./PHP_JPEG_Metadata_Toolkit_1.12/";
include $Toolkit_Dir . 'Toolkit_Version.php';          // Change: added as of version 1.11
include $Toolkit_Dir . 'JPEG.php';                     // Change: Allow this example file to be easily relocatable - as of version 1.11
include $Toolkit_Dir . 'JFIF.php';
include $Toolkit_Dir . 'PictureInfo.php';
include $Toolkit_Dir . 'XMP.php';
include $Toolkit_Dir . 'Photoshop_IRB.php';
include $Toolkit_Dir . 'EXIF.php';

class img{
	public	$url;
	private	$exif_data;
	public	$header_data;
	// private	$xmp_text;
	// private $marker;
	public 	$comment;
	public	$tag;
	public	$rate;
	public	$orientation;
	
	public function __construct($url){
		$this->url 		= $url;
		$this->get_EXIF($url);
	}
	
	public function refrash(){
		
	}
	private function get_EXIF($url){
		$this->exif_data = get_EXIF_JPEG( $url );
		$this->header_data = get_jpeg_header_data( $url );
	}
	
	public function set_EXIF(){
		// $this->exif_data = get_EXIF_JPEG( $url );
		put_jpeg_header_data( $this->url, $this->url, $this->header_data );
	}

	public function get_Rating_xmp(){
		$this->get_EXIF($this->url);
		$xmp_text = get_XMP_text( $this->header_data );
// echo($xmp_text);
		preg_match('(<xmp:Rating>.?</xmp:Rating>)i', $xmp_text, $matches);
		if (!empty($matches)){
// print_r($matches);
			return $matches[0];
		}
	}
	
	public function set_Rating_xmp($value){
		$xmp_text = get_XMP_text( $this->header_data );
		$xmp_text = $this->check_xmp($xmp_text);
		$xmp_text = preg_replace('(<xmp:Rating>.?</xmp:Rating>)im', '<xmp:Rating>'.$value.'</xmp:Rating>', $xmp_text);
		$this->header_data = put_XMP_text($this->header_data, $xmp_text);
	}
	
	private function check_xmp($xmp_text){
		if (empty($xmp_text)){
			$xmp_text .=
				'<?xpacket begin=\'ï»¿\' id=\'W5M0MpCehiHzreSzNTczkc9d\'?>\n\r' .
				'<x:xmpmeta xmlns:x="adobe:ns:meta/">' .
					'<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">' .
						'<rdf:Description rdf:about="uuid:faf5bdd5-ba3d-11da-ad31-d33d75182f1b" xmlns:xmp="http://ns.adobe.com/xap/1.0/">' .
							'<xmp:Rating>' .
								'0' .
							'</xmp:Rating>' .
						'</rdf:Description>' .
						'<rdf:Description rdf:about="uuid:faf5bdd5-ba3d-11da-ad31-d33d75182f1b" xmlns:MicrosoftPhoto="http://ns.microsoft.com/photo/1.0/">' .
							'<MicrosoftPhoto:Rating>' .
								'0' .
							'</MicrosoftPhoto:Rating>' .
							'<MicrosoftPhoto:LastKeywordXMP>' .
								'<rdf:Bag xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">' .
								'</rdf:Bag>' .
							'</MicrosoftPhoto:LastKeywordXMP>' .
						'</rdf:Description>' .
					'</rdf:RDF>' .
				'</x:xmpmeta>'.
				'<?xpacket end=\'w\'?>';
		}else{
			if (empty(stripos($xmp_text, "<rdf:RDF"))){
				preg_match('(<x:xmpmeta.*>)i', $xmp_text, $matches);
				preg_replace($matches[0], $matches[0] .
							'<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">' .
							'</rdf:RDF>', 
							$xmp_text);
			}
			if (empty(stripos($xmp_text, "<xmp:Rating>"))){
				preg_match('(<rdf:RDF.*>)i', $xmp_text, $matches);
				$pattern = "(".$matches[0].")i";
				$str	 = $matches[0] .
								'<rdf:Description rdf:about="uuid:faf5bdd5-ba3d-11da-ad31-d33d75182f1b" xmlns:xmp="http://ns.adobe.com/xap/1.0/">' .
									'<xmp:Rating>' .
										'0' .
									'</xmp:Rating>' .
								'</rdf:Description>';
				preg_replace("$pattern", $str, $xmp_text);
			}
			if (empty(stripos($xmp_text, "xmlns:MicrosoftPhoto="))){
				preg_match('(</rdf:RDF>)i', $xmp_text, $matches);
				$pattern = "(".$matches[0].")i";
				$str	 = '<rdf:Description rdf:about="uuid:faf5bdd5-ba3d-11da-ad31-d33d75182f1b" xmlns:MicrosoftPhoto="http://ns.microsoft.com/photo/1.0/">' .
								'<MicrosoftPhoto:Rating>' .
									'0' .
								'</MicrosoftPhoto:Rating>' .
								'<MicrosoftPhoto:LastKeywordXMP>' .
									'<rdf:Bag xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">' .
									'</rdf:Bag>' .
								'</MicrosoftPhoto:LastKeywordXMP>' .
							'</rdf:Description>'.
							$matches[0];
				preg_replace("$pattern", $str, $xmp_text);
			}
			if (empty(stripos($xmp_text, "<MicrosoftPhoto:Rating>"))){
echo('3: '.$xmp_text."\r\n");
				preg_match('(xmlns:MicrosoftPhoto=.*>)i', $xmp_text, $matches);
				$pattern = "(".$matches[0].")i";
				$str	 = $matches[0] .
							'<MicrosoftPhoto:Rating>' .
								'0' .
							'</MicrosoftPhoto:Rating>';
echo "Pattern:   ".$pattern."\n";
echo "String:    ".$str;							
				preg_replace($pattern, $str, $xmp_text);
echo("\r\n5: ".$xmp_text."\r\n");
			}
			if (empty(stripos($xmp_text, "<MicrosoftPhoto:LastKeywordXMP>"))){

				preg_match('(xmlns:MicrosoftPhoto=.*>)i', $xmp_text, $matches);
				preg_replace($matches[0], 
							$matches[0] .
								'<MicrosoftPhoto:LastKeywordXMP>' .
									'<rdf:Bag xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">' .
									'</rdf:Bag>' .
								'</MicrosoftPhoto:LastKeywordXMP>' ,
							$xmp_text);
			}
		}
		return $xmp_text;
	}
	
	public function get_Rating_MSPhoto(){
		$this->get_EXIF($this->url);
		$xmp_text = get_XMP_text( $this->header_data );
		preg_match('(<MicrosoftPhoto:Rating>\d+</MicrosoftPhoto:Rating>)i', $xmp_text, $matches);
		if (!empty($matches)){
			print_r($matches);
			return $matches[0];
		}
	}
	
	public function set_Rating_MSPhoto($value){
		$xmp_text = get_XMP_text( $this->header_data );
		$xmp_text = preg_replace('(<MicrosoftPhoto:Rating>\d+</MicrosoftPhoto:Rating>)im', '<MicrosoftPhoto:Rating>'.$value.'</MicrosoftPhoto:Rating>', $xmp_text);
		$this->header_data = put_XMP_text($this->header_data, $xmp_text);
	}
	
	private function new_Rating_MSPhoto(){
		
	}
	
	public function set_Rating($value){
	
		$value_prsnt = ((int)$value - 1) * 24 + 1;
		$this->set_Rating_xmp($value);
		$this->set_Rating_MSPhoto($value_prsnt);
		// echo($value_prsnt);
		
		// $this->exif_data[0][0x4746]['Text Value'] = $value;
		// $this->exif_data[0][0x4746]['Data'][0] = $value;
		
		// $this->exif_data[0][0x4749]['Text Value'] = $value_prsnt;
		// $this->exif_data[0][0x4749]['Data'][0] = $value_prsnt;
		
		// $this->header_data = put_EXIF_JPEG( $this->exif_data, $this->header_data );
		}
	
	public function add_to_Marker($mrkr, $value){
		
	}
	
	public function getRotation(){
		
	}
	
	public function turnOn($turnOn){
		
	}
}
$img = new img('Bilder/output.jpg');
$value = 3;

$img->get_Rating_xmp();
$img->get_Rating_MSPhoto();

$img->set_Rating($value);
// $img->set_EXIF();

$img->get_Rating_xmp();
$img->get_Rating_MSPhoto();

// $img->header_data = put_XMP_text($img->header_data, $str);
// put_jpeg_header_data( $img->url, $img->url, $img->header_data );

?>