<?php
	// include_once('imgXMP.php');
	include_once("mysql.php");
	db_connnect();
	
	//---- set Rating-----
	if(!empty($_POST['rate']) && !empty($_POST['name'])){
		setRateDB($_POST['rate'], $_POST['name']);
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
	
	function getRateImg($url){
		$image = new Imagick();
		$image->readImage(__DIR__ . DIRECTORY_SEPARATOR .$url);
		$rate = $image->getImageProperty('GF:rate');
		$image->clear(); 
		$image->destroy();
		return $rate;
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
	
	
	function setTag($url){
		
	}
	
	function setComment($url){
	
	}
	
	function setRateDB($rate, $url){
		global $db;
		$fileName = substr($url, 7);
		$query = "UPDATE file_liste SET rating='$rate' WHERE filename='$fileName'";
		$result = $db->query($query);
		// if ($result){
			// setRateImg($rate, $url);
		// }
	}
	
	function setRateImg($rate, $url){
		$image = new Imagick();
		$image->readImage(__DIR__ . DIRECTORY_SEPARATOR .$url);
		$image->setImageProperty('arate', "$rate");
		// $image->commentImage("Hello World!");
		
		print_r($image->getImageProperties("*"));
		
		$image->writeImage(__DIR__ . DIRECTORY_SEPARATOR .$url);
		
		$image->clear(); 
		$image->destroy();
		
		$image1 = new Imagick();
		$image1->readImage(__DIR__ . DIRECTORY_SEPARATOR .$url);
		
		print_r($image1->getImageProperties());
		$image1->clear(); 
		$image1->destroy();
	}
	// setRateImg('5', "Bilder/output.jpg");
	// echo('aaa'.getRateImg("Bilder/output.jpg"));
?>