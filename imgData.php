<?php
	// include_once('imgXMP.php');
	include_once("mysql.php");
	db_connnect();
	
	//---- set Rating-----
	if(!empty($_POST['rate']) && !empty($_POST['name'])){
		setRate($_POST['rate'], $_POST['name']);
	}

	//---- get Rating, Comment and Tags
	if(!empty($_POST['name']) && (empty($_POST['rate']) && empty($_POST['comment']) && empty($_POST['tag']))){
		//---- get Rating ----
		$rate  		= 'rate: ' . getRate($_POST['name']);
		$comment 	= 'comment: ' . getComment($_POST['name']);
		$tags		= getTags($_POST['name']);
		$tagsStr 	= 'tags: '.implode(",", $tags);
		$imgData 	= $rate."#".$comment."#".$tagsStr.";";

		echo($imgData);
	}
	
	function getRate($url){
		$fileName = substr($url, 7);
		$img = db_select('file_liste', 'rating', 'WHERE filename = \''.$fileName.'\' ', 'rating');
		return $img[0];
	}
	
	function getComment($url){
		$fileName = substr($url, 7);
		$img = db_select('file_liste', 'commentary', 'WHERE filename = \''.$fileName.'\' ', 'commentary');
		return $img[0];
	}
	
	function getTags($url){
		$fileName = substr($url, 7);
		$img = db_select('tag', 'tags', 'INNER JOIN file_liste ON tag.fileid = file_liste.fileid WHERE file_liste.filename=\''.$fileName.'\' ORDER BY tags', 'tags');
		if (empty($img)){
			return array("");
		}
		return $img;
	}
	
	
	function setTag($url){
		
	}
	
	function setComment($url){
	
	}
	
	function setRate($rate, $url){
		global $db;
		$fileName = substr($url, 7);
		$query = "UPDATE file_liste SET rating='$rate' WHERE filename='$fileName'";
		$result = $db->query($query);
	}
	
?>