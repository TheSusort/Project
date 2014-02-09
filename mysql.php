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
        @ $db = new mysqli('localhost', 'user', '1234', 'gruppef');
        global $db_is_connected;
        if (mysqli_connect_errno())
        {
            $db_is_connected = false;
            alert_message("Error: Could not connect ot database. Please try again later.");
            return;
//            exit(alert_message("Error: Could not connect ot database. Please try again later."));
        }else{
            $db_is_connected = true;
        }
        return;
    }

// Insert data in database
    function db_insert($table, $column, $value)
    {
        global $db_is_connected, $db;
        if (!$db_is_connected)
        {
            alert_message("Error: Could not connect to database.");
            return;
//            exit(alert_message("Error: Could not connect to database."));
        }elseif(count($column) <> count($value))
        {
            alert_message("SQL insert query is incorrect");
            exit;
        }else
        {
            $query = "INSERT INTO $table($column) VALUES ('$value');";
            echo $query;
            $result = $db->query($query);
            if ($result)
            {
                alert_message($value.' was downloaded manually. \r\n Inserted into database.');
            }
        }
        return;
    }

// Delete data from database
    function db_delete($table, $column, $value)
{
    global $db_is_connected, $db;
    if (!$db_is_connected)
    {
        alert_message("Error: Could not connect to database.");
        return;
//        exit(alert_message("Error: Could not connect to database."));
    }elseif(count($column) <> count($value))
    {
        alert_message("SQL delete query is incorrect");
        exit;
    }else
    {
        $query = "DELETE FROM $table WHERE $column = '$value';";
        $result = $db->query($query);
        if ($result)
        {
            alert_message($value.' was deleted manually. \r\n Deleted from database.');
        }
    }
    return;
}

// Select data from database
    function db_select($table, $column)
    {
        $result = null;
        global $db_is_connected, $db;
        if (!$db_is_connected)
        {
            alert_message("Error: Could not connect ot database.");
            return;
//            exit(alert_message("Error: Could not connect ot database."));
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


?>
