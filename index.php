<?php
    Echo "<link rel='shortcut icon' href='img/favicon.ico'>";
    include_once("index.php");//test
	include_once("Metadata.php");
    include_once("mysql.php");
    include_once("funksjoner.php");
	
    db_connnect();
    $main = file_get_contents('main.html');
    $message = "";
	
	// new variables test
	
	$cpam = "";
	global $failed;
	$failed	= FALSE;
	
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
	
//	if(isset($_POST['ratingcategory'])){
//		$rcat = $_POST['ratingcategory'];
//		if($rcat == 'unrated'){
//			$files = get_unrated();
//		}
//		if($rcat == 'rated'){
//			$files = get_rated();
//		}
//		if($rcat == 'all'){
//			$files = get_File_List($big, $images);
//		}
//	}
	
	if (!empty($_GET['tag'])){
		$files = get_img_by_tag($_GET['tag']);
		$gallery = VisBilder($files);
	}
	else{
		$gallery = VisBilder("");
	}
	
	//test search function
	
	$ratinginput = "";
	$search = "";
	$ratingcategory = "";
	$submission = "";
	
	if(!empty($_GET['ratinginput'])){$ratinginput = $_GET['ratinginput'];}
	if(!empty($_GET['search'])){$search = $_GET['search'];}
	if(!empty($_GET['ratingcategory'])){$ratingcategory = $_GET['ratingcategory'];}
	if(!empty($_GET['submission'])){$submission = $_GET['submission'];}
	
	if(!empty($submission)){
		$files = get_search_list($ratinginput, $search, $ratingcategory);
		$gallery = VisBilder($files);
	}
	
	$cpam = get_search_parameter_display($failed, $ratingcategory, $search, $ratinginput, $submission);
	
	print_r($failed);
	if($failed){
	
	 echo'<script type="text/javascript">
            window.location.assign("http://localhost/Project/index.php?ratingcategory=all&search=&submission=Search");
     </script>';
	 
	}
	
	$failed = FALSE;
		
	$main = preg_replace('/#parameters#/', $cpam, $main);	
	$main = preg_replace('/#gallery#/', $gallery, $main);
    $tags_str = gen_tags($files);
    $main = preg_replace('/#tags#/', $tags_str, $main);

    echo($main);	//displays the contents of the file main.html
    if (!empty($message)) alert_message($message);	// pop-up message 
?>
