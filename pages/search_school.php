<?php
include('../includes/display.inc.php');
include('../includes/functions.inc.php');

session_start();
?>

<!DOCTYPE html>
<html lang = "fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="../styles/style.css" />
	<script type="text/javascript" src="../includes/js/jquery.min.js"></script>
	<script type="text/javascript" src="../includes/js/script.js"></script>
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
		<?php
		echo searchSchoolForm();
		if (isset($_POST['search']) && $_POST['toSearch'] != "") {
			echo search_school();
		}

		if (isset($_POST['back'])) {
			header('Location: ../index.php');
		}
		echo displayBackBtn();
		?>
	</section>

</body>
</html>
