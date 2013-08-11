<?php
	// get sql code from url
	if (isset($_GET['sql']))
	{
		$sql = urldecode($_GET['sql']);	
		require_once('dbconnect.php');
		
		// lets execute the bad boy
		$results = mysql_query($sql);
		$printedHead = false;
		$num_rows = mysql_num_rows($results);
		if ($num_rows != 0)
		{
			echo "<table>";
			while ($row = mysql_fetch_array($results, MYSQL_ASSOC))
			{
				if(!$printedHead)
				{
					echo "<thead><tr>";
					foreach ($row as $key => $val)
					{
						echo "<td>" + $key + "</td>";
					}
					echo "</thead></tr>";
					echo "<tbody>";
					$printedHead = true;	
				}
				
				echo "<tr>";
				foreach ($row as $cell)
				{
					echo "<td>".$cell."</td>";	
				}
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";
		}
		else
		{
			echo "No results, sorry, try another join.";	
		}
	}
	else
	{
		echo "SQL no provided";
	}
?>