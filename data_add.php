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

//Control that have been added at least the infos for vorname, name and datum
//and that these have the correct type
//I do this using regular expressions and the function preg_match() to do the control
$exp_string = '/[a-z]/i';
$exp_datum = '/\d{2}\/\d{2}\/\d{4}/i';
if ( (preg_match($exp_string, $_POST['vorname']) == 1)
	&& (preg_match($exp_string, $_POST['name']) == 1)
	&& (preg_match($exp_datum, $_POST['datum']) == 1) )
{
	//The date has to be written with the format 'YYYY-mm-dd', so that I can add it into my DB
	$date = date_create_from_format('m/d/Y', $_POST['datum']);
	$date = date_format($date, 'Y-m-d');
	//Insert of the new Mitarbeiter
	$query_ins = "INSERT INTO Mitarbeiter (";
	for ($i=0; $i<$col-1; $i++)
	{
		$query_ins .= $keys[$i] .', ';
	}
	$query_ins .= $keys[$col-1] .") VALUES (";
	$query_ins .= "'" .$_POST['vorname'] ."', '" .$_POST['name'] ."', '" .$date ."', '" .$_POST['ort'] ."')";

	$result_ins = mysqli_query($con, $query_ins);
}

//Select the informations from the DB=>they won't contain the new infos if these haven't correct types
$query_sel = "SELECT ";
for ($i=0; $i<$col-1; $i++)
{
	$query_sel .= $keys[$i] .', ';
}
$query_sel .= $keys[$col-1] . " FROM Mitarbeiter";

$result_sel = mysqli_query($con, $query_sel);

//Save the infos token from the DB in the array $data, only if these have correct types
$data = array();
$exp_datum_DB = '/\d{4}\-\d{2}\-\d{2}/i';
while ($row = mysqli_fetch_array($result_sel, MYSQLI_ASSOC))
{
	if ( (preg_match($exp_string, $row['vorname']) == 1)
		&& (preg_match($exp_string, $row['name']) == 1)
	 	&& (preg_match($exp_datum_DB, $row['geburtsdatum']) == 1) )
	{
		$data[] =  $row;
	}
}

mysqli_close($con);

$jsonData = json_encode($data);

//To give back to the file billiton.js the json object $jsonData I've to echo it
echo $jsonData;

?>
