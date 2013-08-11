<?php
require_once('functions/global.php');
if (isset($_POST['id']) && isset($_POST['endpoint']) && isset($_POST['query']))
{
	$id = $_POST['id'];
	$endpoint = $_POST['endpoint'];
	$query = $_POST['query'];

	// execute using YQL
	$data = false;
	while (!$data)
	{
		$data = createYQLRestQuery($endpoint, $query);
	}
	$i = 0;
	$columns = array();

	$vwName = $id;
	dropTriplesDataFromStore($vwName.".ttl");

	$d = "";
	$lb = "\n";
	
	$d .= "# RDF file for ".$vwName.".ttl".$lb;
	$d .= "@base <http://sparqlr.co.uk/IS/> .".$lb;
	$d .= "@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> . ".$lb;
	$d .= "@prefix q: <http://sparqlr.co.uk/q/> . ".$lb;
	$base = "http://sparqlr.co.uk/IS/";	
	// declare values for the view
	$d .= "<".$vwName.">\t"."rdf:type\tq:ISQuery .".$lb;
	$d .= "<".$vwName.">\t"."q:QueryId\t\"".$vwName."\" . " .$lb;
	$d .= "<".$vwName.">\t"."q:Title\t\"".$id."\" . " .$lb;
	
	$d .= "<".$vwName.">\t"."q:Endpoint\t\"".$endpoint."\" . " .$lb;
	$d .= "<".$vwName.">\t"."q:DateSaved\t\"".time()."\" . " .$lb.$lb;
	
	//TODO: deal with single row datasets
	while ($data['query']['results']['sparql']['result'][$i])
	{
		// row type
		$d .= "<".$vwName."/rowId=".$i.">\trdf:type\tq:".$vwName."\t.".$lb;

		$row = $data['query']['results']['sparql']['result'][$i];
		// for each column
		foreach ($row as $columnName => $columnArray) 
		{
			
			// s
			$d .= "<".$vwName."/rowId=".$i.">\t";
			// p
			$d .= "<".$vwName."/".$columnName.">\t";
			// o
			$value = $columnArray['value'];
			$d .= " \"".$value."\"\t.".$lb;			
		
			// add column to array so we can use it later
			if(!isset($columns[$columnName])) $columns[$columnName] = $base.$vwName."/".$columnName;
		}
		$d .= $lb;
		$i++;
	}
	
	// create SPARQL query
	$selectTerm = "";
	$whereTerm = "";
	$first = true;
	foreach ($columns as $c => $t)
	{
		$selectTerm .= 	" ?".$c;
		// if its not the first row col then wrap in optionals
		$whereTerm .="\t";
		if (!$first) $whereTerm .= "OPTIONAL { ";
		$whereTerm .= "?row <$t> ?$c . ";
		if (!$first) $whereTerm .= "} .";
		$whereTerm .= "\n";
		$first = false;
	}
	
	$sparqlQuery = "SELECT$selectTerm\nWHERE {\n$whereTerm}";

	$d .= "<".$vwName.">\t"."q:QuerySPARQL\t\"".$sparqlQuery."\" . " .$lb;
	$d .= "<".$vwName.">\t"."q:SPARQL\t\"".$query."\" . ".$lb;
	if (!validQuery($sparqlQuery))
	{
		echo "error, sparql query not valid :(";
	}
	else if (saveStringToFile($vwName.".ttl", $d))
	{	
		insertTriplesToStore($vwName.".ttl");
		echo $sparqlQuery;
	}
}
else if (isset($_POST['id']) && isset($_POST['tree_struc']))
{
	// id represents the root of the tree and tree_struc is the structure
	// for now lets just stick this in a text file
	saveStringToFile($_POST['id'].".src", $_POST['tree_struc']);
	echo "1";
}
?>