<?php
require('dbconnect.php');
$failed = false;

if (isset($_POST["sName"])){$name = $_POST["sName"];} else { $failed = true; }
if (isset($_POST["sDescription"])){$description = $_POST["sDescription"];} else { $failed = true; }
if (isset($_POST["sQuery"])){$query = $_POST["sQuery"];} else { $failed = true; }
if (isset($_POST["sEndpoint"])){$endpoint = $_POST["sEndpoint"];} else { $failed = true; }
// encode the queries so they can be stored
$query = urldecode($query);
$description = urldecode($description);
if ($failed == false)
{
	$time = time();
	$query = "INSERT INTO queries VALUES(\"$time\",
										 \"" . mysql_real_escape_string($name) . "\",
										 \"" . mysql_real_escape_string($description) . "\",
										 \"" . mysql_real_escape_string($endpoint) . "\",
										 \"" . mysql_real_escape_string($query) . "\",
										 null, null)";
	

	mysql_query($query);
	
	header("Location: ../viewQuery.php?id=".$time);
}
else
{
	echo "query failed";	
}
// if we reach here then the query has failed :(

?>