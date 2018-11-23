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
			echo createOrganization($connection);
			?>
		</section>

		<aside>
			<?php
			echo connectionForm();
			echo connection($connection);
			?>
		</aside>
		<script src="includes/style_uploadMeetingReport.js"> </script>
	</body>
</html>
