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
			if (isset($_POST['log_in'])) {
				echo connection($connection);
			}
			?>
		</aside>

		<section>
			<?php
			echo createSchoolForm("", "", "", "", "<h3> Inscrire une école </h3>", 'Pour modifier les informations de votre école, <a href="pages/modify_school.php"> Cliquez ici </a>', "Inscrire");
			echo createSchool($connection);
			?>
		</section>
	</body>
</html>
