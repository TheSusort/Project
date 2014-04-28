<?php
/**
 * Created by PhpStorm.
 * User: Gluck
 * Date: 07.02.14
 * Time: 11:12
 */

$db_is_connected = false;
include_once('funksjoner.php');

//  Connection to data base;
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
            $db->query("SET CHARACTER SET utf8");
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
            return FALSE;
//            exit(alert_message("Error: Could not connect to database."));
        }elseif(count($column) <> count($value))
        {
            alert_message("SQL insert query is incorrect");
            return FALSE;
        }else
        {
            $query = "INSERT INTO $table($column) VALUES ('$value');";
            $result = $db->query($query);
            if ($result)
            {
                return TRUE;
            }
        }
        return FALSE;
    }

// Delete data from database
    function db_delete($table, $column, $value){
		global $db_is_connected, $db;
		if (!$db_is_connected)
		{
			db_connnect();
		}
		if (!$db_is_connected)
		{
			alert_message("Error: Could not connect to database.");
			return FALSE;
		}else{
			$query = "DELETE FROM $table WHERE $column = '$value';";
			$result = $db->query($query);
			if ($result)
			{
				return TRUE;
			}
		}
		return FALSE;
	}

// Select data from database
	function db_select($table, $column, $group, $ret)
    {
        $result = null;
        global $db_is_connected, $db;
		if (!$db_is_connected)
		{
			db_connnect();
		}
        if (!$db_is_connected)
        {	
            alert_message("Error: Could not connect ot database.");
            return FALSE;
//            exit(alert_message("Error: Could not connect ot database."));
        }else
        {
            $query = "SELECT $column FROM $table $group;";
			// echo('<br><hr>db_select<br>'.$query);//-----------------------------------------------------
            $rslt = $db->query($query);
            if (!$rslt)
            {
                alert_message($query." isn't correct .");
                return FALSE;
            }else{
                while ($row = $rslt->fetch_assoc()) {
                    $result[] = $row["$ret"];
                }
                return $result;
            }
        }
        return FALSE;
    }
	
	function db_select_query($ret, $query){
		global $db_is_connected, $db;
		$result=array();
		if (!$db_is_connected){
			db_connnect();
		}
		$rslt = $db->query($query);
		if (!$rslt){
			alert_message($query." isn't correct .");
			return FALSE;
		}else{
			// echo('<br><hr>db_select_query<br>'.$query);//---------------------------------------------
			while ($row = $rslt->fetch_assoc()) {
				$result[] = $row["$ret"];
			}
			return $result;
		}
	}
?>
