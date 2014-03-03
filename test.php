<?php
$Toolkit_Dir = "./PHP_JPEG_Metadata_Toolkit_1.12/";
include $Toolkit_Dir . 'Toolkit_Version.php';          // Change: added as of version 1.11
include $Toolkit_Dir . 'JPEG.php';                     // Change: Allow this example file to be easily relocatable - as of version 1.11
include $Toolkit_Dir . 'JFIF.php';
include $Toolkit_Dir . 'PictureInfo.php';
include $Toolkit_Dir . 'XMP.php';
include $Toolkit_Dir . 'Photoshop_IRB.php';
include $Toolkit_Dir . 'EXIF.php';

$filename = __DIR__ . DIRECTORY_SEPARATOR . 'output.jpg';
$exif_data = get_EXIF_JPEG( $filename );
$jpeg_header_data = get_jpeg_header_data( $filename );

print_r($exif_data[0][0x4746]['Text Value']);
print_r($exif_data[0][0x4749]['Text Value']);

$xmp_arr = read_XMP_array_from_text($xmp_text = get_XMP_text( $jpeg_header_data ));
print_r($xmp_arr);//[0]['children'][0]['children'][0]['children'][0]);
$xmp_text = preg_replace('(<xmp:Rating>.?</xmp:Rating>)i', '<xmp:Rating>'.'1'.'</xmp:Rating>', $xmp_text);
$xmp_text = preg_replace('(<MicrosoftPhoto:Rating>*</MicrosoftPhoto:Rating>)i', '<MicrosoftPhoto:Rating>'.''.'</MicrosoftPhoto:Rating>', $xmp_text);
echo($xmp_text);
$jpeg_header_data = put_XMP_text($jpeg_header_data, $xmp_text);

$exif_data[0][0x4746]['Text Value'] = 3;
$exif_data[0][0x4746]['Data'][0] = 3;
print_r($exif_data[0][0x4746]);

$exif_data[0][0x4749]['Text Value'] = 50;
$exif_data[0][0x4749]['Data'][0] = 50;
print_r($exif_data[0][0x4749]);

$jpeg_header_data = put_EXIF_JPEG( $exif_data, $jpeg_header_data );
put_jpeg_header_data( $filename, "output.jpg", $jpeg_header_data );

print_r(get_EXIF_JPEG('output.jpg'));


// include('imgClass.php');
$img = new Imagick();
$img->readImage(__DIR__ . DIRECTORY_SEPARATOR . 'output.jpg');
print_r( $img->getImageProperties());


?>