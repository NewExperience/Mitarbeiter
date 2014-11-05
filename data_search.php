<?php

$con = mysqli_connect("localhost","root","123456","Federica");
if (mysqli_connect_errno())
{
 	echo "Failed to connect to MySQL: " .mysqli_connect_error();
}

$whereClause = 0;

$query = "SELECT * FROM Mitarbeiter";

//The character % means that any other string can be found in the place where % is
if (!empty($_POST['Vorname']))
{
	$query .= " WHERE LOWER(Vorname) LIKE '" .$_POST['Vorname'] ."%'";
	$whereClause++;
}
if (!empty($_POST['Name']))
{
	if ($whereClause>0)  //I've already written other WHERE clauses
	{
		$query .= " AND ";
	}
	else
	{
		$query .= " WHERE ";
	}
	$query .= "LOWER(Name) LIKE '" .$_POST['Name'] ."%'";
	$whereClause++;
}
if (!empty($_POST['Geburtsdatum']))
{
	//I want the date written with the format 'YYYY-mm-dd'
	$date = date_create_from_format('m/d/Y', $_POST['Geburtsdatum']);
	$date = date_format($date, 'Y-m-d');
	if ($whereClause>0)  //I've already written other WHERE clauses
	{
		$query .= " AND ";
	}
	else
	{
		$query .= " WHERE ";
	}
	$query .= "Geburtsdatum = '" .$date ."'";
	$whereClause++;
}
if (!empty($_POST['Geburtsort']))
{
	if ($whereClause>0)  //I've already written other WHERE clauses
	{
		$query .= " AND ";
	}
	else
	{
		$query .= " WHERE ";
	}
	$query .= "Geburtsort = '" .$_POST['Geburtsort'] ."'";
}

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
