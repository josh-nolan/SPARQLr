<?php
	require_once('functions/global.php');
	$title = "Admin";
	require_once('header.php');
?>
<section id="content">
    <div class="contentBody">
    	<h1>Admin Panel</h1>
    	<p>Restting the triplestore will remove all triples:<br/>
    	<a class="button" href="triplestoreFunctions.php?function=reset&callback=admin.php">Reset Triplestore</a></p>
    	<hr/>
    	<h2>Files present in triplestore</h2>
    	<p>Check what ttl files have been sucessfully inserted into our triplestore:
    	<h3>Queries and Views</h3>
    	<table width="100%" align="center">
    		<tr>
    			<td><b>Filename</b></td>
    			<td><b>Present in triplestore</b></td>
    			<td><b>Links</b></td>
    			<td><b>Functions</b></td>
    		</tr>
    		<?php
    		$queryNames = array();

    		$queries = getQueries("Query");
    		foreach ($queries as $q)
        {
          $queryNames[$q->queryId] = $q->dateSaved;
        }
        
        $views = getQueries("View");
        foreach ($views as $v)
        {
          $queryNames[$v->queryId] = $v->dateSaved;
        }

        $sources = getSourceNodes();
        foreach ($sources as $s)
        {
          $queryNames[$s->queryId] = $s->dateSaved;
        }

    		$files = glob('data/*.{ttl}', GLOB_BRACE);
        foreach($files as $file)
        {
  				$title = str_replace("data/", "", $file);
  				$title = str_replace(".ttl", "", $title);	
  				echo "<tr><td>". str_replace("data/", "", $file) ."</td>";
  				echo "<td>";
  				if (isset($queryNames[$title])) 
  				{
  					echo "File was created at " . date('d-m-y H:i:s', $queryNames[$title]);
  					$stored = true;
  				}
  				else
  				{
  					echo "-";
  					$stored = false;
  				}
  				echo "</td>";
  				
  				// TODO: should viewQuery.php and viewView.php be the same page? probs
  				echo "<td><a href=\"".$file."\">Triples</a>";
  				if ($stored) echo " | <a href=\"viewQuery.php?id=".$title."\">View Query</a>";
  				echo "</td>";

  				echo "<td>";
  				if ($stored) echo "<a href='triplestoreFunctions.php?function=drop&title=".$title.".ttl&callback=admin.php'>DROP FROM STORE</a>";
  				else echo "<a href='triplestoreFunctions.php?function=insert&title=".$title.".ttl&callback=admin.php'>INSERT</a> | <a href='triplestoreFunctions.php?function=deletefile&title=".$title."&callback=admin.php'>DELETE FILE</a>";
  				echo "</tr>";
  			}
  			?>
    	</table>
    	<?php
    		
    	?>
    	</span>
    	<hr/>
    	<p>
    </p>
    </div>
</section>
<?php require_once('footer.php');