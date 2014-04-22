<?php
$Toolkit_Dir = "./PHP_JPEG_Metadata_Toolkit_1.12/";
include $Toolkit_Dir . 'Toolkit_Version.php';          // Change: added as of version 1.11
include $Toolkit_Dir . 'JPEG.php';                     // Change: Allow this example file to be easily relocatable - as of version 1.11
include $Toolkit_Dir . 'JFIF.php';
include $Toolkit_Dir . 'PictureInfo.php';
include $Toolkit_Dir . 'XMP.php';
include $Toolkit_Dir . 'Photoshop_IRB.php';
include $Toolkit_Dir . 'EXIF.php';

$exif_data = get_EXIF_JPEG('Bilder/output.jpg');
$jpeg_header_data = get_jpeg_header_data('Bilder/output.jpg');

// print_r($exif_data[0][0x4746]['Text Value']);
// print_r($exif_data[0][0x4749]['Text Value']);

$exif_data[0][0x4746]['Tag Number'] = '18246';
$exif_data[0][0x4746]['Tag Name'] = 'Unknown Tag #18246';
$exif_data[0][0x4746]['Tag Description'] = '';
$exif_data[0][0x4746]['Data Type'] = 3;
$exif_data[0][0x4746]['Type'] = 'Unknown';
$exif_data[0][0x4746]['Units'] = '';
$exif_data[0][0x4746]['Data'] = Array(3);
$exif_data[0][0x4746]['Text Value'] = 3;
$exif_data[0][0x4746]['Decoded'] = '';

// print_r($exif_data[0][0x4746]);
// print_r($exif_data);

print_r($jpeg_header_data);

$jpeg_header_data = put_EXIF_JPEG( $exif_data, $jpeg_header_data );
// put_jpeg_header_data( $filename, "output.jpg", $jpeg_header_data );

print_r(get_EXIF_JPEG('Bilder/output.jpg'));

?>