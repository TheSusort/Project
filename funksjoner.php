<?php
include_once("thumbnail.php");

$images = "Bilder/thumbs/";         # Location of small versions
$big    = "Bilder/";                # Location of big versions (assumed to be a subdir of above)
$cols   = 3;                        # Number of columns to display
$files  = null;   # List of the files from disk

// Transformation array to string
    function array_to_string($array)
    {
        reset($array);
        $str = current($array);
        while (next($array) <> null)
        {
            $str = "$str, ". current($array);
        }
        return $str;
    }

// Show message in the new window
    function alert_message($message)
    {
        echo("
                <script type=\"text/javascript\">
                    alert(\"$message\");
                </script>
            ");
    }

    function check_img_modification($dir)
    {
        global $files;
        $files  = get_img_list($dir);
        check_for_new_img($files);
        check_for_del_img($files);
    }

// check images which are not registered in database and add them to the db
    function check_for_new_img($files_on_disc)
    {
        $files_in_db    = db_select('file_liste', 'filename');

        if      ($files_on_disc <> null & $files_in_db == null){
            $result = $files_on_disc;
        }elseif ($files_on_disc == null & $files_in_db <> null){
            $result = $files_in_db;
        }elseif ($files_on_disc <> null & $files_in_db <> null){
            $result = array_diff($files_on_disc, $files_in_db);
        }else{
            return;
        }
        foreach($result as $rslt)
        {
			if (db_insert('file_liste', 'filename', $rslt))
            {
                alert_message($rslt.' was downloaded manually. \r\n Inserted into database.');
            }
        }
    }

// check images which are deleted from disc and delete them from the db.
    function check_for_del_img($files_on_disc)
    {
        $files_in_db    = db_select('file_liste', 'filename');

        if ($files_on_disc == null & $files_in_db <> null){
            $result = $files_in_db;
        }elseif($files_on_disc <> null & $files_in_db <> null){
            $result = array_diff($files_in_db, $files_on_disc);
        }else{
            return;
        }
        foreach($result as $rslt)
        {
            unlink($GLOBALS['images'].$rslt);
            db_delete('file_liste', 'filename', $rslt);
        }
    }

    function save_file()
        {
            global $images, $big;
            $i=0;
            $files_add = "\\tLagres paa serveren:\\n";
            $files_not_add = "\\n\\n\\tVar samme navn som en annen fil:\\n";
            if (!empty($_FILES["bildefil"]["tmp_name"][0])){
                foreach ($_FILES["bildefil"]["tmp_name"] as $file)
                {
                    if (file_exists($big.$_FILES["bildefil"]["name"][$i]))
                    {
                        $files_not_add = $files_not_add."\\n ".$_FILES["bildefil"]["name"][$i];
                    }elseif (!move_uploaded_file($file, $big.$_FILES["bildefil"]["name"][$i])){
                        alert_message("Alt gikk galt. :-(");
                    }else{
                        $files_add = $files_add."\\n ".$_FILES["bildefil"]["name"][$i].". \\t\\tSeze: ".round(($_FILES["bildefil"]["size"][$i] / 1024),2)."KB";
                        //lager thumbnail av bilde
                        createThumbs($_FILES["bildefil"]["name"][$i], $big, "Bilder/thumbs/", 200);
                        if ($GLOBALS['db_is_connected'])
                        {
                            if (!db_insert('file_liste', 'filename', $_FILES["bildefil"]["name"][$i]))
                            {
                                alert_message('ERROR. can\'t insert into database.');
                            };
                        }
                    }
                    $i++;
                }

                alert_message("$files_add"."$files_not_add");
            }
        }

    function VisBilder()
    {
        global $images, $big, $cols, $files;
        $colCtr = 0;
        $gallery = "";
//        $files  = get_img_list("Bilder");

        if ($files != null)
        {
            $gallery =  '
                <table width="100%" cellspacing="3">
                    <tr>';

            foreach($files as $file)
            {
                //lager thumbs hvis det ikke er laget thumbs avbildet tidligere.
                $thumb = $images.$file;
                if(!file_exists($thumb))
                {
                    createThumbs($file, "Bilder/", "Bilder/thumbs/", 200);
                }
                if($colCtr %$cols == 0)
                    $gallery = $gallery. '
                        </tr>
                        <tr>
                            <td colspan="'.$cols.'">
                                <hr />
                            </td>
                        </tr>
                        <tr>';
                $gallery = $gallery. '
                            <td id="bilde" align="center">
                                <a href="fullscreen.php?bilde='.$big.$file.'" target="_blank">
                                    <img src="' . $images . $file . '" />
                                </a>
                            </td>';
                $colCtr++;
            }

            $gallery = $gallery. '</table>' . "\r\n";
        }
        return $gallery;
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
        $files = null;
        foreach ($f as $file){
            if(preg_match("/\.jp.?g$|\.png$|\.gif$/i", $file)){
                $files[] = $file;
            }
        }
        return $files;
    }

?>