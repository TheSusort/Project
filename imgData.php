<?php
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
	if(isset($_POST['comment']) && !empty($_POST['name'])){
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
		$rate  		= 'rate: ' . get_Rating_DB($_POST['name']);
		$comment 	= 'comment: ' . get_Comment_DB($_POST['name']);
		$tags		= get_Tags_DB($_POST['name']);
		$tagsStr 	= 'tags: '.implode(",", $tags);
		$imgData 	= $rate."#".$comment."#".$tagsStr.";";
		echo($imgData);
	}
	
	
	
?>