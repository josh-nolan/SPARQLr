<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>SPARQLr | <?php echo $title;?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

		<link rel="stylesheet" href="<?php echo $siteURL;?>/css/normalize.min.css">
		<link rel="stylesheet" href="<?php echo $siteURL;?>/css/main.css">
		<link rel="stylesheet" href="<?php echo $siteURL;?>/css/codemirror.css">
		<link rel="stylesheet" href="<?php echo $siteURL;?>/css/sparql.css">
		<link rel="stylesheet" href="<?php echo $siteURL;?>/css/jquery.dataTables.css">
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />
		<link href="<?php echo $siteURL;?>/css/primitives.latest.css" media="screen" rel="stylesheet" type="text/css" />
		<script src="<?php echo $siteURL;?>/js/vendor/modernizr-2.6.1.min.js"></script>
		<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:300' rel='stylesheet' type='text/css'>
        

		<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
		<script type="text/javascript" src="http://code.jquery.com/ui/1.8.18/jquery-ui.min.js"></script>

        
		<script src="<?php echo $siteURL;?>/js/codemirror.js"></script>
		<script src="<?php echo $siteURL;?>/js/formatting.js"></script>
		<script src="<?php echo $siteURL;?>/js/sparql_parse.js"></script>
		<script src="<?php echo $siteURL;?>/js/sparql_execute.js"></script>
		<script src="<?php echo $siteURL;?>/js/jquery.dataTables.min.js"></script>
		
		<script type="text/javascript" src="<?php echo $siteURL;?>/js/primitives.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
        <![endif]-->
	<?php require_once('nav.php'); ?>