<?php
    Echo "<link rel='shortcut icon' href='img/favicon.ico'>";
    // include_once("index.php");//test
	// include_once("Metadata.php");
    include_once("mysql.php");
    include_once("funksjoner.php");
	
    db_connnect();
    $main = file_get_contents('main.html');
    $message = "";
	
	// new variables test
	
	$cpam = "";
	// global $failed;
	// $failed	= FALSE;
    if ($_FILES != null){
        $message = $message.save_file();
    }
	
    if ($GLOBALS['db_is_connected'])
    {
		$mdf = check_img_modification('Bilder/');
		if ($mdf){
			$message = $message.'\n'.$mdf;
		}
    }
	$sortering = array('','');
	if (isset($_GET['SortingCategory'])){
		$sortering = explode('--', $_GET['SortingCategory']);
		// print_r($sortering);
		if (!isset($sortering[1])){
			$sortering = array('','');
		}
	}
	if(!empty($_GET['search']) & isset($_GET['ratinginput'])){ // if you file the search fild
		$search = $_GET['search'];
		$rate = $_GET['ratinginput'];
		$tag = '';
		if (isset($_GET['tag'])){
			$tag = $_GET['tag'];
		}
		$files = getSerchList($search, $tag, $rate, $sortering[1]);
	}elseif(isset($_GET['ratinginput']) & empty($_GET['search'])){ // if you want to get images by rating
		$rating = $_GET['ratinginput'];
		$tag = '';
		if(isset($_GET['tag'])) $tag = $_GET['tag'];
		$files = get_img_by_rating($rating, $tag, $sortering[1]);
	}else{
		if (!empty($_GET['tag'])){						// if you choose only tag
			$files = get_img_by_tag($_GET['tag'], $sortering[1]);
		}else{											// get all images without mask
			$files = get_img_list_db($sortering[1]);
		}
	}
	if (!is_array($files)){$files = array($files);}
	$files = array_unique($files);
	$gallery = VisBilder($files);

	// if($failed){
	
		 // echo'<script type="text/javascript">
				// window.location.assign("http://localhost/Project/index.php?ratingcategory=all&search=&submission=Search");
		 // </script>';
	 
	// }
	
	// $failed = FALSE;
		
	$main = preg_replace('/#parameters#/', $cpam, $main);	
	$main = preg_replace('/#gallery#/', $gallery, $main);
    $tags_str = gen_tags($files);
    $main = preg_replace('/#tags#/', $tags_str, $main);

    echo($main);	//displays the contents of the file main.html
    if (!empty($message)) alert_message($message);	// pop-up message 
?>
