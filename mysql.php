<?php
/**
 * Created by PhpStorm.
 * User: Gluck
 * Date: 07.02.14
 * Time: 11:12
 */

//  Connection to data base;
    $db_is_connected = false;
    function db_connnect()
    {
        global $db;
        @ $db = new mysqli('localhost', 'root', '1234', 'gruppef');
        global $db_is_connected;
        if (mysqli_connect_errno())
        {
            $db_is_connected = false;
            alert_message("Error: Could not connect ot database. Please try again later.");
//            exit;
        }
        $db_is_connected = true;
        return;
    }

// Insert data in database
    function db_insert($table, $column, $value)
    {
        global $db_is_connected, $db;
        if (!$db_is_connected)
        {
            alert_message("Error: Could not connect ot database.");
            exit;
        }elseif(count($column) <> count($value))
        {
            alert_message("SQL query is incorrect");
            exit;
        }else
        {
            $query = "INSERT INTO $table($column) VALUES ('$value');";
            $result = $db->query($query);
            if ($result)
            {
                alert_message($value.' inserted into database.');
            }
        }
        return;
    }

// Select data from database
    function db_select($table, $column)
    {
        global $db_is_connected, $db;
        if (!$db_is_connected)
        {
            alert_message("Error: Could not connect ot database.");
            exit;
        }else
        {
            $query = "SELECT $column FROM $table ;";
            $rslt = $db->query($query);
            if (!$rslt)
            {
                alert_message($query." isn't correct .");
            }else{
                while ($row = $rslt->fetch_assoc()) {
                    $result[] = $row['filename'];
                }
            }
        }
        return $result;
    }

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


//db_connnect();
//$files_in_db    = db_select('file_liste', 'filename');
//print_r($files_in_db);

?>
