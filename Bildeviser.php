<?php
	$images; # Location of small versions
	$big; # Location of big versions (assumed to be a subdir of above)
	$cols; # Number of columns to display

	function VisBilder()
	{
		$images = "Bilder/";
		$big    = "";
		$cols   = 3;
		
		if ($handle = opendir($images)) {
		   while (false !== ($file = readdir($handle))) {
			   if ($file != "." && $file != ".." && $file != rtrim($big,"/")) {
				   $files[] = $file;
			   }
		   }
		   closedir($handle);
		}

		$colCtr = 0;

		echo '<table width="100%" cellspacing="3"><tr>';

		foreach($files as $file)
		{
		  if($colCtr %$cols == 0)
			echo '</tr><tr><td colspan="'.$cols.'"><hr /></td></tr><tr>';
			echo '<td align="center"><a href="' . $images . $big . $file . '"><img src="' . $images . $file . ' "width="200px" length="auto" /></a></td>';
			$colCtr++;
		}

		echo '</table>' . "\r\n";
	}
?>