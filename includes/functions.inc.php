<?php

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
	$return .= '<table>';
	$return .= '<tr>';
	$return .= '<td colspan=2 style = "text-align: center;"> Connectez-vous </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> Identifiant : </td>';
	$return .= '<td> <input type="text" name="login"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> Mot de passe : </td>';
	$return .= '<td> <input type="password" name="password"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td colspan=2 style = "text-align: center;"> <input type="submit" value="Se connecter" name="connect"> </td>';
	$return .= '</tr>';
	$return .= '</table>';
	$return .= '</form>';
	return $return;
}

//fonction de vérification des identifiants/mots de passe dans la base de données
function connection($connection) {
	$return = null;
	if(isset($_POST['connect'])) {
		if($_POST['login'] != "" && $_POST['password'] != "") {
			$login = $_POST['login'];
			$password = $_POST['password'];
			$query = "SELECT * FROM users WHERE login = '$login' AND pass = '$password'";
			$result = $connection->query($query);
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$return = '<p style="color: red;"> Identifiants corrects. </p>';
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
	return $return;
}

?>
