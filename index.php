<?php
	require 'includes/conf_db.inc.php';
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
			// Create connection //
			$connection = new mysqli($host, $login, $password, $dbname);
			// Check connection
			if ($connection->connect_error) {
			    die("Connection failed: " . $connection->connect_error);
			}

			$query = "SELECT * FROM users";
			$result = $connection->query($query);

			if ($result->num_rows > 0) {
    				// output data of each row
    				while($row = $result->fetch_assoc()) {
        				echo "Role: " . $row["role"]. "<br/> Login: " . $row["login"]. "<br/> Password: " . $row["pass"]. "<br/>";
    				}
			} else {
    				echo "0 results";
			}
		?>
	</body>

</html>
