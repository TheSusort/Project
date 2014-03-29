<?php
include_once('rotation.php');

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
	function gen_tags($fileslist1)
	{
		$tags_str = "<ul class=\"nav\">\n\r";
		$tags = get_tags($fileslist1);
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
    function get_tags($filelist1)
    {
			$filelist = $filelist1;
			$finalTags = array();
		
if(!empty($filelist)){		
			foreach($filelist as $rr){
				$where = 'tag';
				$what = 'tags';
				$sQuery3 = "INNER JOIN file_liste ON tag.fileid = file_liste.fileid WHERE file_liste.filename = '$rr'";
				$currentTags = db_select($where, $what, $sQuery3, $what);
				
				if(!empty($currentTags)){
					$currentTags = array_filter($currentTags);
					foreach($currentTags as $bb){
						array_push($finalTags, $bb);
					}
				}
			}	
					
			$sluttTags = array_filter($finalTags);
			
			// print_r($sluttTags);
			
			return $sluttTags;
			}
	
    //   $tag_list = db_select('tag', 'tags', 'GROUP BY tags', 'tags');
    //  return $tag_list;
	
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

	// Search function
		
		function giveSearch($search1, $rcat){
		
//			if(isset($_POST['submission'])){

				$search = $search1;
				
				if (strpos($search,"'") !== false) {
					alert_message("Invalid character!");
					return;
				}
				
				$where = 'file_liste';
				$what = 'filename';
				$files = null;
				
				$sQuery1 = "WHERE commentary LIKE '%$search%'";
				
				$sQuery2 = "INNER JOIN tag ON file_liste.fileid = tag.fileid WHERE tag.tags LIKE '$search%'";
				
				$files1 = db_select($where, $what, $sQuery1, $what);
				$files2 = db_select($where, $what, $sQuery2, $what);
				
				if(is_array($files1) && is_array($files2)){
					$files = array_merge($files1, $files2);
				}
				
				if(!empty($files1) && empty($files2)){
					$files = $files1;
				}
				
				if(empty($files1) && !empty($files2)){
					$files = $files2;				
				}
				
				if(!empty($files)){
					$files = array_unique($files);
					}
				
				if(!$files){
					alert_message("Search yields no results");
					return;
				}
				
				if($rcat == 'unrated'){
					$files = array_intersect($files, get_unrated());
					return $files;
				}
				
				if($rcat == 'rated'){
					$files = array_intersect($files, get_rated());
					return $files;
				}
				
				return $files;
				
	//		}
			
		}
		
		function giveRating($value1){
		
			//if(isset($_POST['submission'])){
				
				$value = $value1;
				$where = 'file_liste';
				$what = 'filename';
				$files = null;
								
				$sQuery0 = "WHERE rating >= $value";
				
				$files = db_select($where, $what, $sQuery0, $what);
				
				if(!empty($files)){
					$files = array_unique($files);
					}
				
				if(!$files){
					alert_message("Search yields no results");
					return FALSE;
				}
								
				return $files;
				
			//}
		}
		
		function giveBoth($search1, $value1, $rcat){
			
			$search = $search1;
			$value = $value1;
		
			$filestmp1 = giveSearch($search, $rcat);
			$filestmp2 = giveRating($value);
			
			if(empty($filestmp1) && !empty($filestmp2)){
				return $filestmp2;
			}
			
			if(!empty($filestmp1) && empty($filestmp2)){
				return $filestmp2;
			}
			
			$filestmp3 = array_intersect($filestmp1, $filestmp2);
			
			$files = array_unique($filestmp3);
			
			if(!$files){
					alert_message("Search yields no results");
					return FALSE;
			}	
			return $files;
		}
		
		function get_unrated()
		{
			$files = db_select('file_liste', 'filename', 'WHERE rating IS NULL', 'filename');
			return $files;
		}
		
		function get_rated()
		{
			$files = db_select('file_liste', 'filename', 'WHERE rating IS NOT NULL', 'filename');
			return $files;
		}
		
		// testfunksjon for oppdatering av tagsliste etter søk / ikke i bruk
		function findTags($filelist1){
		
		$filelist = $filelist1;
		
		$finalTags = array();
		
			foreach($filelist as $rr){
				array_to_string($rr);
				$where = 'tag';
				$what = 'tags';
				$sQuery3 = "INNER JOIN file_list ON tag.fileid = file_liste.fileid WHERE file_liste.filename LIKE '$rr%'";
				$currentTags = db_select($where, $what, $sQuery3, $what);
				$finalTags[] = $currentTags;
			}	
			
			$finalTags = array_unique($finalTags);
			$finalTagsStr = array_to_string($finalTags);
			return $finalTagsStr;
		
		}
		
		// Testfunksjoner / ikke i bruk
		
		function get_search_error(){
			return $searcherror;
		}
		
		function set_search_error($value){
			return $searcherror = $value;;
		}
		
?>
