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
            check_for_new_img('Bilder');
            check_for_del_img('Bilder');
        }
		$gallery = VisBilder();
		
		$main = preg_replace('/#gallery#/', $gallery, $main);

		echo($main);	//displays the contents of the file main.html
		
	?>
