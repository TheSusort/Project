<?php

    include_once("thumbnail.php");

	$mappeForBilder = "Bilder/";
	
	if (count($_FILES["bildefil"]["tmp_name"]))
	{
		for ($i = 0; count($_FILES["bildefil"]["tmp_name"]) > $i; $i++)
		{
			//sjekker om filen eksisterer fra f�r av.
			if (file_exists($mappeForBilder . $_FILES["bildefil"]["tmp_name"][$i]))
			{
				echo $_FILES["bildefil"]["tmp_name"] . " har samme navn som en annen fil.";
			}
			// Flytter fil til mappe.
			elseif (!move_uploaded_file($_FILES["bildefil"]["tmp_name"][$i], $mappeForBilder . $_FILES["bildefil"]["name"][$i]))
			{
				echo "Alt gikk galt.";
			}
			echo "Upload: " . $_FILES["bildefil"]["name"][$i] . "<br>";
			echo "Type: " . $_FILES["bildefil"]["type"][$i] . "<br>";
			echo "Size: " . ($_FILES["bildefil"]["size"][$i] / 1024) . " kB<br>";
			echo "Temp file: " . $_FILES["bildefil"]["tmp_name"][$i] . "<br>";
            
            //lager thumbnail av bilde
            createThumbs($_FILES["bildefil"]["name"][$i], $mappeForBilder, "Bilder/thumbs/", 200);
		}
	}
	//G� tilbake til index etter 3s
	echo '<meta http-equiv="refresh" content="3;URL=index.php" /> '
?>