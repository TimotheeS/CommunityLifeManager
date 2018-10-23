<?php
	include('includes/display.inc.php');
	include('includes/functions.inc.php');
	require 'includes/conf_db.inc.php';
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

		require 'includes/conf_db.inc.php';
		//création de la connexion
		$connection = new mysqli($host, $login, $password, $dbname);
		//vérification de la connexion
		if ($connection->connect_error) {
		    die("Connection failed: " . $connection->connect_error);
		}

		if ($connection->connect_errno) {
    			printf("Connect failed: %s\n", $connection->connect_error);
    			exit();
		}

		echo connectionForm();

		if(isset($_POST['connect'])) {
			if($_POST['login'] != "" && $_POST['password'] != "") {
				$login = $_POST['login'];
				$password = $_POST['password'];
				//$query = "SELECT * FROM users";
				$query = "SELECT * FROM users WHERE login = '$login' AND pass = '$password'";
				$result = $connection->query($query);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
				  		echo('<p style="color: red;"> Identifiants corrects. </p>');
					}
				} else {
					echo('<p style="color: red;"> Identifiants incorrects. </p>');
				}
				$connection->close();
			}
			else {
				echo('<p style="color: red;"> Veuillez renseigner vos identifiants svp. </p>');
			}
		}
		?>
	</body>

</html>
