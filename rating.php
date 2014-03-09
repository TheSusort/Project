<?php
	// include_once("funksjoner.php");
	include_once("mysql.php");
	db_connnect();
	if(!empty($_POST['rate'])){
		$rate = $_POST['rate'];
		$fileName = substr($_POST['name'], 7);
		$query = "UPDATE file_liste SET rating='$rate' WHERE filename='$fileName'";
		$result = $db->query($query);
	}
?>