<?php
	require_once('functions/global.php');
	
	$title = "TESTS";
	require_once('header.php');
?>
<section>
	<?php
		echo "<h1>Testing: createYQLRestQuery(endpoint, query)</h1>";
		$NUM_TESTS = 30;
		$t = 0;
		$results = array();
		echo "<p>Basic test to see the success rate of the YQL Function given a simple query</p>";
		echo "<p>endpoint: dbpedia.org/sparql<br/>";
echo "query: SELECT * WHERE { ?s ?p ?o . } LIMIT 10</p>";
		
		while ($t < $NUM_TESTS)
		{
			$results[$t] = array();
			$results[$t]['id'] = $t;
			$results[$t]['executedAt'] = microtime(true);
			$data = createYQLRestQuery("http://dbpedia.org/sparql", "SELECT * WHERE { ?s ?p ?o . } LIMIT 10");
			$results[$t]['responseAt'] = microtime(true);
			$results[$t]['timeTaken'] = $results[$t]['responseAt'] - $results[$t]['executedAt'];
			if(isDataSetOkay($data))
			{
				$results[$t]['success'] = 1;
				
				if(isset($data['query']['results']['sparql']['result']))
				{
					$results[$t]['error'] = sizeof($data['query']['results']['sparql']['result'])." rows recieved";
				}
				else
				{
					$results[$t]['error'] = "nope";
				}
			}
			else
			{
				$results[$t]['success'] = 0;
				$results[$t]['error'] = $data['error']['description'];
			}
			$t++;
		
		}

		printResultArray($results);

		echo "<br/><hr/><br/>";
		echo "<h1>Testing: createYQLRestQuery_withXML(endpoint, query, xmlLocation)</h1>";
		echo "<p>I want to see the results if I host the <i>sparql.xml</i> locally instead of using triplr.org...</p>";
		$NUM_TESTS = 30;
		$t = 0;
		$results = array();
		echo "<p>Basic test to see the success rate of the YQL Function given a simple query</p>";
		echo "<p>endpoint: dbpedia.org/sparql<br/>";
		echo "query: SELECT * WHERE { ?s ?p ?o . } LIMIT 10<br/>";
		echo "xmlLocation: sparqlr.co.uk/sparql.xml</p>"; 
		while ($t < $NUM_TESTS)
		{
			$results[$t] = array();
			$results[$t]['id'] = $t;
			$results[$t]['executedAt'] = microtime(true);
			$data = createYQLRestQuery_withXML("http://dbpedia.org/sparql", "SELECT * WHERE { ?s ?p ?o . } LIMIT 10", "http://sparqlr.co.uk/sparql.xml");
			$results[$t]['responseAt'] = microtime(true);
			$results[$t]['timeTaken'] = $results[$t]['responseAt'] - $results[$t]['executedAt'];
			if(isDataSetOkay($data))
			{
				$results[$t]['success'] = 1;
				
				if(isset($data['query']['results']['sparql']['result']))
				{
					$results[$t]['error'] = sizeof($data['query']['results']['sparql']['result'])." rows recieved";
				}
				else
				{
					$results[$t]['error'] = "nope";
				}
			}
			else
			{
				$results[$t]['success'] = 0;
				$results[$t]['error'] = $data['error']['description'];
			}
			$t++;
		
		}

		printResultArray($results);


		function printResultArray($r)
		{
			$averages = array();
			echo "<table width='100%' cellspacing='2' border='1'>";
				// print out headers
				echo "<tr>"; 
				foreach ($r[0] as $key => $value)
				{
					echo "<td>$key</td>";
					if (is_numeric($value))
					{
						$averages[$key] = 0; 
					}
					else
					{
						$averages[$key] = "";
					}
				}
				echo "</tr>";
				// print out body cells
				foreach($r as $rowId => $rowValue)
				{
					echo "<tr>";
					foreach($rowValue as $cellName => $cellValue)
					{
						echo "<td>".$cellValue."</td>";
						if (is_numeric($cellValue))
						{
							$averages[$cellName] += $cellValue;
						}
					}
					echo "</tr>";
				}
				echo "<tr>";
					foreach($averages as $key => $value)
					{
						$value = $value / $rowId;
						echo "<td>".$value."</td>";
					}
				echo "</tr>";
			echo "</table>";
		}
	?>
<section>
<?php require_once('footer.php'); ?>