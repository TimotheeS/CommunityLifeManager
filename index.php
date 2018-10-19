<?php
	include('includes/display.inc.php');
	include('includes/functions.inc.php');
?>

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

		<?php
		db_connection();
		echo connectionForm();
		if(isset($_POST['connect'])) {
			if($_POST['login'] != "" && $_POST['password'] != "") {
				echo('<p style="color: red;"> Connexion. </p>');
			}
			else {
				echo('<p style="color: red;"> Veuillez renseigner vos identifiants svp. </p>');
			}
		}
		?>
	</body>

</html>
