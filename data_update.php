<?php


$con = mysqli_connect("localhost","root","123456","Federica");
if (mysqli_connect_errno())
{
 	echo "Failed to connect to MySQL: " .mysqli_connect_error();
}

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

mysqli_close($con);

//Connection with data_search.php using an url
// $url = "http://localhost/extjs/data_search.php";
// $ch = curl_init($url);
// $datatopost = array (
//     "first" => $_POST['start'],
//     "last" => $_POST['limit']
// );
// $datatopost = json_encode($datatopost);
// $data = array ('json' => json_encode($datatopost));
// $curlConfig = array (
//     CURLOPT_URL            => $url,
//     CURLOPT_POST           => true,
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_POSTFIELDS     => $datatopost,
//     CURLOPT_HTTPHEADER     => array('Content-Type: application/x-www-form-urlencoded')
// );
// curl_setopt_array($ch, $curlConfig);
// $result = curl_exec($ch);
// $info = curl_getinfo($ch);
// curl_close($ch);
// echo $result;

?>
