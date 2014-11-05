<?php

$con = mysqli_connect("localhost","root","123456","Federica");
if (mysqli_connect_errno())
{
 	echo "Failed to connect to MySQL: " .mysqli_connect_error();
}

$keys = array ("vorname", "name", "geburtsdatum", "geburtsort");
$col = count($keys);

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
