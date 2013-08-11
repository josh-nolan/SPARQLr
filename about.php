<?php
	$title = "About";
    require_once('functions/global.php');
	require_once('header.php');
?>
<section id="content">
    <div class="contentBody">
        <h1>About</h1>
         <p>So SPARQLr (if you can forgive the very generic web 2.0 name) is my third year project, it is a web based Linked Data mashup tool, it allows users to discover, interact and manipulate data. I am hoping for it to become a social SPARQL query repository, where users can  learn to write queries quickly - and I think this is best done by learning by example. </p>
         <p><a href="listSources.php">Integrated Source's</a> allow you to integrate data from multiple different sources all from a single page.</p>
         <h3>Linked data what?</h3>
         <p>If this is all brand new to you then I would reccomend you check out the following video, a Tim Berners-Lee speech from 2009 where he is introducing the idea of 'linked data' and why it is so important:</p>
         <iframe src="http://embed.ted.com/talks/tim_berners_lee_on_the_next_web.html" width="640" height="360" frameborder="0" scrolling="no" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
         <p>You can see the current state of the Linked Data Cloud <a href="http://richard.cyganiak.de/2007/10/lod/lod-datasets_2011-09-19_colored.png" target="_blank">here</a>. I have also written a blog post about <a href="http://www.sparqlr.co.uk/blog/2012/10/03/linked-data/">linked data</a> and how it relates to my project.</p>
         <h3>SPARQL</h3>
         <p>This is the query language which allows you to query all these datasources, if you know SQL you are part of the way there already. To learn SPARQL I invested in <a href="http://www.amazon.co.uk/Learning-SPARQL-Bob-DuCharme/dp/1449306594" target="_blank">Learning SPARQL by Bob DuCharme</a> which has been very helpful. There are also many helpful resources online, there is great collection provided by LinkedData.org <a href="http://linkeddata.org/guides-and-tutorials" target="_blank">here</a> - you will be writing SPARQL in no time.</p>
        </div>
</section>
<?php 
	require_once('footer.php');
?>