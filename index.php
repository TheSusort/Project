<?php
    include_once("Metadata.php");
    include_once("mysql.php");
    include_once("funksjoner.php");
    db_connnect();
    $main = file_get_contents('main.html');
    $message = "";

    if ($_FILES != null){
        $message = "$message".save_file();
    }

    if ($GLOBALS['db_is_connected'])
    {
        $message = $message.check_img_modification('Bilder/');
    }

    $gallery = VisBilder();
    $main = preg_replace('/#gallery#/', $gallery, $main);

    $tags_str = gen_tags();
    $main = preg_replace('/#tags#/', $tags_str, $main);

    echo($main);	//displays the contents of the file main.html
    if (!empty($message)) alert_message($message);
?>
