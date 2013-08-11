<?php
require_once('functions/global.php');
$failed = false;

if (isset($_POST["sName"])){$title = $_POST["sName"];} else { $failed = true; }
if (isset($_POST["sDescription"])){$description = $_POST["sDescription"];} else { $failed = true; }
if (isset($_POST["sQuery"])){$query = $_POST["sQuery"];} else { $failed = true; }
if (isset($_POST["sEndpoint"])){$endpoint = $_POST["sEndpoint"];} else { $failed = true; }
if (isset($_POST["sTags"])){$tags = $_POST["sTags"];} else { $failed = true; }
if (isset($_POST["sClassification"])){$classification = $_POST["sClassification"];} else { $failed = true; }

// decode the queries so they can be stored
$query = urldecode($query);
$description = urldecode($description);



// check for " and swap for
$query = str_replace("\"", "\\\"", $query);
$name = stripIllegalChars($title);

// lets validate the query to ensure the spambot doesn't kill us :(
if (!validQuery($query))
{
	$failed = true;
}

// process tags
$tags = explode(",",$tags);

foreach ($tags as $key => $value)
{
	$tags[$key] = strtoupper(trim($value));
}

if ($failed == false)
{
	// lets make some triples to stick into our store
	$d = "";
	$lb = "\n";
	$d .= "# RDF file for QUERY: ".$name.".ttl".$lb;
	$d .= "@base <http://sparqlr.co.uk/q/> .".$lb;
	$d .= "@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> . ".$lb;
	$d .= "@prefix q: <http://sparqlr.co.uk/q/> . ".$lb;
	$base = "http://sparqlr.co.uk/q/";	
	// declare values for the view
	$d .= "<".$name.">\t"."q:QueryId\t\"".$name."\" . " .$lb;
	$d .= "<".$name.">\t"."q:Title\t\"".$title."\" . " .$lb;
	$d .= "<".$name.">\t"."q:Endpoint\t\"".$endpoint."\" . " .$lb;
	$d .= "<".$name.">\t"."q:Classification\t\"".$classification."\" . " .$lb;
	$d .= "<".$name.">\t"."q:DateSaved\t\"".time(). "\" . " .$lb;
	$d .= "<".$name.">\t"."q:Description\t\"".$description."\" . " .$lb;
	$d .= "<".$name.">\t"."q:QuerySPARQL\t\"".$query."\" . " .$lb;
	$d .= "<".$name.">\trdf:type\tq:Query .".$lb;

	if (strpos($query, "<%", 0) != false)
	{
		// loop through query parameters
		$params = getQueryParameters($query);
		$d .= "<".$name.">\tq:HasParameters\t\"true\" . ".$lb;
	}
	
	foreach ($tags as $key => $value)
	{
		$d .= "<".$name.">\t"."q:Tag\t\"".$value."\" . " .$lb;
	}
	// if this query already exists then drop from triplestore first
	dropTriplesDataFromStore($name.".ttl");
	
	// now lets save the file and insert into the store
	if (saveStringToFile($name.".ttl", $d))
	{
		insertTriplesToStore($name.".ttl");
		header("Location: viewQuery.php?id=".$name);
	}
	else
	{
		echo "error 64: saveStringToFile()";
	}

	

}
else
{
	echo "query save failed";	
}
// if we reach here then the query has failed :(

?>