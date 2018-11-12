<?php
	include('includes/display.inc.php');
	include('includes/functions.inc.php');

	session_start();
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
		$connection = db_connection();

		?>

		<div id = "banner">
			<?php
				echo sessionInformation();
			?>
		</div>

		<section>
			<?php
			echo createOrganizationForm($connection);
			?>
		</section>

		<section>
			<?php
			echo createSchoolForm($connection);
			echo createSchool($connection);
			?>
		</section>

		<aside>
			<?php
			echo connectionForm();
			if (isset($_POST['submit'])) {
				echo connection($connection);
			}
			?>
		</aside>
	</body>

</html>
