<?php
	require_once('functions/global.php');
	
	if (isset($_GET['id']))
	{
		$vwName = $_GET['id'];
		$sQuery = getQuery($vwName);
	}
	else
	{
		// query string isn't valid so bail
		header("Location: index.php");
	}
	
	$title = "Query: " . $sQuery->title;
	
?>
<?php 
	require_once('header.php');
?>
    
		<section id="content">
			<div class="contentBody">
                <h1><?php echo $sQuery->title;?></h1>
                <h3>Materialized On: <?php echo date('d-m-y H:m:s', $sQuery->dateSaved);?></h3>

                <p>A materialized view of <a href="viewQuery.php?id=<?php echo $sQuery->viewOfQuery;?>">this query</a>. Inspect the triples that form this view <a href="data/<?php echo $sQuery->title;?>.ttl">here</a>.</p>
                <label for="endpoint" class="inputform">ENDPOINT:</label>
                <input type="text" name="endpoint" id="endpoint" class="inputform" style="width:350px;" disabled="disabled" value="<?php echo  $sQuery->endpoint;?>"/>
                <br/>
                <h3>Query</h3>
                <textarea id="querybox"><?php echo $sQuery->SPARQL;?></textarea>    
                <h3>Results</h3>
                <div id="results"></div>
                <br/>
               	<div id="log"></div>
				<script>
				!function(d,s,id)
				{
					var js,fjs=d.getElementsByTagName(s)[0];
					if(!d.getElementById(id))
					{
						js=d.createElement(s);
						js.id=id;
						js.src="//platform.twitter.com/widgets.js";
						fjs.parentNode.insertBefore(js,fjs);
					}
				}
				(document,"script","twitter-wjs");
				</script>
                </div>
            </div>
        </section>
        <script>
        	var query;
			var clearError = function(){};
			var markerHandle = null;
			
			$(document).ready(function()
			{
				// highlight code from query box
				var querybox = document.getElementById('querybox');
				var query = CodeMirror.fromTextArea(querybox,{
					mode :'sparql_parse',
					lineNumbers: false,
					readOnly : "nocursor"
				});
				
				executeSparql(query.getValue(), $('#endpoint').val(), $('#results'));
			});
			
			
		</script>
<?php require_once('footer.php');