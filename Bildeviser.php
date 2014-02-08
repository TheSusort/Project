<?php
	$images = "Bilder/";                # Location of small versions
	$big    = "";                       # Location of big versions (assumed to be a subdir of above)
	$cols   = 3;                        # Number of columns to display
    $files  = get_img_list("Bilder");   # List of the files from disk

	function VisBilder()
	{
		global $images, $big, $cols, $files;
        $colCtr = 0;
//        $files = get_File_List($big, $images);

        check_for_new_img('Bilder');
        check_for_del_img('Bilder');

		echo '<table width="100%" cellspacing="3"><tr>';

		foreach($files as $file)
		{
		  if($colCtr %$cols == 0)
			echo '
			        </tr>
			        <tr>
			            <td colspan="'.$cols.'">
			                <hr />
			            </td>
			        </tr>
			        <tr>';
			echo '
			        <td id="bilde" align="center">
			            <a href="' . $images . $big . $file . '">
			                <img src="' . $images . $file . ' "width="200px" length="auto" />
			            </a>
			        </td>';
			$colCtr++;
		}

		echo '</table>' . "\r\n";
	}

// return array of files names from dir.
    function get_File_List($big, $images)
    {
        if ($handle = opendir($images)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && $file != rtrim($big,"/")) {
                    $files[] = $file;
                }
            }
            closedir($handle);
        }
        return $files;
    }

// return array of .jpg and .bmp files names from dir.
    function get_img_list($dir)
    {
        $f = scandir($dir);

        foreach ($f as $file){
            if(preg_match("/\.jp.?g$|\.png$/i", $file)){
                $files[] = $file;
            }
        }
        return $files;
    }

// check images which are not registered in database and add them to the db
    function check_for_new_img($dir)
    {
        $files_on_disc  = $GLOBALS['files'];
        $files_in_db    = db_select('file_liste', 'filename');
        $result = array_diff($files_on_disc, $files_in_db);
        foreach($result as $rslt)
        {
            db_insert('file_liste', 'filename', $rslt);
        }
    }

// check images which are deleted from disc and delete them from the db.
    function check_for_del_img($dir)
{
    $files_on_disc  = $GLOBALS['files'];
    $files_in_db    = db_select('file_liste', 'filename');
    $result = array_diff($files_in_db, $files_on_disc);
    foreach($result as $rslt)
    {
        db_delete('file_liste', 'filename', $rslt);
    }
}

?>