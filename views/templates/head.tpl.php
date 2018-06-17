<!DOCTYPE html>
<html>
	<head>
		<title><?=$title?> | HGit</title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
        <meta name="description" content="" />
        <meta name="author" content="matthias.lantsch">
        <meta name="robots" content="index, follow"/>
        <meta name="revisit-after" content="2 month"/>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" type="text/css" media="all" />
    	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	    <!--[if lt IE 9]>
	      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	    <![endif]-->
 		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"
			integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
		<!-- font-awesome icons -->
		<link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
  		<link type="text/css" rel="stylesheet" href="<?=linkCss("default")?>"/>
   		<link type="text/css" rel="stylesheet" href="<?=linkCss("highlight")?>"/>
		<script>
			function returnFWAlias() {
				return "<?=linkTo()?>";
			}
		</script>
	</head>
	<body>
