<?php
require_once('functions/global.php');
$failed = false;

if (isset($_GET["s"])){$name = urldecode($_GET["s"]);} else { $failed = true; }
if (isset($_GET["r"])){$rules = $_GET["r"];} else { $failed = true; }

foreach ($rules as $key => $value)
{
	$rules[$k] = urldecode($v);
}

if ($failed == false)
{
	// lets make some triples to stick into our store
	$d = "";
	$lb = "\n";
	$d .= "# RDF file for INTEGRATED SOURCE : ".$name.".ttl".$lb;
	$d .= "@base <http://sparqlr.co.uk/q/> .".$lb;
	$d .= "@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> . ".$lb;
	$d .= "@prefix q: <http://sparqlr.co.uk/q/> . ".$lb;
	// declare values for the view
	$d .= "<".$name.">\t"."q:QueryId\t\"".$name."\" . " .$lb;
	$d .= "<".$name.">\t"."q:Title\t\"".$name."\" . " .$lb;
	$d .= "<".$name.">\t"."q:Endpoint\t\"http://sparqlr.co.uk/endpoint.php\" . " .$lb;
	$d .= "<".$name.">\t"."q:Classification\t\"IntegratedSource\" . " .$lb;
	$d .= "<".$name.">\t"."q:DateSaved\t\"".time(). "\" . " .$lb;
	$d .= "<".$name.">\trdf:type\tq:IntegratedSource .".$lb;
	// loop through query parameters
	foreach ($rules as $key => $value)
	{
		$d .= "<".$name.">\t"."q:IntegratedSourceRule\t\"".$value."\" . " .$lb;
	}

	// if this query already exists then drop from triplestore first
	//dropTriplesDataFromStore($name);

	// now lets save the file and insert into the store
	if (saveStringToFile($name, $d))
	{
		insertTriplesToStore($name);
	}

	header("Location: viewSource.php?id=".$name);
}
else
{
	echo "query failed";	
}
// if we reach here then the query has failed :(

?>