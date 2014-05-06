<?php
include_once('image.php');

include_once("mysql.php");

$thumbs = "Bilder/thumbs/";         # Location of small versions
$big    = "Bilder/";                # Location of big versions (assumed to be a subdir of above)
$cols   = 4;                        # Number of columns to display
$files  = null;                     # List of the files from disk

// Transformation array to string
    function array_to_string($array)
    {
		if(!is_array($array)){
			$array = array($array);
		}
        reset($array);
        $str = current($array);
        while (next($array) <> null)
        {
            $str = "$str, ". current($array);
        }
        return $str;
    }

// Show message in the pop-up window
    function alert_message($message)
    {
        echo("
                <script type=\"text/javascript\">
                    alert(\"$message\");
                </script>
            ");
    }
// Show message in the console
	function consol_message($message)
    {
        echo("
                <script type=\"text/javascript\">
                    console.log((\"$message\");
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
        global $files, $big, $thumbs;
        $result = null;
        $files_new_str = 'Downloaded manually:';
        if ($files_on_disc <> null & $files_in_db == null){
            $result = $files_on_disc;
        }elseif ($files_on_disc <> null & $files_in_db <> null){
            $result = array_diff($files_on_disc, $files_in_db);
        }else{
            return FALSE;
        }
        foreach($result as $rslt){
            // if (db_insert('file_liste', 'filename', $rslt))
            // {
			$files_new_str = $files_new_str.'\n\t'.$rslt;
			$ratateStatus = autoRotateImage($big . $rslt);
			if (!$ratateStatus){
				createThumbs($big.$rslt, $thumbs.$rslt, 150);	//lager thumbnail av bilde
			}
			if ($GLOBALS['db_is_connected']){
				$data = get_EXIF($big.$rslt);
				$query = "INSERT INTO file_liste 
										(filename, 
										commentary, 
										rating, 
										date_of_addition,
										date_of_shooting) 
								VALUES 	('$rslt',
										'".$data['commentary']."',
										'".$data['rating']."',
										'".$data['date_of_addition']."',
										'".$data['date_of_shooting']."');";
				if (db_insert_query($query)){
					if(!empty($data['tag'])){
						foreach($data['tag'] as $tag){
							$queryTag = "INSERT INTO tag 
											(fileid,
											tags)
										VALUES
											((SELECT fileid FROM file_liste WHERE filename='$rslt'),
											'$tag');";
							db_insert_query($queryTag);
						}
					}
				}
			}
            // }
        }
        if (!empty($result)){
            $files = db_select('file_liste', 'filename', 'ORDER BY rating', 'filename');
			return $files_new_str;
        }
    }

// check images which are deleted from disc and delete them from the db.
    function check_for_del_img($files_on_disc, $files_in_db)
    {
        global $files, $thumbs;
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
				if (file_exists($thumbs.$rslt))
				{
					unlink($thumbs.$rslt);
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
		global $thumbs, $big;
		$i=0;
		// $add = FALSE;
		$not_add = FALSE;
		// $files_add = '\tSaved on server:\n';
		$files_not_add = '\tFile\'s not upload:\n';
		$file_name = $_FILES["bildefil"]["name"];
		$file_tmp_name = $_FILES["bildefil"]["tmp_name"];
		
		if (!empty($file_tmp_name[0])){
			foreach ($file_tmp_name as $file){
				if (check_img($file_name[$i])){
					if (file_exists($big.$file_name[$i])){
						$files_not_add = $files_not_add.'\n '.$file_name[$i].' File name already exist';
						$not_add = TRUE;
					}elseif (!move_uploaded_file($file, $big.$file_name[$i])){
						$files_not_add = $files_not_add.'\n '.$file_name[$i].' Something went wrong. :-(';
						$not_add = TRUE;
					}elseif (check_img($file_name[$i])){
						$ratateStatus = autoRotateImage($big . $file_name[$i]);
						if (!$ratateStatus){
							createThumbs($big.$file_name[$i], $thumbs.$file_name[$i], 150);	//lager thumbnail av bilde
						}
						if ($GLOBALS['db_is_connected']){
							$data = get_EXIF($big.$file_name[$i]);
							$query = "INSERT INTO file_liste 
													(filename, 
													commentary, 
													rating, 
													date_of_addition,
													date_of_shooting) 
											VALUES 	('$file_name[$i]',
													'".$data['commentary']."',
													'".$data['rating']."',
													'".$data['date_of_addition']."',
													'".$data['date_of_shooting']."');";
							if (db_insert_query($query)){
								if(!empty($data['tag'])){
									foreach($data['tag'] as $tag){
										$queryTag = "INSERT INTO tag 
														(fileid,
														tags)
													VALUES
														((SELECT fileid FROM file_liste WHERE filename='$file_name[$i]'),
														'$tag');";
										db_insert_query($queryTag);
									}
								}
							}
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
        global $thumbs, $big, $cols, $files;
        $colCtr = 0;
        $gallery = "";
        if (empty($files))
		{	
			// alert_message("get list from disc");
			// $files = get_img_list($big);
		}
		$gallery =  '
			<table width="100%" cellspacing="3">
				<tr>';
		
		if(!empty($files)){
			foreach($files as $file)
			{
				if (null != $file)
				{
					//lager thumbs hvis det ikke er laget thumbs avbildet tidligere.
					$thumb = $thumbs.$file;
					if(!file_exists($thumb))
					{
						createThumbs($big.$file, $thumbs.$file, 150);
					}
					if($colCtr %$cols == 0)
					{
						$gallery = $gallery. '
							</tr>
							<tr>
								<td colspan="'.$cols.'">
									<hr />
								</td>
							</tr>
							<tr>';
					}
					$gallery = $gallery. '
								<td id="bilde" align="center" 
										onDblClick = openFulskr("'.$big.$file.'")>
									<img src="' . $thumbs . $file . '" />
								</td>';
								// viuwEXIF("'.get_EXIF($big.$file).'") 
								// viuwEXIF("'.$big.$file.'")
					$colCtr++;
				}
			}
		}else{
			$gallery = $gallery. '
							</tr>
							<tr>
								<td colspan="'.$cols.'">
									<hr />
								</td>
							</tr>
							<tr>
							<td align="center" >
								<img src="img/empty.jpg" />
							</td>';
		}
		$gallery = $gallery. '</table>' . "\r\n";
		
        return $gallery;
    }

// Generate HTML cod for tags list
	function gen_tags($filesList)
	{	
		$searchData ='';
		if (isset($_GET['search'])){
			$searchData .= "&search=".$_GET['search'];
			$searchData .= "&ratinginput=".$_GET['ratinginput'];
		}
		
		$order ='';
		if (isset($_GET['SortingCategory'])){
			$order .= "&SortingCategory=".$_GET['SortingCategory'];
		}
		
		$tagChoosed ='';
		if(isset($_GET['tag'])){
			$tagChoosed = $_GET['tag'];
		}
		
		$tags_str = "<ul class=\"nav\">\n\r";
		$tags = get_tags($filesList);
		if(!empty($tags)) asort($tags);
		
		if(empty($tagChoosed)){
			$tags_str = $tags_str."<li><a href=\"index.php?$order\" style='background: #D9DCDC no-repeat;
														color: #141818;
														padding: 7px 15px 7px 30px;'><span>All</span></a></li>\r\n";
		}else{
			$tags_str = $tags_str."<li><a href=\"index.php?$order\"><span>All</span></a></li>\r\n";
		}
		if (!empty($tags)){
			foreach($tags as $tag)
			{
				if ($tag == $tagChoosed){
					$tags_str = $tags_str."<li><a href=\"?tag=$tag$searchData$order\" style='background: #D9DCDC no-repeat;
														color: #141818;
														padding: 7px 15px 7px 30px;'>
														<span>".$tag."</span></a></li>\r\n";
				}else{
					$tags_str = $tags_str."<li><a href=\"?tag=$tag$searchData$order\"><span>".$tag."</span></a></li>\r\n";
				}
			}
		}
		$tags_str = $tags_str."</ul>";
		return $tags_str;
	}
	
// Get tags list from db
//	return: tags list [array]
    function get_tags($filelist)
    {
		$finalTags = array();
		
		if(!empty($filelist)){
			foreach($filelist as $file){
				$where = 'tag';
				$what = 'tags';
				$query = "SELECT tags FROM tag 
							INNER JOIN file_liste ON tag.fileid = file_liste.fileid 
							WHERE file_liste.filename = '$file'
							ORDER BY tag.tags" ;
				$currentTags = db_select_query('tags', $query);
				// $currentTags = db_select($where, $what, $sQuery3, $what);
				
				if(!empty($currentTags)){
					//$currentTags = array_filter($currentTags);
					foreach($currentTags as $bb){
						array_push($finalTags, $bb);
					}
				}
			}	
					
			//$sluttTags = array_filter($finalTags);
			$sluttTags = array_unique($finalTags);
			
			
			return $sluttTags;
		}else{
		
		}
	
    //   $tag_list = db_select('tag', 'tags', 'GROUP BY tags', 'tags');
    //  return $tag_list;
	
    }
// get images list by tag
	function get_img_by_tag($tag, $order='')
	{
		// $files = db_select('file_liste', 'filename', 'INNER JOIN tag ON file_liste.fileid = tag.fileid WHERE tag.tags = \''.$tag.'\'', 'filename');
		$queryOrder = '';
		if (!empty($order)){
			if ($order == 'filename'){
				$queryOrder = $order.', ';
			}else{
				$queryOrder = $order.' DESC, ';
			}
		}
		
		$files = db_select_query('filename',"SELECT filename FROM file_liste
								INNER JOIN tag ON file_liste.fileid = tag.fileid 
									WHERE tag.tags = '$tag' 
									ORDER BY $queryOrder file_liste.filename");
		return $files;
	}

// eksperiment get images list by rating
	function get_img_by_rating($rating, $tag = false, $order = ''){
	
		$querySort = '';
		if (!empty($order)){
			if ($order == 'filename'){
				$querySort = $order.', ';
			}else{
				$querySort = $order.' DESC, ';
			}
		};
			
		$query_1 = '';
		$query_2 = '';
		if ($tag){
			$query_1 = 'INNER JOIN tag ON file_liste.fileid = tag.fileid';
			$query_2 = " (tag.tags = '$tag')";
		}
		
		if ($rating == '0'){
			$query = "SELECT filename FROM file_liste
						$query_1
						WHERE file_liste.rating IS NULL or file_liste.rating = '0'
						and $query_2
						ORDER BY $querySort file_liste.filename";
			$files = db_select_query('filename',$query);
		}elseif ($rating == '6'){
			$query = "SELECT filename FROM file_liste
						$query_1
						WHERE file_liste.rating IS NOT NULL and file_liste.rating != '0'
						and $query_2
						ORDER BY $querySort file_liste.filename";
			$files = db_select_query('filename', $query);
		}elseif ($rating == '-1'){
			if(!empty($query_2)){
				$query = "SELECT filename FROM file_liste
							$query_1
							WHERE $query_2
							ORDER BY $querySort file_liste.filename";
			}else{
				$query = "SELECT filename FROM file_liste
							ORDER BY $querySort file_liste.filename";
			}
			$files = db_select_query('filename', $query );
		}else{
			$query = "SELECT filename FROM file_liste
						$query_1
						WHERE file_liste.rating = '$rating'
						$query_2
						ORDER BY $querySort file_liste.filename";
			$files = db_select_query('filename', $query);
		}
		return $files;
	}
	
	
// return array of files names from dir.
    function get_File_List($big, $thumbs)
    {
        if ($handle = opendir($thumbs)) {
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

// return array of files names from db.
    function get_img_list_db($sortering='')
    {
		$querySort = '';
		if (!empty($sortering)){
			if ($sortering == 'file_liste.filename'){
				$querySort = $sortering.', ';
			}else{
				$querySort = $sortering.' DESC, ';
			}
		};
        $query = "SELECT * FROM file_liste 
					ORDER BY $querySort file_liste.filename";
			// echo($query);		
		$files = db_select_query('filename', $query );
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
	


	// Search function
		
		function giveSearch($search1, $rcat){
		
//			if(isset($_POST['submission'])){

				$search = $search1;
				
				if (strpos($search,"'") !== false) {
					consol_message("Invalid character!");
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
				
				error($files);
				
				if($rcat == 'unrated'){
					$files = array_intersect($files, get_unrated());
					error($files);
					return $files;
				}
				
				if($rcat == 'rated'){
					$files = array_intersect($files, get_rated());
					error($files);
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
				
				error($files);
								
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
			
			error($files);
			
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

		function error($files){
		
			if(!$files){
				global $failed;
				consol_message('Search yields no results!');
				$failed = TRUE;
				return FALSE;
			}
		}
		
		function get_search_list($ratinginput, $search, $ratingcategory)
		{
			$files = null;
			
			if (!empty($ratinginput) && !empty($search))
			{
				$files = giveBoth($search,$ratinginput,$ratingcategory);
			}
			
			if (!empty($search) && empty($ratinginput))
			{
				$files = giveSearch($search,$ratingcategory);
			}
		
			if (!empty($ratinginput) && empty($search))
			{
				$files = giveRating($ratinginput);
			}
			
			if (empty($ratinginput) && empty($search) && !empty($ratingcategory))
			{
				if($ratingcategory=='all')
				{
					$files = db_select('file_liste', 'filename', '', 'filename');
				}			
				elseif($ratingcategory=='unrated')
				{
					$files = get_unrated();
				}				
				elseif($ratingcategory=='rated')
				{
					$files = get_rated();
				}
			}	
		
			return $files;
		}
		
		function get_search_parameter_display($failed, $ratingcategory, $search, $ratinginput, $submission){
		
			
			if(!$failed){		
				if((!empty($submission)) && !(empty($ratingcategory) && empty($search) && empty($ratinginput))){
					if(!($ratingcategory=='all' && (empty($search) && empty($ratinginput)))){	
		 
					$cpam =	'<div id ="parameters"><h3> CURRENT SEARCH: <i>';
		
					if(!empty($ratingcategory)){
						$cpam .= '(CATEGORY: ';
						if($ratingcategory=='unrated' && !empty($ratinginput)){
							$cpam .= 'all)';
						}
						else{
							$cpam .= $ratingcategory.')';
						}
					}	
					
					if(!empty($ratinginput)){
						$cpam .=' & ';
						$cpam .= '(RATING: >= ';
						$cpam .= $ratinginput.')';
					}
					
					if(!empty($search)){
						$cpam .= ' & ';
						$cpam .= '(COMMENT/TAG: "';
						$cpam .= $search.'")';
					}
					
					$cpam .= '</i></h3></div>';
					
					return $cpam;
					
					}
				}
			}

		}
		
		function getSerchList($serch, $tag, $rate, $sortering=''){
			$queryTag = '';
			$queryRate = '';
			$querySort = '';
			if (!empty($sortering)){
				if ($sortering == 'filename'){
					$querySort = $sortering.', ';
				}else{
					$querySort = $sortering.' DESC, ';
				}
			};
			
			if ($rate !== null){
				if ($rate == 0){
					$queryRate .= "and (file_liste.rating IS NULL or file_liste.rating='0')";
				}elseif ($rate == 6){
					$queryRate .= "and (file_liste.rating IS NOT NULL and file_liste.rating<>'0') ";
				}elseif ($rate == -1){
					$queryRate .= "";
				}else{
					$queryRate .= "and (file_liste.rating = $rate)";
				}
			}
			$queryS = "SELECT * FROM file_liste 
						LEFT JOIN tag ON file_liste.fileid = tag.fileid
						WHERE (INSTR(file_liste.commentary, '$serch') > 0 or INSTR(tag.tags, '$serch') > 0)
							$queryRate
						ORDER BY $querySort file_liste.filename";
// echo($queryS);
			$resalt = $filesS = db_select_query('filename', $queryS);
			if(!is_array($filesS)){$filesS=array($filesS);}
			
			if (!empty($tag)){
				$queryT = "SELECT * FROM file_liste 
							LEFT JOIN tag ON file_liste.fileid = tag.fileid 
							WHERE INSTR(tag.tags, '$tag') > 0
								$queryRate
							ORDER BY $querySort file_liste.filename";
				$filesT = db_select_query('filename', $queryT);
				if(!is_array($filesT)){$filesT=array($filesT);}
				$resalt = array_intersect($filesS, $filesT);
			}
			return($resalt);
		}
		
		// testfunksjon for oppdatering av tagsliste etter søk / ikke i bruk
//		function findTags($filelist1){
		
//		$filelist = $filelist1;
		
//		$finalTags = array();
		
//			foreach($filelist as $rr){
//				array_to_string($rr);
//				$where = 'tag';
//				$what = 'tags';
//				$sQuery3 = "INNER JOIN file_list ON tag.fileid = file_liste.fileid WHERE file_liste.filename LIKE '$rr%'";
//				$currentTags = db_select($where, $what, $sQuery3, $what);
//				$finalTags[] = $currentTags;
//			}	
			
//			$finalTags = array_unique($finalTags);
//			$finalTagsStr = array_to_string($finalTags);
//			return $finalTagsStr;
		
//		}
		
		// Testfunksjoner / ikke i bruk
		
//		function get_search_error(){
//			return $searcherror;
//		}
		
//		function set_search_error($value){
//			return $searcherror = $value;;
//		}
		

		
?>
