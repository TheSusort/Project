<?php
	// include_once('imgEXIF.php');
	include_once('image.php');
	include_once("mysql.php");
	db_connnect();
	
	//---- set Rating----- 
	if(!empty($_POST['rate']) && !empty($_POST['name'])){
		$result = set_Rating_DB($_POST['name'], $_POST['rate']);
		if ($result){
			set_Rating_exif($_POST['name'], $_POST['rate']);
		}
	}
	
	//---- set Comment-----
	if(!empty($_POST['comment']) && !empty($_POST['name'])){
		$result = set_Comment_DB($_POST['name'], $_POST['comment']);
		if ($result){
			set_Comment_exif($_POST['name'], $_POST['comment']);
		}
	}
	
	//---- set Tags-----
	if(!empty($_POST['tags']) && !empty($_POST['name'])){
		$result = setTagDB($_POST['name'], $_POST['tags']);
		if ($result){
			setTagEXIF($_POST['name'], $_POST['tags']);
		}
	}

	//---- get Rating, Comment and Tags
	if(!empty($_POST['name']) && (empty($_POST['rate']) && empty($_POST['comment']) && empty($_POST['tag']))){
		//---- get Rating ----
		$rate  		= 'rate: ' . get_Rating_DB($_POST['name']);
		$comment 	= 'comment: ' . get_Comment_DB($_POST['name']);
		$tags		= get_Tags_DB($_POST['name']);
		$tagsStr 	= 'tags: '.implode(",", $tags);
		$imgData 	= $rate."#".$comment."#".$tagsStr.";";
		echo($imgData);
	}
	
	/* function getRateDB($url){
		// $fileName = substr($url, 7);
		// $img = db_select('file_liste', 'rating', 'WHERE filename = \''.$fileName.'\' ', 'rating');
		// return $img[0];
	// }
	
	// function getCommentDB($url){
		// $fileName = substr($url, 7);
		// $img = db_select('file_liste', 'commentary', 'WHERE filename = \''.$fileName.'\' ', 'commentary');
		// return $img[0];
	// }
	
	// function getTagsDB($url){
		// $fileName = substr($url, 7);
		// $img = db_select('tag', 'tags', 'INNER JOIN file_liste ON tag.fileid = file_liste.fileid WHERE file_liste.filename=\''.$fileName.'\' ORDER BY tags', 'tags');
		// if (empty($img)){
			// return array("");
		// }
		// return $img;
	// }
	
	// function getRateEXIF($url){
		// $rate = get_Rating($url);
		// return $rate;
	// }
	
	// function setRateDB($rate, $url){
		// global $db;
		// $fileName = substr($url, 7);
		// $query = "UPDATE file_liste SET rating='$rate' WHERE filename='$fileName'";
		// $result = $db->query($query);
		// return $result;
	// }
	
	// function setTagEXIF($tegs, $url){
		// if (checkImg($url)){
			// add_KeyWord($tegs, $url);
		// }
	// }
	
	// function setCommentEXIF($comment, $url){
		
	// }
	
	// function setRateEXIF($rate, $url){
		// if (checkImg($url)){
			// set_Rating($rate, $url);
		// }
	// }
	
	// function checkImg($imgName){
		// if(preg_match("/\.jp.?g$|\.ti.?f$/i", $imgName))
		// {
			// return TRUE;
        // }
		// return FALSE;
	// }*/
	
	// set_Comment_exif('Bilder/svard.jpg', 'Fjord svard');
	// get_Comment_exif('Bilder/20120617_170700.jpg');
?>