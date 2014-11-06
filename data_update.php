<?php

$con = mysqli_connect("localhost","root","123456","Federica");
if (mysqli_connect_errno())
{
 	echo "Failed to connect to MySQL: " .mysqli_connect_error();
}

$keys = array ("Vorname", "Name", "Geburtsdatum", "Geburtsort");
$col = count($keys);

$query = "UPDATE Mitarbeiter SET";
//Change only those values (for Vorname, Name and Geburtsdatum) that aren't empty,
//because they have always to be written
//Variable used to count how many informations I've to update => how many informations aren't empty
$notEmptyInfo = 0;
if(!empty($_POST['Vorname']))
{
	$query .= " Vorname = '" .$_POST['Vorname'] ."'";
	$notEmptyInfo++;
}
if(!empty($_POST['Name']))
{
	if ($notEmptyInfo>0)
	{
		$query .= ",";
	}
	$query .= " Name = '" .$_POST['Name'] ."'";
	$notEmptyInfo++;
}
if(!empty($_POST['Geburtsdatum']))
{
	//I want the date written with the format 'YYYY-mm-dd'
	$date = date_create_from_format('m/d/Y', $_POST['Geburtsdatum']);
	$date = date_format($date, 'Y-m-d');
	if ($notEmptyInfo>0)
	{
		$query .= ",";
	}
	$query .= " Geburtsdatum = '" .$date ."'";
	$notEmptyInfo++;
}
if (empty($_POST['Geburtsort']))
{
	$_POST['Geburtsort'] = null;
}
if ($notEmptyInfo>0)
{
	$query .= ",";
}
$query .= " Geburtsort = '" .$_POST['Geburtsort'] ."'";
$query .= " WHERE ID = '" .$_POST['ID'] ."'";

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
