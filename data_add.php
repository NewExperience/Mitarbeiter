<?php

//Take the content of the table from my DataBase, then I create the array $data
//and at the end I pass to $table_data.js doing and echo of the json object
$con = mysqli_connect("localhost","root","123456","Federica");
if (mysqli_connect_errno())
{
 	echo "Failed to connect to MySQL: " .mysqli_connect_error();
}

$keys = array ("Vorname", "Name", "Geburtsdatum", "Geburtsort");
$col = count($keys);

//Control that have been added at least the infos for vorname, name and datum
//and that these have the correct type
//I do this using regular expressions and the function preg_match() to do the control
$exp_string = '/[a-z]/i';
$exp_datum = '/\d{2}\/\d{2}\/\d{4}/i';
if ( (preg_match($exp_string, $_POST['Vorname']) == 1)
	&& (preg_match($exp_string, $_POST['Name']) == 1)
	&& (preg_match($exp_datum, $_POST['Geburtsdatum']) == 1) )
{
	//The date has to be written with the format 'YYYY-mm-dd', so that I can add it into my DB
	$date = date_create_from_format('m/d/Y', $_POST['Geburtsdatum']);
	$date = date_format($date, 'Y-m-d');
	//Insert of the new Mitarbeiter
	$query_ins = "INSERT INTO Mitarbeiter (";
	for ($i=0; $i<$col-1; $i++)
	{
		$query_ins .= $keys[$i] .', ';
	}
	$query_ins .= $keys[$col-1] .") VALUES (";
	$query_ins .= "'" .$_POST['Vorname'] ."', '" .$_POST['Name'] ."', '" .$date ."', '" .$_POST['Geburtsort'] ."')";

	$result_ins = mysqli_query($con, $query_ins);
}

mysqli_close($con);

//Connect this file with data_search.php (that's the one sending back to index.js)
header('Location: '."data_search.php");

?>
