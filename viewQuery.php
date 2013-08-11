<?php
	require_once('functions/global.php');
	
	if (isset($_GET['id']))
	{
		$vwName = $_GET['id'];
		$sQuery = getQuery($vwName);
	}
	else
	{
		header("Location: index.php");
	}
	
	$title = "Query: " . $sQuery->title;
	require_once('header.php');
?>
    
		<section id="content">
			<div class="contentBody">
                <h1><?php echo $sQuery->title;?></h1>
                <div class="column">
                	<p style="padding: 5px;"><?php echo  $sQuery->description;?></p>
					<?php
                	if ($sQuery->hasParameters)
                	{
                		echo "<div id='query_parameters'>";
                			echo "<h4>Parameters</h4>";
							foreach ($sQuery->parameters as $p)
							{
								$strippedName = substr($p, 2, strlen($p) - 3);
  								echo "<h5>" . $strippedName . "</h5>";
								echo "<input type='text style='width:300px;' id='" . $strippedName ."' class='inputform'>";
								echo "<div id='execute'>Execute</div>";
							}

                		echo "</div>";

                	}
                	?>
                </div>
                <div class="column">
					<label for="endpoint" class="inputform">ENDPOINT:</label>
                	<input type="text" name="endpoint" id="endpoint" class="inputform" style="width:350px;" disabled="disabled" value="<?php echo  $sQuery->endpoint;?>"/><br/>
                	<label for="endpoint" class="inputform">CLASSIFICATION:</label>
                	<input type="text" name="endpoint" id="endpoint" class="inputform" style="width:295px;" disabled="disabled" value="<?php echo  $sQuery->classification;?>"/><br/>
                	<label for="endpoint" class="inputform">TAGS:</label>
                	<?php
                		echo $sQuery->outputTags();
                	?>
                </div>
                <div class="clear"></div>
                <h3>Query</h3>
                <textarea id="querybox"><?php echo  $sQuery->SPARQL;?></textarea>    
                <h3>Results</h3>
                <div id="results"></div>
                <br/>
                <hr/>
                <div class="column">
                	<div class="share">
                        <h3>Share this query!</h3>
                        <p>You can share this query via the following URL:</p>
						<input type="text" id="sharelink" class="inputform" style="width:450px;" value="http://sparqlr.co.uk/q/<?php echo $sQuery->queryId;?>"/>
                        <br/><br/>
                        Or you can tweet about it: <br/>
                        <a href="https://twitter.com/share" class="twitter-share-button" data-text="Check out this SPARQLr query " data-via="SPARQLr" data-size="large">Tweet</a>
					</div>
            	 </div>
                 <div class="column">
                    <h3>Link this query!</h3>
                    <p>SPARQLr allows you to join queries on this site, if you have some data which isn't directly related (through HTTP URI's) then this allows you to join related queries together. If this sounds like something you're interested in then click the button:</p>
					 <a class="button" href="<?php echo $siteURL;?>/saveView.php?id=<?php echo $sQuery->queryId;?>">Materialize</a>
                </div>
                <div class="clear"></div>
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
			var queryParams = new Array();
			
			$(document).ready(function()
			{
				// highlight code from query box
				var querybox = document.getElementById('querybox');
				query = CodeMirror.fromTextArea(querybox,{
					mode :'sparql_parse',
					lineNumbers: false,
					readOnly : "nocursor",
				});
			
				if ($('#query_parameters').length)
				{
					$('#query_parameters .inputform').each(function()
					{
						queryParams[$(this).attr('id')] = "<%" + $(this).attr('id') + ">";
					});
					
					// we have some params
					$('#query_parameters .inputform').change( function() {
						var param = $(this).attr('id');
						var q = query.getValue();
						q = q.replace(queryParams[param], $(this).val());
						query.setValue(q);
						queryParams[$(this).attr('id')] = $(this).val();
						rewriteURLWithParams();
					});
					
					$('#query_parameters #execute').click( function() {
						executeSparql(query.getValue(), $('#endpoint').val(), $('#results'));
					});
					
					// now if we have some hash value in the URL we can populate the fields too
					if (location.hash.length > 0)
					{
						var hash = location.hash.substring(1);
						var params = hash.split("&");
						$.each(params, function(k,v)
						{
							var splitOnEquals = v.split("=");
							var param = splitOnEquals[0];
							var value = decodeURI(splitOnEquals[1]);
							$('#' + param).val(value);
							$('#' + param).change();
						});
						
						$('#query_parameters #execute').click();
					}
					
				}
				else
				{
					executeSparql(query.getValue(), $('#endpoint').val(), $('#results'));
				}
				
			});
			function rewriteURLWithParams()
			{
				var hashvalue = "#";
				var firstParam = true;
				$('#query_parameters .inputform').each( function()
				 {
					if (!firstParam) hashvalue += "&";
					hashvalue += $(this).attr('id') + "=" + encodeURI($(this).val());
					firstParam = false;
					location.hash = hashvalue;
				});
				 $('#sharelink').val($('#sharelink').val() + "#");
				var sl = $('#sharelink').val();
				$('#sharelink').val(sl.substring(0, $.inArray("#", $('#sharelink').val()) - 1) + hashvalue);								   
			}
		</script>
<?php require_once('footer.php'); ?>