<?php
require_once("class/SparqlrQuery.php");
include_once("arc2/ARC2.php");
include_once("dbconnect.php");

$config = array(
	'db_name' => 'web234-a-wo-45',
	'db_user' => 'web234-a-wo-45',
	'db_pwd' => 'CFydj.VR-',
	'store_name' => 'sparqlr'
);
// set up store
$store = ARC2::getStore($config);
if (!$store->isSetUp())
{
  $store->setUp();
}

//we need to run the following query to allow for some BIG joins
include_once('functions/dbconnect.php');
$sql = "SET OPTION SQL_BIG_SELECTS=1";
$result = mysql_query($sql, $con);

$siteURL = "http://sparqlr.co.uk";
if ($_SERVER['HTTP_HOST'] == "localhost") { $siteURL = "http://localhost/sparqlr/v1.3"; }

include_once("functions.php");
?>