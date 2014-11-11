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
//and that vorname and name are strings of letters
//I do this using a regular expression and the function preg_match_all() that returns an array containing the matches
$exp_string = '/[a-z]/i';

if(empty($_POST['Geburtsort']))
{
	$_POST['Geburtsort'] = null;
}

preg_match_all($exp_string, $_POST['Vorname'], $match_vorname);
preg_match_all($exp_string, $_POST['Name'], $match_name);

if ( (strlen($_POST['Vorname']) == count($match_vorname[0]))
	&& (strlen($_POST['Name']) == count($match_name[0]))
	&& (!empty($_POST['Geburtsdatum'])) )
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

?>
