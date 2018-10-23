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

?>
