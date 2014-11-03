<?php

//Take the content of the table from my DataBase, then I create the array $data
//and at the end I pass to $table_data.js doing and echo of the json object
$con = mysqli_connect("localhost","root","123456","Federica");
if (mysqli_connect_errno())
{
 	echo "Failed to connect to MySQL: " .mysqli_connect_error();
}

$keys = array ("vorname", "name", "geburtsdatum", "geburtsort");
$col = count($keys);

$whereClause = 0;

$query = "SELECT ";
for ($i=0; $i<$col-1; $i++)
{
	$query .= $keys[$i] .', ';
}
$query .= $keys[$col-1] ." FROM Mitarbeiter";

//The character % means that any other string can be found in the place where % is
if (!empty($_POST['vorname']))
{
	$query .= " WHERE LOWER(vorname) LIKE '" .$_POST['vorname'] ."%'";
	$whereClause++;
}
if (!empty($_POST['name']))
{
	if ($whereClause>0)  //I've already written other WHERE clauses
	{
		$query .= " AND ";
	}
	else
	{
		$query .= " WHERE ";
	}
	$query .= "LOWER(name) LIKE '" .$_POST['name'] ."%'";
	$whereClause++;
}
if (!empty($_POST['datum']))
{
	//I want the date written with the format 'YYYY-mm-dd'
	$date = date_create_from_format('m/d/Y', $_POST['datum']);
	$date = date_format($date, 'Y-m-d');
	if ($whereClause>0)  //I've already written other WHERE clauses
	{
		$query .= " AND ";
	}
	else
	{
		$query .= " WHERE ";
	}
	$query .= "geburtsdatum = '" .$date ."'";
	$whereClause++;
}
if (!empty($_POST['ort']))
{
	if ($whereClause>0)  //I've already written other WHERE clauses
	{
		$query .= " AND ";
	}
	else
	{
		$query .= " WHERE ";
	}
	$query .= "geburtsort = '" .$_POST['ort'] ."'";
}

// var_dump($query);

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
