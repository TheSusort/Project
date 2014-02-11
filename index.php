<?php
    include_once("Metadata.php");
    include_once("mysql.php");
    include_once("funksjoner.php");
    db_connnect();
    $main = file_get_contents('main.html');

    if ($_FILES != null){
        save_file();
    }

    if ($GLOBALS['db_is_connected'])
    {
        check_img_modification('Bilder');
    }
    $gallery = VisBilder();
    $tags = get_tags();
    $main = preg_replace('/#gallery#/', $gallery, $main);
    $tags_str = array_to_string($tags);
    $main = preg_replace('/#tags#/', $tags_str, $main);
    echo($main);	//displays the contents of the file main.html
?>
