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

// As soon as I click in a column's header I've to order the infos depending on the column clicked ($columnSort)
// (it's sent in the key 'property' of the array sort)
// and in the direction sent in the key 'direction' ($directionSort) of the array $sort

//Use the function isset to check if the info 'sort' has been sent as a parameter
if (isset($_POST['sort']))
{
	//Since $_POST['sort'] is a json string I decode it in the associative array $sort
	$sort = json_decode($_POST['sort'], true);
	$columnSort = $sort[0]['property'];
	$directionSort = $sort[0]['direction'];
	$query .= " ORDER BY " .$columnSort ." " .$directionSort;
}

$result = mysqli_query($con, $query);

//Save the infos token from the DB in the array $items
$items = array();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$items[] =  $row;
}

mysqli_close($con);

//As a response sent back to index.js I've also to add how many Mitarbeiter there are
$totalItems = count($items);

//Split all the items regarding the limit parameter,
//so that each page shows only a limit number of Mitarbeiter
for($i=0; $i<$totalItems; $i++)
{
	$column[$i] = $items[$i]['Vorname'];
}

//The array $data that I'll send as a response (after having changed it into a json object)
//has to have also the info 'total'=totalItems
$data['items'] = $items;
$data['total'] = $totalItems;

$jsonData = json_encode($data);

//To give back to the file index.js the json object $jsonData I've to echo it
echo $jsonData;

?>
