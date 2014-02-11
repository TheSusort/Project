<?php
//	$images = "Bilder/thumbs/";                # Location of small versions
//	$big    = "Bilder/";                       # Location of big versions (assumed to be a subdir of above)
//	$cols   = 3;                        # Number of columns to display
//    $files  = get_img_list("Bilder");   # List of the files from disk
//
//    include_once("thumbnail.php");
//
//	function VisBilder()
//	{
//		global $images, $big, $cols, $files;
//        $colCtr = 0;
//        $gallery = "";
//        if ($GLOBALS['db_is_connected'])
//        {
//            check_for_new_img('Bilder');
//            check_for_del_img('Bilder');
//        }
//        if ($files != null)
//        {
//            $gallery =  '<table width="100%" cellspacing="3"><tr>';
//
//            foreach($files as $file)
//            {
//                //lager thumbs hvis det ikke er laget thumbs avbildet tidligere.
//                $thumb = $images.$file;
//                if(!file_exists($thumb))
//                {
//                       createThumbs($file, "Bilder/", "Bilder/thumbs/", 200);
//                }
//              if($colCtr %$cols == 0)
//                  $gallery = $gallery. '
//                        </tr>
//                        <tr>
//                            <td colspan="'.$cols.'">
//                                <hr />
//                            </td>
//                        </tr>
//                        <tr>';
//                $gallery = $gallery. '
//                        <td id="bilde" align="center">
//                            <a href="fullscreen.php?bilde='.$big.$file.'" target="_blank">
//                                <img src="' . $images . $file . '" />
//                            </a>
//                        </td>';
//                $colCtr++;
//            }
//
//            $gallery = $gallery. '</table>' . "\r\n";
//        }
//	    return $gallery;
//    }
//
//// return array of files names from dir.
//    function get_File_List($big, $images)
//    {
//        if ($handle = opendir($images)) {
//            while (false !== ($file = readdir($handle))) {
//                if ($file != "." && $file != ".." && $file != rtrim($big,"/")) {
//                    $files[] = $file;
//                }
//            }
//            closedir($handle);
//        }
//        return $files;
//    }
//
//// return array of .jpg and .bmp files names from dir.
//    function get_img_list($dir)
//    {
//        $f = scandir($dir);
//        $files = null;
//        foreach ($f as $file){
//            if(preg_match("/\.jp.?g$|\.png$|\.gif$/i", $file)){
//                $files[] = $file;
//            }
//        }
//        return $files;
//    }
//
//?>