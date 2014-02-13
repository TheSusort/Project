<?php

$images = "Bilder/thumbs/";         # Location of small versions
$big    = "Bilder/";                # Location of big versions (assumed to be a subdir of above)
$cols   = 3;                        # Number of columns to display
$files  = null;                     # List of the files from disk

// Transformation array to string
    function array_to_string($array)
    {
        reset($array);
        $str = current($array);
        while (next($array) <> null)
        {
            $str = "$str, ". current($array);
        }
        return $str;
    }

// Show message in the new window
    function alert_message($message)
    {
        echo("
                <script type=\"text/javascript\">
                    alert(\"$message\");
                </script>
            ");
    }

	// Check for image modification
    function check_img_modification($dir)
    {
        global $files;
        $files_on_disc  = get_img_list($dir);
        $files_in_db    = db_select('file_liste', 'filename', 'ORDER BY rating', 'filename');
        check_for_new_img($files_on_disc, $files_in_db);
        check_for_del_img($files_on_disc, $files_in_db);
        if ($files == null){
            $files = $files_in_db;
        }
    }

// check images which are not registered in database and add them to the db
    function check_for_new_img($files_on_disc, $files_in_db)
    {
        global $files, $big, $images;
        $result = null;
        $files_add = 'Downloaded manually:';
        if ($files_on_disc <> null & $files_in_db == null){
            $result = $files_on_disc;
        }elseif ($files_on_disc <> null & $files_in_db <> null){
            $result = array_diff($files_on_disc, $files_in_db);
        }else{
            return FALSE;
        }
        foreach($result as $rslt)
        {
            if (db_insert('file_liste', 'filename', $rslt))
            {
                $files_add = $files_add.'\n\t'.$rslt;
                createThumbs($rslt, $big, $images, 200);
            }
        }
        if (!empty($result)){
            alert_message($files_add);
            $files = db_select('file_liste', 'filename', 'ORDER BY rating', 'filename');
        }
    }

// check images which are deleted from disc and delete them from the db.
    function check_for_del_img($files_on_disc, $files_in_db)
    {
        global $files, $images;
        $result = null;
        $files_del = 'Was deleted manually:';
        if ($files_on_disc == null & $files_in_db <> null){
            $result = $files_in_db;
        }elseif($files_on_disc <> null & $files_in_db <> null){
            $result = array_diff($files_in_db, $files_on_disc);
        }else{
            return FALSE;
        }
        foreach($result as $rslt)
        {
            if(db_delete('file_liste', 'filename', $rslt)){
                $files_del = $files_del.'\n\t'.$rslt;
                unlink($images.$rslt);
            }
        }
        if (!empty($result)){
            alert_message($files_del);
            $files = db_select('file_liste', 'filename', 'ORDER BY rating', 'filename');
        }
    }
	
// Upload files
    function save_file()
        {
            global $images, $big;
            $i=0;
            $files_add = '\tLagres paa serveren:\n';
            $files_not_add = '\n\n\tVar samme navn som en annen fil:\n';
            if (!empty($_FILES["bildefil"]["tmp_name"][0])){
                foreach ($_FILES["bildefil"]["tmp_name"] as $file)
                {
                    if (file_exists($big.$_FILES["bildefil"]["name"][$i]))
                    {
                        $files_not_add = $files_not_add.'\n '.$_FILES["bildefil"]["name"][$i];
                    }elseif (!move_uploaded_file($file, $big.$_FILES["bildefil"]["name"][$i])){
                        alert_message("Alt gikk galt. :-(");
                        return FALSE;
                    }else{
                        $files_add = $files_add.'\n'.$_FILES["bildefil"]["name"][$i].'. \t\tSeze: '.round(($_FILES["bildefil"]["size"][$i] / 1024),2)."KB";
                        //lager thumbnail av bilde
                        createThumbs($_FILES["bildefil"]["name"][$i], $big, $images, 200);
                        if ($GLOBALS['db_is_connected'])
                        {
                            if (!db_insert('file_liste', 'filename', $_FILES["bildefil"]["name"][$i]))
                            {
                                alert_message('ERROR. can\'t insert into database.');
                                return FALSE;
                            };
                        }
                    }
                    $i++;
                }
                return "$files_add"."$files_not_add";
            }
        }

// Generate HTML cod for gallery. Return sting.
    function VisBilder()
    {
        global $images, $big, $cols, $files;
        $colCtr = 0;
        $gallery = "";
        if ($files != null)
        {
            $gallery =  '
                <table width="100%" cellspacing="3">
                    <tr>';

            foreach($files as $file)
            {
                //lager thumbs hvis det ikke er laget thumbs avbildet tidligere.
                $thumb = $images.$file;
                if(!file_exists($thumb))
                {
                    createThumbs($file, "Bilder/", "Bilder/thumbs/", 200);
                }
                if($colCtr %$cols == 0)
                    $gallery = $gallery. '
                        </tr>
                        <tr>
                            <td colspan="'.$cols.'">
                                <hr />
                            </td>
                        </tr>
                        <tr>';
                $gallery = $gallery. '
                            <td id="bilde" align="center">
                                <a href="fullscreen.php?bilde='.$big.$file.'" target="_blank">
                                    <img src="' . $images . $file . '" />
                                </a>
                            </td>';
                $colCtr++;
            }

            $gallery = $gallery. '</table>' . "\r\n";
        }
        return $gallery;
    }

// Generate HTML cod for tags list
	function gen_tags()
	{
		$tags_str = "<ul>\n\r";
		$tags = get_tags();
		if (!empty($tags)){
			foreach($tags as $tag)
			{
				$tags_str = $tags_str."<li><a href=\"#\"><span>".$tag."</span></a></li>\r\n";
			}
		}
		$tags_str = $tags_str."</ul>";
		return $tags_str;
	}

// return array of files names from dir.
    function get_File_List($big, $images)
    {
        if ($handle = opendir($images)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && $file != rtrim($big,"/")) {
                    $files[] = $file;
                }
            }
            closedir($handle);
        }
        return $files;
    }

// return array of .jpg and .bmp files names from dir.
    function get_img_list($dir)
    {
        $f = scandir($dir);
        $files = null;
        foreach ($f as $file){
            if(preg_match("/\.jp.?g$|\.png$|\.gif$/i", $file)){
                $files[] = $file;
            }
        }
        return $files;
    }

// Get tags list from db
//	return: tags list [array]
    function get_tags()
    {
        $tag_list = db_select('tag', 'tags', 'GROUP BY tags', 'tags');
        return $tag_list;
    }

// Generate Thumbs
    function createThumbs($filename, $path_to_image_directory, $path_to_thumbs_directory, $final_width_of_image) {

        //sjekker hva slags bilde det er snakk om, og loader det.
        if(preg_match('/[.](jp.?g)$/i', $filename)){
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