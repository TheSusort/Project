<?php

function createThumbs($filename, $path_to_image_directory, $path_to_thumbs_directory, $final_width_of_image) {
     
     //sjekker hva slags bilde det er snakk om, og loader det.
    if(preg_match('/[.](jpg)$/i', $filename) or preg_match('/[.](jpeg)$/i', $filename)) {
        $im = imagecreatefromjpeg($path_to_image_directory . $filename);
    } else if (preg_match('/[.](gif)$/i', $filename)) {
        $im = imagecreatefromgif($path_to_image_directory . $filename);
    } else if (preg_match('/[.](png)$/i', $filename)) {
        $im = imagecreatefrompng($path_to_image_directory . $filename);
    }
    
     //finner dimensjoner
    $ox = imagesx($im);
    $oy = imagesy($im);
    
     //regner ut thumbnaildimensjoner
    $nx = $final_width_of_image;
    $ny = floor($oy * ($final_width_of_image / $ox));
    
     //Lager nytt bilde i thumbnailstørrelse, kopierer og resizer bilde til thumbnailfilen.
    $nm = imagecreatetruecolor($nx, $ny);
    imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
     
    if(!file_exists($path_to_thumbs_directory)) {
      if(!mkdir($path_to_thumbs_directory)) {
           die("There was a problem with creating the thumbnail. Please try again!");
      }
       }
    //lagrer thumbnailen til mappe. 
    imagejpeg($nm, $path_to_thumbs_directory . $filename);
    
}

?>