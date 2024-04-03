<?php
    $servername='localhost';
    $username='qbocorredoresdes_richard';
    $password='10Br3nd@10';
    $dbname = "qbocorredoresdes_gruposi3_qbo";
    $conn=mysqli_connect($servername,$username,$password,"$dbname");
    if(!$conn){
        die('Could not Connect MySql Server:' .mysqli_connect_error());
    }
?>