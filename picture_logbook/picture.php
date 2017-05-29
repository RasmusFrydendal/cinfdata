<?php
header( 'Content-Type: image/jpeg', true );
include("../common_functions_v2.php");
# Create data base connection
$dbi = std_dbi();
#mysql_set_charset('utf8', $db);

$id_number = $_GET['id'];

$query = "SELECT data from binary_data WHERE id = " . $id_number;
$result = mysqli_query($dbi, $query);
$row = mysqli_fetch_array($result);
echo($row[0]);
?>