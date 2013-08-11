<?php
	require_once('functions/global.php');
	
	if (isset($_GET['field']) && isset($_GET['value']) && isset($_GET['type']))
	{
		$type = $_GET['type'];
		$field = $_GET['field'];
		$value = $_GET['value'];
		$results = search($type, $field, $value);
	}
	
	$title = "Search";

	$fields['Classification'] = false;
	$fields['Description'] = false;
	$fields['Endpoint'] = false;	
	$fields['Tag'] = false;	
	$fields['Title'] = false;
	if (isset($fields[$field])) $fields[$field] = true;	
?>

<?php 
	require_once('header.php');
?>    
<section id="content">
	<div class="contentBody">
        <h1>Search: <?php echo "\"". $value ."\" in \"".$field."\"";?></h1>
        <form method="GET" action="search.php" id="searchBox" class="inputform">
        	<input type="radio" name="type" value="Query" <?php if ($type == "Query") echo "checked='checked'";?>> 
        	Query  <input type="radio" name="type" value="View" <?php if ($type == "View") echo "checked='checked'";?>> View <br/>
        	<label for="field">Search Field:</label>
        	<select name="field">
				<?php
				foreach ($fields as $k => $v)
				{
					if ($v) echo "<option selected>";
					else echo "<option>";
					echo $k."</option>";
				}
				?>
        	</select><br/>
        	<label for="value">Search Value:</label>
        	<input type="text" style="width: 300px;" name="value" id="value" value="<?php echo $value;?>"/><br/>
        	<input type="submit" value="Search" class="button"/>
        </form>
        </div>
        <hr/>
        <h2>Results</h2>
        <div class="tablesHolder">
        <?php
	        if (isset($results)) 
	        {
	        	foreach ($results as $q)
	        	{
	          		$q->outputModuleHtml();
	        	}
	        }
        ?> 
    	</div>
		<div class="clear"></div>
   
    </div>
</section>
<?php require_once('footer.php');