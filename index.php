<?php
	include('includes/display.inc.php');
	include('includes/functions.inc.php');

	session_start();
?>

<!DOCTYPE html>
<html lang = "fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" type="text/css" href="styles/style.css"/>
		<title> Community Life Manager </title>
	</head>

	<body>
		<header>
		</header>

		<h1> COMMUNITY LIFE MANAGER </h1>

		<div id = "banner">
			<?php
			echo sessionInformation();
			?>
		</div>

		<section>
			<a href="pages/user_connection.php"> Se connecter </a> <br/>
			<a href="pages/school_creation.php"> Inscrire son école </a> <br/>
			<a href="pages/school_modification.php"> Modifier son école </a> <br/>
			<a href="pages/org_creation.php"> Inscrire son association </a> <br/>
		</section>
	</body>
</html>
