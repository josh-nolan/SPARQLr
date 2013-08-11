<?php
	$title = "Home";
	require_once('header.php');
?>
<section id="content">
    <div class="contentBody">
		<div class="column">
			<div id="Overview">
				<h1>SPARQLr Overview</h1>
				<h2 style="text-align: center;">A linked data mashup tool</h2>
				<p>SPARQLr is a Third Year Project created by <a href="http://joshnolan.co.uk/" target="_blank" title="JoshNolan.co.uk">Josh Nolan</a> for <a href="http://www.cs.manchester.ac.uk/">The School of Computer Science, Manchester</a>.
					You can find out more information about the project <a href="http://www.sparqlr.co.uk/blog/">here</a>. So what does SPARQLr do:</p>
				<ul>
					<li>An <a href="http://sparqlr.co.uk/editor.php" title="SPARQLr: Editor">editor</a> which allows you to write <a href="http://en.wikipedia.org/wiki/SPARQL" title="Wikipedia: SPARQL" target="_blank">SPARQL</a> queries and execute them against <a href="http://labs.mondeca.com/sparqlEndpointsStatus/" title="SPARQL Endpoints - Status" target="_blank">SPARQL endpoints</a> and view the results</li>
					<li>A <a href="http://sparqlr.co.uk/listQueries.php" title="SPARQLr: Queries">repository</a> of queries which users can view, learn and extend on</li>
					<li>Users can <a href="http://en.wikipedia.org/wiki/Materialized_view" target="_blank" title="Wikipedia: Materialized View">materialize</a> queries into the SPARQLr triplestore which allows for further manipulation. Meaning that previously unlinked data between two sources can now be joined together!</li>
					<li><i>Currently in development:</i> The creation of <a href="createIntegratedSource.php" title="SPARQLr: Create an Integrated Source">Integrated Sources</a> which will reconcile the semantic differences in entities between datasources.</li>
				</ul>
				<h2>Latest @SPARQLr Tweet:</h2>
				    <div id="tweet"></div>
				<p>Follow <a href="http://twitter.com/SPARQLr" target="_blank" title="Twitter: SPARQLr">@SPARQLr</a> on twitter for regular updates!</p>
				</div>
		</div>
		<div class="column">
			<h3>11-06-13</h3>
			<p>I have finally had chance to fix the bugs listed below, hopefully that's the last we see of the evil spambot >:)</p>
			<h3>20-04-13</h3>
			<p>A quick note to say that all saving of queries / views has been disabled until further notice, the site has been hit by a spambot which is posting crude and offensive messages, keep up to date by following @SPARQLr.</p>
			<h3>SPARQLr v1.3</h3>
			<p>SPARQLr v1.3 has arrived and is the most exciting iteration yet! By introduction 'integrated sources' SPARQLr hopes to reconcile semantics, 
			create some very interesting datasets and look good while doing it! Read about v1.3 <a href="http://www.sparqlr.co.uk/blog/2013/02/12/iteration-3/">here</a>.
			<hr/>
			<h3>SPARQLr v1.2</h3>
			<p>SPARQLr v1.2 allows you to save your SPARQL queries. These queries can then be materialised into the SPARQLr triplestore, this triplestore
			can then be queried to join two or more queries together! Read more about what v1.2 was <a href="http://www.sparqlr.co.uk/blog/2012/11/12/iteration-two/">about</a>,
			and the <a href="http://www.sparqlr.co.uk/blog/2012/12/21/iteration-2-results/">results</a> of completing this iteration.</p>
			<hr/>
			<h3>SPARQLr v1.1</h3>
			<p>Welcome to SPARQLr v1.1, finally I have some code for you to try out. With a <s>fully</s> partially functioning  sparql editor with storage facilities you can now write a SPARQL query and store it to share with others. Get going right away with <a href="queryEditor.php">the query editor</a> or check out some of the <a href="listQueries.php">saved queries</a>.</p>
			<p>The code is very much in development, so be prepared to encounter quite a few bugs, but if you do come across any major issues, have any advice or reccomendations or want to find out more about the project then please contact me at <a href="mailto:joshnolan118@gmail.com">joshnolan118@gmail.com</a>.</p>
			<hr/>
			<h3>A Third Year Project by Josh Nolan.</h3>
			<p>SPARQLr is currently under major construction, I am hoping to release some code before Christmas. If you would like to learn more about the project then check out the <a href="http://sparqlr.co.uk/blog/" target="_blank">blog</a>. I make regular posts regarding the progress of the project. You can also <a href="http://twitter.com/SPARQLr" target="_blank">follow SPARQLr on twitter.</a></p>
   	 	</div>
		<div class="clear"></div>
	</div>
</section>
<script>
	$(document).ready(function()
	{
		$.getJSON("https://api.twitter.com/1/statuses/user_timeline/sparqlr.json?count=1&include_rts=1&callback=?", function(data) 
		{
     		$("#tweet").html(data[0].text);
		});
	});
</script>
<?php 
	require_once('footer.php');
?>