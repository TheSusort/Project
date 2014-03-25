<?php
	include_once('imgEXIF.php');
	include_once("mysql.php");
	db_connnect();
	
	//---- set Rating----- IKKE VIRKE MED EXIF!!!
	if(!empty($_POST['rate']) && !empty($_POST['name'])){
		$result = setRateDB($_POST['rate'], $_POST['name']);
		// if ($result){
			// setRateEXIF($_POST['rate'], $_POST['name']);
		// }
	}
	
	//---- set Comment-----
	if(!empty($_POST['comment']) && !empty($_POST['name'])){
		$result = setCommentDB($_POST['comment'], $_POST['name']);
		if ($result){
			setCommentEXIF($_POST['comment'], $_POST['name']);
		}
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
		$image 		= new img($_POST['name']);
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
		$image = new Imagick();
		$image->readImage(__DIR__ . DIRECTORY_SEPARATOR .$url);
		$rate = $image->getImageProperty('xmp:Rating');
		$image->clear(); 
		$image->destroy();
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
		$image = new img($_POST['name']);
		$image->set_to_Marker('XP_KEYWORDS', $tegs);
	}
	
	function setCommentEXIF($comment, $url){
		$image = new img($_POST['name']);
		$image->set_to_Marker('XP_COMMENT', $comment);
	}
	
	function setRateEXIF($rate, $url){
		$image = new Imagick();
		$image->readImage(__DIR__ . DIRECTORY_SEPARATOR .$url);
		$image->setImageProperty('xmp:Rating', $rate);
		// $image->commentImage("Hello World!");
		
		print_r($image->getImageProperties("*"));
		clearstatcache(dirname(__DIR__ . DIRECTORY_SEPARATOR .$url));
		unlink(__DIR__ . DIRECTORY_SEPARATOR .$url);
		$image->writeImage(__DIR__ . DIRECTORY_SEPARATOR .$url);
		
		$image->clear(); 
		$image->destroy();
		
		$image1 = new Imagick();
		$image1->readImage(__DIR__ . DIRECTORY_SEPARATOR .$url);
		
		print_r($image1->getImageProperties());
		$image1->clear(); 
		$image1->destroy();
	}
	// echo(getRateImg("Bilder/output.jpg"));
	// setRateImg('5', "Bilder/output.jpg");
	// echo('aaa'.getRateImg("Bilder/output.jpg"));
?>