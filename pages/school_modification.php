<?php
	include('../includes/display.inc.php');
	include('../includes/functions.inc.php');
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

		<h1> MODIFIEZ LES INFORMATIONS DE VOTRE ÉCOLE  </h1>

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
			echo alterSchoolForm();
            if (isset($_POST['chooseSchool']) AND isset($_POST['schoolSelect'])) {
                $id=$_POST['schoolSelect'];
                header("Refresh: 0.1;url=modify_school.php?id=$id");
            }
            if (isset($_POST['Modifier'])) {
                echo alterSchool();
            }
			?>
            <form method="POST">
                <input type="submit" name="retour" value="Retour">
            </form>
            <?php
                if(isset($_POST['retour'])) header("Location: ../index.php");
            ?>

		</section>
	</body>
</html>
