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
	
	//
	
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
	
	if(isset($_POST['ratingcategory'])){
		$rcat = $_POST['ratingcategory'];
		if($rcat == 'unrated'){
			$files = get_unrated();
		}
		if($rcat == 'rated'){
			$files = get_rated();
		}
		if($rcat == 'all'){
			$files = get_File_List($big, $images);
		}
	}
	
	if (!empty($_GET['tag'])){
		$files = get_img_by_tag($_GET['tag']);
		$gallery = VisBilder($files);
	}
	else{
		$gallery = VisBilder("");
	}
	
	//test search function
	
		if (!empty($_POST['ratinginput']) && !empty($_POST['search'])){
			$files = giveBoth($_POST['search'],$_POST['ratinginput'],$_POST['ratingcategory']);
			$gallery = VisBilder($files);
		}
	
		if (!empty($_POST['search']) && empty($_POST['ratinginput'])){
			$files = giveSearch($_POST['search'],$_POST['ratingcategory']);
			$gallery = VisBilder($files);
		}
		
		if (!empty($_POST['ratinginput']) && empty($_POST['search'])){
			$files = giveRating($_POST['ratinginput']);
			$gallery = VisBilder($files);
		}	

if(!$failed){		
	if((!empty($_POST['submission'])) && !(empty($_POST['ratingcategory']) && empty($_POST['search']) && empty($_POST['ratinginput']))){
		if(!($_POST['ratingcategory']=='all' && (empty($_POST['search']) && empty($_POST['ratinginput'])))){	
	 
		$cpam =	'<div id ="parameters"><h3> CURRENT SEARCH: <i>';
	
		if(!empty($_POST['ratingcategory'])){
			$cpam .= '(CATEGORY: ';
			if($_POST['ratingcategory']=='unrated' && !empty($_POST['ratinginput'])){
				$cpam .= 'all")';
			}
			else{
				$cpam .= $_POST['ratingcategory'].')';
			}
		}	
		
		if(!empty($_POST['ratinginput'])){
			$cpam .=' & ';
			$cpam .= '(RATING: >= ';
			$cpam .= $_POST['ratinginput'].')';
		}
		
		if(!empty($_POST['search'])){
			$cpam .= ' & ';
			$cpam .= '(COMMENT/TAG: "';
			$cpam .= $_POST['search'].'")';
		}
		
		$cpam .= '</i></h3></div>';
		
		}
 	}
}

	$failed = FALSE;
		
	$main = preg_replace('/#parameters#/', $cpam, $main);
	
	$main = preg_replace('/#gallery#/', $gallery, $main);

    $tags_str = gen_tags($files);
    $main = preg_replace('/#tags#/', $tags_str, $main);

    echo($main);	//displays the contents of the file main.html
    if (!empty($message)) alert_message($message);	// pop-up message 
?>
