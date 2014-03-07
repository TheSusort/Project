<?php
    Echo "<link rel='shortcut icon' href='img/favicon.ico'>";
    include_once("index.php");//test
	include_once("Metadata.php");
    include_once("mysql.php");
    include_once("funksjoner.php");
	
    db_connnect();
    $main = file_get_contents('main.html');
    $message = "";

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
	
	
	
	if (!empty($_GET['tag'])){
		$files = get_img_by_tag($_GET['tag']);
		$gallery = VisBilder($files);
	}
	else{
		$gallery = VisBilder("");
	}
	
	//test search function
	
	if (!empty($_POST['search']) && !empty($_POST['searchw'])){
		$files = giveSearch($_POST['search'], $_POST['searchw']);
		$gallery = VisBilder($files);
	}
	
	//test
	
	
	$main = preg_replace('/#gallery#/', $gallery, $main);

    $tags_str = gen_tags();
    $main = preg_replace('/#tags#/', $tags_str, $main);

    echo($main);	//displays the contents of the file main.html
    if (!empty($message)) alert_message($message);
?>
