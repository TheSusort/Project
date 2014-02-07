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
        @ $db = new mysqli('localhost', 'root', '1234', 'gruppef');
        if (mysqli_connect_errno())
        {
            global $db_is_connected;
                $db_is_connected = true;
            echo("
                <script type=\"text/javascript\">
                    alert(\"Error: Could not connect ot database. Please try again laiter.\");
                </script>
            ");
        }
        return;
    }
?>