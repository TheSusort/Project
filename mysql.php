<?php

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
            consol_message("Error: Could not connect to database. Please try again later.");
            return;
        }else{
            $db_is_connected = true;
            $db->query("SET CHARACTER SET utf8");
        }
        return;
    }

// Insert data in database
    function db_insert($table, $column, $value){
        global $db_is_connected, $db;
		if (!$db_is_connected){
			db_connnect();
		}
        if (!$db_is_connected){
            consol_message("Error: Could not connect to database.");
            return FALSE;
        }elseif(count($column) <> count($value)){
            consol_message("SQL insert query is incorrect");
            return FALSE;
        }else{
            $query = "INSERT INTO $table($column) VALUES ('$value');";
            $result = $db->query($query);
            if ($result){
                return TRUE;
            }
        }
        return FALSE;
    }
	
	function db_insert_query($query){
        global $db_is_connected, $db;
		if (!$db_is_connected){
			db_connnect();
		}
        if (!$db_is_connected){
            consol_message("Error: Could not connect to database.");
            return FALSE;
        }else{
			// echo($query);
            $result = $db->query($query);
            if ($result){
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
			consol_message("Error: Could not connect to database.");
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
            consol_message("Error: Could not connect to database.");
            return FALSE;
        }else{
            $query = "SELECT $column FROM $table $group;";
            $rslt = $db->query($query);
            if (!$rslt)
            {
                consol_message($query." isn't correct .");
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
			consol_message($query." isn't correct .");
			return FALSE;
		}else{
			while ($row = $rslt->fetch_assoc()) {
				$result[] = $row["$ret"];
			}
			return $result;
		}
	}
	
	function db_do_query($query){
		$result = null;
        global $db_is_connected, $db;
		if (!$db_is_connected){
			db_connnect();
		}
        if (!$db_is_connected){	
            consol_message("Error: Could not connect to database.");
            return FALSE;
        }else{
			$result = $db->query($query);
			if ($result)
			{
				return TRUE;
			}
		}
		return FALSE;
	}
?>
