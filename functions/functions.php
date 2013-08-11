<?php
	// create a uri to execute using using YQL
	function createYQLRestQuery($endpoint, $query)
	{
		// code snippet taken from 
		// http://snipplr.com/view.php?codeview&id=53633
		$yql = "use\"http://triplr.org/sparyql/sparql.xml\" as sparql;";
		$yql .= "SELECT * FROM sparql WHERE query = '";
		$yql .= $query."' ";
    	$yql .= "AND service=\"".$endpoint."\"";
		// now we need to stick this into a URL which when sent will execute on YQL stuff
		$YahooUrl = "http://query.yahooapis.com/v1/public/yql?q=";
		$YahooUrl .= urlencode($yql);
		$YahooUrl .= "&format=json";
		$json = get_data($YahooUrl);
		return json_decode($json, TRUE);
	}

	function createYQLRestQuery_withXML($endpoint, $query, $xml)
	{
		// code snippet taken from 
		// http://snipplr.com/view.php?codeview&id=53633
		$yql = "use\"".$xml."\" as sparql;";
		$yql .= "SELECT * FROM sparql WHERE query = '";
		$yql .= $query."' ";
    	$yql .= "AND service=\"".$endpoint."\"";
		// now we need to stick this into a URL which when sent will execute on YQL stuff
		$YahooUrl = "http://query.yahooapis.com/v1/public/yql?q=";
		$YahooUrl .= urlencode($yql);
		$YahooUrl .= "&format=json";
		$json = get_data($YahooUrl);
		return json_decode($json, TRUE);
	}


	function get_data($url) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	function isDataSetOkay($data)
	{
		if (isset($data['error']))
		{
			return false;
		}
		else if ($data['query']['results'] == null)
		{
			return false;
	
		}
		else
		{
			return true;
		}
	}
		
	
	function getVariableType($v) 
	{
		if (is_numeric($v))
		{
			// its a number - adding a 0 will cast to its proper type
			// http://www.php.net/manual/en/function.is-numeric.php#107326
			$v += 0;
			if (is_int($v))
				return "INTEGER";
			else if (is_float($v))
				return "FLOAT";
			else
			{
				// not sure what we would have... decimal
				return "DECIMAL";
			}
		}
		return "VARCHAR";
	}
	
	// get the SPARQL query for the Materialized view from our triplestore
	function getSPARQLQueryForView($id)
	{
		global $store;
		
		$query = "SELECT ?query WHERE
		{
			<http://sparqlr.co.uk/q/vw_".$id."> <http://sparqlr.co.uk/q/QuerySPARQL> ?query
		}";
		$rs = $store->query($query);
		
		if (!$store->getErrors()) {
			return $rs['result']['rows'][0]['query'];
		}
		return false;
	}
	
	// return results for the given sparql query
	function executeSPARQLOnView($q)
	{
		global $store;
		$rs = $store->query($q);
		
		if (!$store->getErrors()) {
			return $rs['result']['rows'];
		}
		return false;
	}
	

	// does the conversion from a dataset to our query class
	function dataSetToSparqlrQuery($data, $sparqlrQuery)
	{
		$sparqlrQuery->title = $data['Title'];
		$sparqlrQuery->queryId = $data['QueryId'];
		$sparqlrQuery->dateSaved = $data['DateSaved'];
		$sparqlrQuery->endpoint = $data['Endpoint'];
		if(isset($data['SPARQL'])) $sparqlrQuery->SPARQL = $data['SPARQL'];
		if(isset($data['QuerySPARQL'])) $sparqlrQuery->SPARQL = $data['QuerySPARQL'];
		if(isset($data['Description'])) $sparqlrQuery->description = $data['Description'];
		if(isset($data['Classification'])) $sparqlrQuery->classification = $data['Classification'];
		if(isset($data['ViewOfQuery'])) $sparqlrQuery->viewOfQuery = $data['ViewOfQuery'];
		if(isset($data['HasParameters']))
		{
			$sparqlrQuery->hasParameters = true;
		}

		return $sparqlrQuery;
	}

	// return an array of tags for the given query
	function getQueryTags($queryId)
	{
		global $store;
		$tags = array();
		$query = "SELECT * WHERE { <http://sparqlr.co.uk/q/".$queryId."> <http://sparqlr.co.uk/q/Tag> ?t . }";
		$rs = $store->query($query);
		if (!$store->getErrors())
		{
			$i = 0;
			while (isset($rs['result']['rows'][$i]['t']))
			{
				$tags[$i] = $rs['result']['rows'][$i]['t'];
				$i++;
			}
		}
		return $tags;
	}

	function getQuery($id)
	{
		global $store;
		
		$query = "SELECT * WHERE
		{
			<http://sparqlr.co.uk/q/".$id."> <http://sparqlr.co.uk/q/Title> ?Title .
			<http://sparqlr.co.uk/q/".$id."> <http://sparqlr.co.uk/q/QueryId> ?QueryId .
			<http://sparqlr.co.uk/q/".$id."> <http://sparqlr.co.uk/q/DateSaved> ?DateSaved .
			<http://sparqlr.co.uk/q/".$id."> <http://sparqlr.co.uk/q/Endpoint> ?Endpoint .
			<http://sparqlr.co.uk/q/".$id."> <http://sparqlr.co.uk/q/QuerySPARQL> ?SPARQL .
			OPTIONAL {<http://sparqlr.co.uk/q/".$id."> <http://sparqlr.co.uk/q/HasParameters> ?HasParameters} .
			OPTIONAL {<http://sparqlr.co.uk/q/".$id."> <http://sparqlr.co.uk/q/Description> ?Description} .
			OPTIONAL {<http://sparqlr.co.uk/q/".$id."> <http://sparqlr.co.uk/q/Classification> ?Classification} .
			OPTIONAL {<http://sparqlr.co.uk/q/".$id."> <http://sparqlr.co.uk/q/ViewOfQuery> ?ViewOfQuery } .
		}";
		$rs = $store->query($query);
		if (!$store->getErrors()) {
			$sQuery = new SparqlrQuery();
			$sQuery = dataSetToSparqlrQuery($rs['result']['rows'][0], $sQuery);
			$sQuery->tags = getQueryTags($sQuery->queryId);
			if ($sQuery->hasParameters) $sQuery->parameters = getQueryParameters($sQuery->SPARQL);
			return $sQuery;
		}
		else
		{
			print_r($store->getErrors());
		}
		
		return false;
	}

	// return all the 'rdf:type query' from the datastore
	function getQueries($type)
	{
	
		global $store;
		$query = "SELECT * WHERE
		{
			?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://sparqlr.co.uk/q/".$type."> .
			?s <http://sparqlr.co.uk/q/Title> ?Title .
			?s <http://sparqlr.co.uk/q/QueryId> ?QueryId .
			?s <http://sparqlr.co.uk/q/DateSaved> ?DateSaved .
			?s <http://sparqlr.co.uk/q/Endpoint> ?Endpoint .
			?s <http://sparqlr.co.uk/q/QuerySPARQL> ?SPARQL .
			OPTIONAL {?s <http://sparqlr.co.uk/q/Description> ?Description} .
			OPTIONAL {?s <http://sparqlr.co.uk/q/Classification> ?Classification} .
			OPTIONAL {?s <http://sparqlr.co.uk/q/ViewOfQuery> ?ViewOfQuery } .
		}";
		$rs = $store->query($query);
		if (!$store->getErrors())
		{
			$i = 0;
			$queryArray = array();
			while (isset($rs['result']['rows'][$i]))
			{
				$sQuery = new SparqlrQuery();
				$queryArray[$i] = dataSetToSparqlrQuery($rs['result']['rows'][$i], $sQuery);
				$queryArray[$i]->type = $type;
				$queryArray[$i]->tags = getQueryTags($queryArray[$i]->queryId);
				$i++;
			}
		}
		return $queryArray;
	}

	function getQuerySchema($id)
	{
		global $store;
		$query = "SELECT ?p WHERE
		{
  			<http://sparqlr.co.uk/q/".$id."/rowId=0> ?p ?o .
		}";
		$rs = $store->query($query);
		if (!$store->getErrors())
		{
			$i = 0;
			$schema = array();
			while (isset($rs['result']['rows'][$i]))
			{
				if ($rs['result']['rows'][$i]['p'] != "http://www.w3.org/1999/02/22-rdf-syntax-ns#type")
				$schema[$i] = str_replace("http://sparqlr.co.uk/q/$id/", "", $rs['result']['rows'][$i]['p']);
				$i++;
			}	
		}
		return $schema;
	}
	
	function stripIllegalChars($str)
	{
		$bad = array_merge(array_map('chr', range(0,31)), array("<", ">", ":"," ", '"', "/", "\\", "|", "?", "*"));
        $s = str_replace($bad, "", $str);	
		return $s;
	}
	
	// reset will empty all tables
	function triplestoreReset()
	{
		global $store;
		$store->reset();
		return true;
	}
	

	function saveStringToFile($name, $string)
	{
		$url = getURLToDataFile($name);
		$fh = fopen($url, 'w') or die("can't open file");
		fwrite($fh, $string);
		fclose($fh);
		return true;
	}

	function getURLToDataFile($file)
	{
		$myFile = "data/".$file;
		// set file url, different if we are localhost
		if ($_SERVER['HTTP_HOST'] == "localhost")
		{
			$url = "http://localhost/sparqlr/v1.3/".$myFile;

		}
		else
		{
			// TODO: this is sleazy, do better
			$url = dirname(__FILE__);
			$url = str_replace("functions", "", $url);
			$url = "file://" . $url . $myFile;
		}	
		return $url;
	}


	function dropTriplesDataFromStore($name)
	{
		$url = getURLToDataFile($name);

		global $store;
		// do we need to drop any of the triples that we already had from the last materialization? maybe
		//if (file_exists($url))
		//{
		$store->query('DELETE FROM <'.$url.'>');
		//}
		//else
		//{
		//	echo "<h1>File does not exist: $url</h1>";
		//	return false;
		//}
	}

	function insertTriplesToStore($name)
	{
		// TODO: need to alter the date somehow :/
		$url = getURLToDataFile($name);
		global $store;
		
		// DISABLED
		$rs = $store->query('LOAD <'.$url.'>');
		// $rs['query_time']                   Duration
		// $rs['result']['t_count']            Added triples
		// $rs['result']['load_time']          Load time
		// $rs['result']['index_update_time']  Index update time
		//if (!$store->getErrors())
		//{
		//	echo "added " . $rs['result']['t_count'] . " triples<br/>";
		//}
		//else
		//{
		//	print_r($store->getErrors());
		//}
		
	}

	function deleteFile($name)
	{
		$url = "data/".$name.".ttl";
		unlink($url);
	}


	function search($type, $field, $value)
	{
		global $store;
		
		if ($field == "Tag")
		{
			// we gotta do something a bit different for tags
			$query = "SELECT * WHERE
			{
				?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://sparqlr.co.uk/q/".$type."> .
				?s <http://sparqlr.co.uk/q/Title> ?Title .
				?s <http://sparqlr.co.uk/q/QueryId> ?QueryId .
				?s <http://sparqlr.co.uk/q/DateSaved> ?DateSaved .
				?s <http://sparqlr.co.uk/q/Endpoint> ?Endpoint .
				?s <http://sparqlr.co.uk/q/QuerySPARQL> ?SPARQL .
				?s <http://sparqlr.co.uk/q/Tag> \"".$value."\" .
				OPTIONAL {?s <http://sparqlr.co.uk/q/Description> ?Description} .
				OPTIONAL {?s <http://sparqlr.co.uk/q/Classification> ?Classification} .
				OPTIONAL {?s <http://sparqlr.co.uk/q/ViewOfQuery> ?ViewOfQuery } .
			}";
		}
		else
		{
			$query = "SELECT * WHERE
			{
				?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://sparqlr.co.uk/q/".$type."> .
				?s <http://sparqlr.co.uk/q/Title> ?Title .
				?s <http://sparqlr.co.uk/q/QueryId> ?QueryId .
				?s <http://sparqlr.co.uk/q/DateSaved> ?DateSaved .
				?s <http://sparqlr.co.uk/q/Endpoint> ?Endpoint .
				?s <http://sparqlr.co.uk/q/QuerySPARQL> ?SPARQL .
				OPTIONAL {?s <http://sparqlr.co.uk/q/Description> ?Description} .
				OPTIONAL {?s <http://sparqlr.co.uk/q/Classification> ?Classification} .
				OPTIONAL {?s <http://sparqlr.co.uk/q/ViewOfQuery> ?ViewOfQuery } .
				FILTER regex(str(?".$field."), \"".$value."\", \"i\") 
			}";
		}
	

		$rs = $store->query($query);
		if (!$store->getErrors())
		{
			$i = 0;
			$queryArray = array();
			while (isset($rs['result']['rows'][$i]))
			{
				$sQuery = new SparqlrQuery();
				$queryArray[$i] = dataSetToSparqlrQuery($rs['result']['rows'][$i], $sQuery);
				$queryArray[$i]->type = $type;
				$queryArray[$i]->tags = getQueryTags($queryArray[$i]->queryId);
				$i++;
			}
		}
		return $queryArray;
	}

	function getEndpoints()
	{
		global $store;
		$query = "SELECT DISTINCT ?endpoint ?class WHERE {
  					?s <http://sparqlr.co.uk/q/Endpoint> ?endpoint .
  					?s <http://sparqlr.co.uk/q/Classification> ?class .
				}";
		
		$rs = $store->query($query);
		if (!$store->getErrors())
		{
			$r = array();
			$i = 0;
			while (isset($rs['result']['rows'][$i]))
			{
				$r[$rs['result']['rows'][$i]['endpoint']] = $rs['result']['rows'][$i]['class'];
				$i++;
			}
		}
		return $r;
	}

	function getQueryParameters($query)
	{
		$params = array();
		$index = 0;
		$n = 0;
		while (!$index)
		{
			$index = strpos($query, "<%", $index);
			if ($index != false) 
			{
				$params[$n] = substr($query, $index, (strpos($query, ">", $index) + 1) - $index);
				$index++;
				$n++;
			}
		}
		if ($n == 0) return null;
		return $params;
	}

	// get the tree structure for the given source
	function getSourceTreeStructure($id)
	{
		return explode(" ", file_get_contents(getURLToDataFile($id.".src")));
	}

	function getSourceTreeRaw($id)
	{
		return file_get_contents(getURLToDataFile($id.".src"));
	}
	function doesTreeHaveCorrectLeaves($id)
	{
		$id = str_replace(".src", "", $id);
		$struc = getSourceTreeStructure($id);
		foreach ($struc as $node)
		{
			if ($node != "NULL" && $node != "")
			{
				$n = getSource($node);
				if ($n->title == "")
				{
					return false;
				}

			}
		}
		return true;
	}

	function getSources()
	{
		$s = array();
		foreach (glob("data/*.src") as $filename)
		{
    		$struc = file_get_contents($filename);
    		$struc = explode(" ", $struc);
			$struc = array_diff($struc, array("NULL"));
    		$s[str_replace("data/", "", $filename)] = count($struc);
		}
		return $s;
	}

	function getSource($id)
	{
		global $store;
		$query = "SELECT * WHERE
		{
			<http://sparqlr.co.uk/IS/".$id."> <http://sparqlr.co.uk/q/Title> ?Title .
			<http://sparqlr.co.uk/IS/".$id."> <http://sparqlr.co.uk/q/QueryId> ?QueryId .
			<http://sparqlr.co.uk/IS/".$id."> <http://sparqlr.co.uk/q/DateSaved> ?DateSaved .
			<http://sparqlr.co.uk/IS/".$id."> <http://sparqlr.co.uk/q/Endpoint> ?Endpoint .
			<http://sparqlr.co.uk/IS/".$id."> <http://sparqlr.co.uk/q/QuerySPARQL> ?QuerySPARQL .
		}";
		$rs = $store->query($query);
		if (!$store->getErrors()) 
		{
			$sQuery = new SparqlrQuery();
			$sQuery = dataSetToSparqlrQuery($rs['result']['rows'][0], $sQuery);
			return $sQuery;
		}
		return false;
	}


	// return all the source nodes in the triplestore
	function getSourceNodes()
	{
		global $store;
		$query = "SELECT * WHERE
		{
			?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://sparqlr.co.uk/q/ISQuery> .
			?s <http://sparqlr.co.uk/q/QueryId> ?QueryId .
			?s <http://sparqlr.co.uk/q/DateSaved> ?DateSaved .
		}";
		$rs = $store->query($query);
		if (!$store->getErrors())
		{
			$i = 0;
			$queryArray = array();
			while (isset($rs['result']['rows'][$i]))
			{
				$sQuery = new SparqlrQuery();
				$queryArray[$i] = dataSetToSparqlrQuery($rs['result']['rows'][$i], $sQuery);
				//$queryArray[$i]->type = $type;
				//$queryArray[$i]->tags = getQueryTags($queryArray[$i]->queryId);
				$i++;
			}
		}
		else
		{
			print_r($store->getErrors());
		}
		return $queryArray;
	}

	function getProject()
	{
		$urlToFile = "data/project.zip";
		if(!file_exists("../".$urlToFile))
		{
			// file doesn't exist so we need to make it!
			createProjectZip($urlToFile);
		}
		
		return $urlToFile;
	}

	function createProjectZip($url)
	{
		
	}

	function validQuery($q)
	{
		/* parser instantiation */
		$parser = ARC2::getSPARQLParser();
		$parser->parse($q);
		if (!$parser->getErrors())
		{
  			return true;
		}
		else {
  			//echo "invalid query: " . print_r($parser->getErrors());
  			return false;
		}

	}
?>