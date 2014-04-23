<?php
	include_once('imgEXIF.php');
	include_once("mysql.php");
	db_connnect();
	
	//---- set Rating----- IKKE VIRKE MED EXIF!!!
	if(!empty($_POST['rate']) && !empty($_POST['name'])){
		$result = setRateDB($_POST['rate'], $_POST['name']);
		if ($result){
			setRateEXIF($_POST['rate'], $_POST['name']);
		}
	}
	
	//---- set Comment-----
	if(!empty($_POST['comment']) && !empty($_POST['name'])){
		$result = setCommentDB($_POST['comment'], $_POST['name']);
		// if ($result){
			// setCommentEXIF($_POST['comment'], $_POST['name']);
		// }
	}
	
	//---- set Tags-----
	if(!empty($_POST['tags']) && !empty($_POST['name'])){
		$result = setTagDB($_POST['tags'], $_POST['name']);
		if ($result){
			setTagEXIF($_POST['tags'], $_POST['name']);
		}
	}

	//---- get Rating, Comment and Tags
	if(!empty($_POST['name']) && (empty($_POST['rate']) && empty($_POST['comment']) && empty($_POST['tag']))){
		//---- get Rating ----
		$rate  		= 'rate: ' . getRateDB($_POST['name']);
		$comment 	= 'comment: ' . getCommentDB($_POST['name']);
		$tags		= getTagsDB($_POST['name']);
		$tagsStr 	= 'tags: '.implode(",", $tags);
		$imgData 	= $rate."#".$comment."#".$tagsStr.";";
		echo($imgData);
	}
	
	function getRateDB($url){
		$fileName = substr($url, 7);
		$img = db_select('file_liste', 'rating', 'WHERE filename = \''.$fileName.'\' ', 'rating');
		return $img[0];
	}
	
	function getCommentDB($url){
		$fileName = substr($url, 7);
		$img = db_select('file_liste', 'commentary', 'WHERE filename = \''.$fileName.'\' ', 'commentary');
		return $img[0];
	}
	
	function getTagsDB($url){
		$fileName = substr($url, 7);
		$img = db_select('tag', 'tags', 'INNER JOIN file_liste ON tag.fileid = file_liste.fileid WHERE file_liste.filename=\''.$fileName.'\' ORDER BY tags', 'tags');
		if (empty($img)){
			return array("");
		}
		return $img;
	}
	
	function getRateEXIF($url){
		$rate = get_Rating($url);
		return $rate;
	}
	
	function setRateDB($rate, $url){
		global $db;
		$fileName = substr($url, 7);
		$query = "UPDATE file_liste SET rating='$rate' WHERE filename='$fileName'";
		$result = $db->query($query);
		return $result;
	}
	
	function setTagEXIF($tegs, $url){
		if (checkImg($url)){
			add_KeyWord($tegs, $url);
		}
	}
	
	function setCommentEXIF($comment, $url){
		
	}
	
	function setRateEXIF($rate, $url){
		if (checkImg($url)){
			set_Rating($rate, $url);
		}
	}
	
	function checkImg($imgName){
		if(preg_match("/\.jp.?g$|\.ti.?f$/i", $imgName))
		{
			return TRUE;
        }
		return FALSE;
	}
?>