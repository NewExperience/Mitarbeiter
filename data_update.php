<?php

$con = mysqli_connect("localhost","root","123456","Federica");
if (mysqli_connect_errno())
{
 	echo "Failed to connect to MySQL: " .mysqli_connect_error();
}

$keys = array ("Vorname", "Name", "Geburtsdatum", "Geburtsort");
$col = count($keys);

//I want the date written with the format 'YYYY-mm-dd'
$date = date_create_from_format('m/d/Y', $_POST['Geburtsdatum']);
$date = date_format($date, 'Y-m-d');

$query = "UPDATE Mitarbeiter SET";
$query .= " Vorname = '" .$_POST['Vorname'] ."',";
$query .= " Name = '" .$_POST['Name'] ."',";
$query .= " Geburtsdatum = '" .$date ."'";
if (!empty($_POST['Geburtsort']))
{
	$query .= ", Geburtsort = '" .$_POST['Geburtsort'] ."'";
}
$query .= " WHERE ID = '" .$_POST['ID'] ."'";

//At least one info token from the form has to be the same as the one in the DB
// $query .= " WHERE vorname = '" .$_POST['vorname'] ."'";
// $query .= " OR name = '" .$_POST['name'] ."'";
// $query .= " OR geburtsdatum = '" .$date ."'";
// if (!empty($_POST['ort']))
// {
// 	$query .= " OR geburtsort = '" .$_POST['ort'] ."'";
// }

$result = mysqli_query($con, $query);

//Save the infos token from the DB in the array $data
$data = array();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$data[] =  $row;
}

mysqli_close($con);

$jsonData = json_encode($data);

//To give back to the file table_data.js the json object $jsonData I've to echo it
echo $jsonData;

?>
