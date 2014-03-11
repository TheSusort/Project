<?php
include_once('image.php');

include_once("mysql.php");

$images = "Bilder/thumbs/";         # Location of small versions
$big    = "Bilder/";                # Location of big versions (assumed to be a subdir of above)
$cols   = 4;                        # Number of columns to display
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
		$message = '';
        $files_on_disc  = get_img_list($dir);
        $files_in_db    = db_select('file_liste', 'filename', 'ORDER BY rating', 'filename');
		$new = check_for_new_img($files_on_disc, $files_in_db);
		$del = check_for_del_img($files_on_disc, $files_in_db);
		if ($new) { $message = $message . $new;}
        if ($del) { $message = $message . '\n' . $del;}
        if ($files == null){
            $files = $files_in_db;
        }
		return $message;
    }

// check images which are not registered in database and add them to the db
    function check_for_new_img($files_on_disc, $files_in_db)
    {
        global $files, $big, $images;
        $result = null;
        $files_new_str = 'Downloaded manually:';
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
                $files_new_str = $files_new_str.'\n\t'.$rslt;
                createThumbs($rslt, $big, $images, 200);
            }
        }
        if (!empty($result)){
            $files = db_select('file_liste', 'filename', 'ORDER BY rating', 'filename');
			return $files_new_str;
        }
    }

// check images which are deleted from disc and delete them from the db.
    function check_for_del_img($files_on_disc, $files_in_db)
    {
        global $files, $images;
        $result = null;
        $files_del_str = 'Was deleted manually:';
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
                $files_del_str = $files_del_str.'\n\t'.$rslt;
				if (file_exists($images.$rslt))
				{
					unlink($images.$rslt);
				}
            }
        }
        if (!empty($result)){
            $files = db_select('file_liste', 'filename', 'ORDER BY rating', 'filename');
			return $files_del_str;
        }
    }
	
// Upload files
    function save_file()
	{
		global $images, $big;
		$i=0;
		// $add = FALSE;
		$not_add = FALSE;
		// $files_add = '\tSaved on server:\n';
		$files_not_add = '\tFile\'s not upload:\n';
		$file_name = $_FILES["bildefil"]["name"];
		$file_tmp_name = $_FILES["bildefil"]["tmp_name"];
		
		if (!empty($file_tmp_name[0])){
			foreach ($file_tmp_name as $file)
			{
				if (check_img($file_name[$i])){
					if (file_exists($big.$file_name[$i])){
					
						$files_not_add = $files_not_add.'\n '.$file_name[$i].' File name already exist';
						$not_add = TRUE;
					
					}elseif (!move_uploaded_file($file, $big.$file_name[$i])){
					
						$files_not_add = $files_not_add.'\n '.$file_name[$i].' Something went wrong. :-(';
						$not_add = TRUE;
					
					}elseif (check_img($file_name[$i])){
						autoRotateImage(__DIR__ . DIRECTORY_SEPARATOR . $big . $file_name[$i],'');
						createThumbs($file_name[$i], $big, $images, 200);	//lager thumbnail av bilde
					
						if ($GLOBALS['db_is_connected']){
							if (!db_insert('file_liste', 'filename', $file_name[$i])){
								// alert_message('ERROR. can\'t insert into database.');
								// return FALSE;
							};
						}
					}
				}else{
					$files_not_add = $files_not_add.'\n '.$file_name[$i].' Illegal image type.';
					$not_add = TRUE;
				}
				$i++;
			}
			if($not_add){
				return $files_not_add;
			}
		}
	}

// Generate HTML cod for gallery. Return sting.
    function VisBilder($files)
    {
        global $images, $big, $cols, $files;
        $colCtr = 0;
        $gallery = "";
        if (empty($files)){
			$files = get_img_list($big);
		}
		if (!empty($files))
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
                            <td id="bilde" align="center" 
									onClick = viuwEXIF("'.get_EXIF($big.$file).'") 
									onDblClick = openFulskr("'.$big.$file.'")>
								<img src="' . $images . $file . '" />
                            </td>';
							// viuwEXIF("'.get_EXIF($big.$file).'") 
							// viuwEXIF("'.$big.$file.'")
                $colCtr++;
            }

            $gallery = $gallery. '</table>' . "\r\n";
        }
        return $gallery;
    }

// Generate HTML cod for tags list
	function gen_tags()
	{
		$tags_str = "<ul class=\"nav\">\n\r";
		$tags = get_tags();
		$tags_str = $tags_str."<li><a href=\"index.php\"><span>All</span></a></li>\r\n";
		if (!empty($tags)){
			foreach($tags as $tag)
			{
				$tags_str = $tags_str."<li><a href=\"?tag=$tag\"><span>".$tag."</span></a></li>\r\n";
			}
		}
		$tags_str = $tags_str."</ul>";
		return $tags_str;
	}

// get images list by tag
	function get_img_by_tag($tag)
	{
		$files = db_select('file_liste', 'filename', 'INNER JOIN tag ON file_liste.fileid = tag.fileid WHERE tag.tags = \''.$tag.'\'', 'filename');
		return $files;
	}

// eksperiment get images list by rating
	function get_img_by_rating($ratinglist)
	{
		$files = $ratinglist;
		return $files;
	}
	
// get EXIF data
	function get_EXIF($file)
	{
		// $img_ob = new img($file);
		// return $img_ob;
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
            if(check_img($file)){
                $files[] = $file;
            }
        }
        return $files;
    }

//Sjekke at bildet er en støttet type
	function check_img($img_name)
	{
		if(preg_match("/\.jp.?g$|\.png$|\.gif$|\.ti.?f$/i", $img_name))
		{
			return TRUE;
        }
		return FALSE;
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
        } elseif (preg_match('/[.](ti.?f)$/i', $filename)) {
			$im = imagecreatefromstring(file_get_contents($path_to_image_directory . $filename));
		} else return FALSE;

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

	// test search function
	
		function giveSearch($search1, $searchw1){
		
			if(isset($_POST['submission'])){
				$search = $search1;
				$searchw = $searchw1;
				
				$where = 'file_liste';
				$what = 'filename';
				
				$sQuery = "WHERE $searchw = $search";
				
				if($searchw=="commentary"){ 
					$sQuery = "WHERE $searchw LIKE '%$search%'";
					}
				
				if($searchw=="tag"){ 
					//$sQuery = 'INNER JOIN tag ON file_liste.fileid = tag.fileid WHERE tag.tags LIKE \''.$search.'\'';
					$sQuery = "INNER JOIN tag ON file_liste.fileid = tag.fileid WHERE tag.tags LIKE '$search%'";
				}
				
				//if($searchw=="clear"){
				//	$a = giveSearch($search, 'commentary');
				//	$b = giveSearch($search, 'tag');
				//	$c = $merge_array($a, $b);
				//	return array_unique($c);
				//}
				
				
				$files = db_select($where, $what, $sQuery, $what);
				if(!empty($files)){
					$files = array_unique($files);
					}
				
				if(!$files){
					alert_message("Search yields no results");
					return FALSE;
				}
				
				return $files;
				
			}
			
		}
	
		
?>
