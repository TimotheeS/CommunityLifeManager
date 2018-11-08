<?php

/*-----------------------------------------------------------------------------*/

//fonction de connexion à la base de données
function db_connection() {
	require 'conf_db.inc.php';
	//création de la connexion
	$connection = new mysqli($host, $login, $password, $dbname);
	//vérification de la connexion
	if ($connection->connect_error) {
	    die("Connection failed: " . $connection->connect_error);
	}
	return $connection;
}

/*-----------------------------------------------------------------------------*/

//fonction d'affichage du formulaire de connexion
function connectionForm() {
	$return = '<form action = "#" method = "POST">';
	$return .= '<table class = "centered bordered" style="margin-top: 10px;">';
	$return .= '<tr>';
	$return .= '<td class="nb" colspan=2> <h3> Connectez-vous </h3> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> Identifiant : </td>';
	$return .= '<td> <input type="text" name="login" autocomplete="off"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> Mot de passe : </td>';
	$return .= '<td> <input type="password" name="password" autocomplete="off"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td class="nb" colspan=2> <input type="submit" value="Se connecter" name="log_in"> </td>';
	$return .= '</tr>';
	$return .= '</table>';
	$return .= '</form>';
	return $return;
}

//fonction de vérification des identifiants/mots de passe dans la base de données
function connection($connection) {
	$return = null;
	if(isset($_POST['log_in'])) {
		if($_POST['login'] != "" && $_POST['password'] != "") {
			$login = $_POST['login'];
			$password = $_POST['password'];
			$query = "SELECT * FROM users WHERE login = '$login' AND pass = '$password'";
			$result = $connection->query($query);
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$return = '<p style="color: red;"> Identifiants corrects. </p>';
					session_start();
					$_SESSION['user_connected'] = true;
	                    $_SESSION['user_login'] = $login;
					$query = "SELECT useuser_name, user_forename FROM users WHERE login = '$login' AND pass = '$password'";
					$result = $connection->query($query);
					while($row = $result->fetch_assoc()) {
						$_SESSION['user_name'] = $row['user_name'];
						$_SESSION['user_forename'] = $row['user_forename'];
					}
					header('location: index.php');
				}
			} else {
				$return = '<p style="color: red;"> Identifiants incorrects. </p>';
			}
			$connection->close();
		}
		else {
			if($_POST['login'] == "" && $_POST['password'] != "") {
				$return = '<p style="color: red;"> Veuillez renseigner votre identifiant svp. </p>';
			}
			else if($_POST['login'] != "" && $_POST['password'] == "") {
				$return = '<p style="color: red;"> Veuillez renseigner votre mot de passe svp. </p>';
			}
			else {
				$return = '<p style="color: red;"> Veuillez renseigner votre identifiant et votre mot de passe svp. </p>';
			}
		}
	}
	$return = '<table class = "centered"> <tr> <td>' .$return .'</td> </tr> </table>';
	return $return;
}

function sessionInformation() {
	$return = '<table>';
	$return .= '<tr>';
	if(!isset($_SESSION['user_connected'])) {
		$return .= '<td class = "nb"> Vous n\'etes pas connecté. </td>';
	} else if(isset($_SESSION['user_connected']) && $_SESSION['user_connected'] = true) {
		$return .= '<td class = "nb"> Connecté en tant que : </td>';
		$return .= '<td class = "nb">' .$_SESSION['user_forename'] .' ' .$_SESSION['user_name'] .'</td>';
		$return .= '<td class = "nb"> <form action="pages/log_out.php" method="POST"> <input type="submit" value="Se déconnecter" name="log_out"> </form> </td>';
	}
	$return .= '</tr>';
	$return .= '</table>';
	return $return;
}

/*-----------------------------------------------------------------------------*/

function createOrganizationForm() {
	$return = '<form action="#" method="post">';
	$return .= '<table>';
	$return .= '<tr>';
	$return .= '<td colspan=2> <h3> Créer mon association </h3> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> Nom de l\'assocation : </td> <td> <input type="text" style="width: 400px;"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> Description de l\'assocation : </td> <td> <textarea style="height: 80px; width: 400px;"> </textarea> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> Séléctionnez votre école </td> <td> <select value="" name="school" style="width: 400px;"> </td>';
	$return .= '</tr>';
	$return .= '</table>';
	$return .= '</form>';
	return $return;
}

?>
