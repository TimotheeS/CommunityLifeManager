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
					$query = "SELECT user_name, user_forename FROM users WHERE login = '$login' AND pass = '$password'";
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
	} else if(isset($_SESSION['user_connected']) && $_SESSION['user_connected'] == true) {
		$return .= '<td class = "nb"> Connecté en tant que : </td>';
		$return .= '<td class = "nb">' .$_SESSION['user_forename'] .' ' .$_SESSION['user_name'] .'</td>';
		$return .= '<td class = "nb"> <form action="pages/log_out.php" method="POST"> <input type="submit" value="Se déconnecter" name="log_out"> </form> </td>';
	}
	$return .= '</tr>';
	$return .= '</table>';
	return $return;
}

/*-----------------------------------------------------------------------------*/

//fonction d'affichage du formulaire de création d'associations
function createOrganizationForm($connection) {
	$return = '<form action="#" method="post" enctype="multipart/form-data">';
	$return .= '<table>';
	$return .= '<tr>';
	$return .= '<td colspan=2> <h3> Créer mon association </h3> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> Nom de l\'assocation : </td> <td> <input type="text" name="org_name" style="width: 400px;" autocomplete="off"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> Description de l\'assocation : </td> <td> <textarea name="org_description" style="height: 80px; width: 400px;"> </textarea> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> Séléctionnez votre école : </td> <td> <select name="org_school" style="width: 400px;">';
	$return .= '<option value ="default" selected=selected> ... </option>';
	$query = "SELECT school_name FROM schools ORDER BY school_name";
	$result = $connection->query($query);
	while($row = $result->fetch_assoc()) {
		$return .= '<option value="' .$row['school_name'] .'">' .$row['school_name'] .'</option>';
	}
	$return .= '</select> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> Joindre le compte-rendu de l\'assemblée générale : </td>';
	$return .= '<td style="text-align: left;">';
	$return .= '<input id="real_button" hidden="hidden" type="file" name="org_report_upl"/>';
	$return .= '<button type="button" id="fake_button"> Choisir un fichier </button> <span id="fake_text"> Aucun fichier choisi </span> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> Je certifie être responsable de l\'association : </td>';
	$return .= '<td style="text-align: left;"> <input type="checkbox" style="height: 20px; width: 20px;" name="org_responsable"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> J\'accepte les conditions de la charte associative : </td>';
	$return .= '<td style="text-align: left;"> <input type="checkbox" style="height: 20px; width: 20px;" name="org_rules"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td colspan=2> <input type="submit" name="org_create" style="width: 100px;"> </td>';
	$return .= '</tr>';
	$return .= '</table>';
	$return .= '</form>';
	return $return;
}

//fonction de création d'associations
function createOrganization($connection) {
	$return = null;
	if(isset($_POST['org_create'])) {
		if(isset($_POST['org_name']) && $_POST['org_name'] != "") {
			$org_name = $_POST['org_name'];
			$query = "SELECT organization_name FROM organizations WHERE organization_name = '$org_name'";
			$result = $connection->query($query);
			if($result->num_rows == 0) {
				if(strlen($_POST['org_name']) < 50) {
					if(stringVerify($_POST['org_name'])) {

					} else {
						$return = 'Le nom de l\'association ne peut pas contenir de cractères spéciaux.';
					}
				} else {
					$return = 'Le nom de l\'assocation ne peut pas dépasser les 50 caractères.';
				}
			} else {
				$return = 'Ce nom d\'association est déja utilisé.';
			}
		} else {
			$return = 'Un nom d\'association est requis.';
		}
 	}
	$return = '<p>' .$return .'</p>';
	return $return;
}

function createSchoolForm(){
	$return = '<form action="#" method="post">';
	$return .= '<table>';
	$return .= '<tr>';
	$return .= '<td colspan=2> <h3> Inscrire une école </h3>';
	$return .= '</tr> <tr>';
	$return .= '<td> Nom de l\'école : </td> <td> <input type="text" style="width: 400px;" name="nomEcole"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> Adresse l\'école : </td> <td> <input type="text" style="width: 400px;" name="adresseEcole"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td colspan=2> <input type="submit" style="width: 80px;" name="submit" value="Inscrire">';
	$return .= '</table>';
	$return .= '</form>';
	return $return;
}

function createSchool($connection){
	if(isset($_POST['submit'])){
		$nomEcole = $_POST['nomEcole'];
		$adresseEcole = $_POST['adresseEcole'];
		$return = "";
		$query = "INSERT INTO schools (school_name, nb_students, nb_organization, adress) VALUES ('$nomEcole', 0, 0, '$adresseEcole')";
		$result = $connection->query($query);

		if ($result === TRUE) {
		    $return = "Ecole correctement inscrite";
		} else {
		    $return = "Error: " . $query . "<br>" . $connection->error;
		}
		return $return;
	}
}

?>
