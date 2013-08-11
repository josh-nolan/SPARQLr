<?php
include_once("arc2/ARC2.php");
$config = array(
	'db_name' => 'web234-a-wo-45',
	'db_user' => 'web234-a-wo-45',
	'db_pwd' => 'CFydj.VR-',
	'store_name' => 'sparqlr',
	/* stop after 100 errors */
	'max_errors' => 100,
	/* endpoint */
 	'endpoint_features' => array('select', 'ask'),
  	'endpoint_max_limit' => 10000 /* optional */
);

// set up endpoint
$ep = ARC2::getStoreEndpoint($config);
if (!$ep->isSetUp()) {
  $ep->setUp(); /* create MySQL tables */
}

//we need to run the following query to allow for some BIG joins
include_once('functions/dbconnect.php');
$sql = "SET OPTION SQL_BIG_SELECTS=1";
$result = mysql_query($sql, $con);


/* request handling */
$ep->go();
print_r($ep);
?>