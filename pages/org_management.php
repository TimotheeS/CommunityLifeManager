<?php
	include('../includes/display.inc.php');
	include('../includes/functions.inc.php');

	session_start();
?>

<!DOCTYPE html>
<html lang = "fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" type="text/css" href="../styles/style.css"/>
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
			echo orgManagement();
            if (isset($_POST['addMembers'])) {
                echo addMembersForm();
            }
            if (isset($_POST['addMemberValidate'])) {
                echo addMember();
                header('Refresh: 0;url=org_management#tableau');
            }
            echo orgManagementModify();
            if (isset($_POST['modifyMember'])) {
                echo org_modify();
                header('Refresh: 0;url=org_management#tableau');
            }

            orgManagementDelete();

            echo backBtn();
            if (isset($_POST['back'])) {
                header('Location: org_vizualisation.php');
            }

			?>
		</section>
	</body>
</html>
--
