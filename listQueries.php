<?php
  require_once('functions/global.php');
  $title = "List Queries";
  require_once('header.php');
?>
<section id="content">
  <div class="contentBody">
    <h1>Queries</h1>
    <p>Below are queries written by SPARQLr users, check them out - they may help you learn a few things!</p>
 		<div class="queries">
      <?php
        $queries = getQueries("Query");
        foreach ($queries as $q)
        {
          $q->outputModuleHtml();
        }
      ?> 
      <div class="clear"></div>
    </div>
  </div>
</section>
<?php 
	require_once('footer.php');
?>