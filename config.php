<?php

    session_start();

    $servername = "localhost";  
    $db_username = "root";
    $db_password = "";
    $db_name = "gym";

    $conn = mysqli_connect($servername, $db_username, $db_password, $db_name);

    if(!$conn) {
        die("neuspesna konekcija");
    }