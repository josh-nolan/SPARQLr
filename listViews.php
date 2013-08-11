<?php
	require_once('functions/global.php');
	$title = "Views";
	require_once('header.php');
?>
<section id="content">
    <div class="contentBody">
    	<h1>Views</h1>
        <p>Below are the queries that have been materialized as views with SPARQLr's triplestore.</p>
   		<div class="tablesHolder">
        <?php
			$queries = getQueries("View");
	        foreach ($queries as $q)
	        {
	          $q->outputModuleViewHtml();
	        }
        ?> 
    	</div>
		<div class="clear"></div>
    </div>
    <script>
		var MAX_LINKS = 2;
		var selected = 0;
		var queries = new Array();
		$(document).ready(function(){
			$('.buttonHolder .join').click(function ()
			{
				$('#joinlink').show();
      			if (selected == 0)
				{
					$('#links').show('slow');	
				}
				if (selected < MAX_LINKS)
				{
					$(this).parent().parent().addClass('selected');
					$(this).css("display", "none");
					$('#linksList').append("<li>" + $(this).attr('id') + "</li>");
					queries[selected] = $(this).attr('id');
					selected++;
				}
				else
				{
					alert("You can only select " + MAX_LINKS + " queries to link!");
				}
				if (selected == 2)
				{
					var string = "?id[]=" + queries[0];
					for (var i = 1; i < MAX_LINKS; i++)
					{
						string += "&id[]=" + queries[i];
					}
					$('#joinlink').attr("href", "joinTables.php" + string);
					$('#joinlink').show();
				}
    		});
			$('#joinlink').hide();
			$('#links').hide();
		});
	</script>
</section>
<?php 
	require_once('footer.php');
?>