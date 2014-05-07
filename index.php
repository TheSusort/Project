<?php
    Echo "<link rel='shortcut icon' href='img/favicon.ico'>";
    
    include_once("mysql.php");
    include_once("funksjoner.php");
	
    db_connnect();
    $main = file_get_contents('main.html');
    $message = "";
	
	$cpam = "";
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
	
	$files = get_imgs();
	$gallery = VisBilder($files);
		
	$main = preg_replace('/#parameters#/', $cpam, $main);	
	$main = preg_replace('/#gallery#/', $gallery, $main);
    $tags_str = gen_tags($files);
    $main = preg_replace('/#tags#/', $tags_str, $main);

    echo($main);	//displays the contents of the file main.html
    if (!empty($message)) alert_message($message);	// pop-up message 
?>
