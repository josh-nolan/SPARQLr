<?php
require_once('functions/global.php');
if (isset($_GET['function']))
{
	switch($_GET['function'])
	{
		case 'reset':
			triplestoreReset();
			break;

		case 'drop':
			if (isset($_GET['title']))
			{
				dropTriplesDataFromStore($_GET['title']);
			}
			break;

		case 'insert':
			if (isset($_GET['title']))
			{
				"trying to insert:". $_GET['title']."<hr/>";
				insertTriplesToStore($_GET['title']);
			}
			break;
		case 'deletefile':
			if (isset($_GET['title']))
			{
				deleteFile($_GET['title']);
			}
			break;
	}



	if(isset($_GET['callback']))
	{
		header("location: ".$_GET['callback']);	
	}
	else
	{
		header("location: index.php");
	}
}


?>