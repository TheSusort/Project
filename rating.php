<?php
	// include_once('imgXMP.php');
	include_once("mysql.php");
	db_connnect();
	
	if(!empty($_POST['rate'])){
		$rate = $_POST['rate'];
		$fileName = substr($_POST['name'], 7);
		$query = "UPDATE file_liste SET rating='$rate' WHERE filename='$fileName'";
		$result = $db->query($query);
		// set_Rating($rate);
	}
	if(!empty($_POST['name']) && empty($_POST['rate'])){
		$fileName = substr($_POST['name'], 7);
		$img = db_select('file_liste', 'rating', 'WHERE filename = \''.$fileName.'\' ', 'rating');
		echo $img[0];
	}
?>