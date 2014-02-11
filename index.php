<?php
		include_once("Metadata.php");
<<<<<<< HEAD
		include_once("Bildeviser.php");
        include_once("mysql.php")
	?>
    
    <div id="containermain">
        
        <div id="title">
				<h1>Web Photo Gallery</h1>
                <?php db_connnect(); ?>
        </div>
        
        <div id="containerleft">
        
        <div id="upload">
				<form action="Opplasting.php" method="post" enctype="multipart/form-data">
					<input name="bildefil[]" id="bildefil" type="file" multiple=""><br>
					<input type="submit" name="submit" value="Submit">
				</form>
        </div>
        
        <div id="directory">
				<p>This is the directory script</p>
        </div>
            
        <div id="exif">
				<p>This is the EXIF script</p>
        </div>
            
        </div>
            
        <div id="gallery">
				<?php					
					LastInnMetadata();
					VisBilder();
				?> 
        </div>
=======
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
>>>>>>> origin/tihan-code
		
	?>
