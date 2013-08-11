<?php
	require_once('functions/global.php');
	
	if (isset($_GET['id']))
	{
		$sQuery = new SparqlrQuery();
		$sQuery = getQuery($_GET['id']);
	}
	else
	{
		// query string isn't valid so bail
		header("Location: index.php");
	}	
	// right lets see if we can execute against YAHOO API with PHP...
	$data = createYQLRestQuery($sQuery->endpoint, $sQuery->SPARQL);
	while (!$data)
	{
		//TODO: check for failing requests :/
		$data = createYQLRestQuery($sQuery->endpoint, $sQuery->SPARQL);		
	}

	$i = 0;
	$columns = array();

	$vwName = "vw_" . stripIllegalChars($sQuery->queryId);
	dropTriplesDataFromStore($vwName.".ttl");

	$d = "";
	$lb = "\n";
	
	$d .= "# RDF file for ".$vwName.".ttl".$lb;
	$d .= "@base <http://sparqlr.co.uk/q/> .".$lb;
	$d .= "@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> . ".$lb;
	$d .= "@prefix q: <http://sparqlr.co.uk/q/> . ".$lb;
	$base = "http://sparqlr.co.uk/q/";	
	// declare values for the view
	$d .= "<".$vwName.">\t"."rdf:type\tq:View .".$lb;
	$d .= "<".$vwName.">\t"."q:QueryId\t\"".$vwName."\" . " .$lb;
	$d .= "<".$vwName.">\t"."q:Title\t\"".$vwName."\" . " .$lb;
	
	$d .= "<".$vwName.">\t"."q:Endpoint\t\"http://sparqlr.co.uk/endpoint.php\" . " .$lb;
	$d .= "<".$vwName.">\t"."q:DateSaved\t\"".time()."\" . " .$lb.$lb;
	$d .= "<".$vwName.">\t"."q:ViewOfQuery\t\"".$sQuery->queryId."\" . " .$lb.$lb;
	
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

	$d .= "<".$vwName.">\t"."q:QuerySPARQL\t\"".$sparqlQuery."\" . " .$lb.$lb;
	if (!validQuery($sparqlQuery))
	{
		echo "error, sparql query not valid :(";
	}
	else if (saveStringToFile($vwName.".ttl", $d))
	{	
		insertTriplesToStore($vwName.".ttl");
		header('Location: viewView.php?id='.$vwName);
	}
	else
	{
		echo "error: could not save to file";
	}
?>