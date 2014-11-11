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
if ($_POST['Geburtsort']!='not specified')  //I want to search also in relation to Geburtsort
											//(that can also be empty)
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
	$query .= " ORDER BY LOWER(" .$columnSort .") " .$directionSort;
}

$result = mysqli_query($con, $query);

//Save the infos token from the DB in the array $items,
//only if these have correct types (I check it using regular expression)
$items = array();
$exp_string = '/[a-z]/i';
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	preg_match_all($exp_string, $row['Vorname'], $match_vorname);
	preg_match_all($exp_string, $row['Name'], $match_name);
	if ( (strlen($row['Vorname']) == count($match_vorname[0]))
		&& (strlen($row['Name']) == count($match_name[0]))
		&& (!empty($row['Geburtsdatum'])) )
	 {
	 	$items[] =  $row;
	 }
}

mysqli_close($con);

//As a response sent back to index.js I've also to add how many Mitarbeiter there are
$totalItems = count($items);

//Split all the items regarding the limit parameter,
//so that each page shows only a limit number of Mitarbeiter
$itemsPage = array();

// var_dump($_POST['start']);
// var_dump($_POST['limit']);

//$i+$_POST['start'] has to be less then $totalItems so that $items[$i+$_POST['start']] always exist
for ($i=0; ($i<$_POST['limit']) && ($i+$_POST['start']<$totalItems); $i++)
{
	$itemsPage[$i] = $items[$i+$_POST['start']];
}

//The array $data that I'll send as a response (after having changed it into a json object)
//has to have also the info 'total'=totalItems
$data['items'] = $itemsPage;
$data['total'] = $totalItems;

$jsonData = json_encode($data);

//To give back to the file index.js the json object $jsonData I've to echo it
echo $jsonData;

?>
