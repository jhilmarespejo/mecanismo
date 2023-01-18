<?php
$serverName = "192.168.2.42"; //serverName\instanceName
$connectionInfo = array( "Database"=>"test", "UID"=>"defensoria", "PWD"=>"D3f3ns0r.2019.DB");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
     echo "Connection established.<br />";
}else{
     echo "Connection could not be established.<br />";
     die( print_r ( sqlsrv_errors(), true) );
}

?>