<?php

/*--------------------------------------------------------------------------------------------------------------*/

//fonction de connexion à la base de données
function db_connection() {
	require 'conf_db.inc.php';
	//création de la connexion
	$connection = new mysqli($host, $login, $password, $dbname, $port);
	//vérification de la connexion
	if ($connection->connect_error) {
	    die("Connection failed: " . $connection->connect_error);
	}
	return $connection;
}

/*--------------------------------------------------------------------------------------------------------------*/

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
function connection() {
	$connection = db_connection();
	$return = null;
	if(isset($_POST['log_in'])) {
		if($_POST['login'] != "" && $_POST['password'] != "") {
			$login = $_POST['login'];
			$password = $_POST['password'];
			$query = "SELECT * FROM users WHERE login = '$login' AND pass = '$password'";
			$result = $connection->query($query);
			if($result->num_rows > 0) {
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
					header('location: ../index.php');
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

//fonction d'affichage d'informations sur la session en cours
function sessionInformation($c_page='default') {
	$return = '<table>';
	$return .= '<tr>';
	if(!isset($_SESSION['user_connected'])) {
		$return .= '<td class = "nb"> Vous n\'etes pas connecté. </td>';
	} else if(isset($_SESSION['user_connected']) && $_SESSION['user_connected'] == true) {
		$return .= '<td class = "nb"> Connecté en tant que : </td>';
		$return .= '<td class = "nb">' .$_SESSION['user_forename'] .' ' .$_SESSION['user_name'] .'</td>';
		if($c_page == 'index')
			$return .= '<td class = "nb"> <form action="pages/user_log_out.php" method="POST"> <input type="submit" value="Se déconnecter" name="log_out"> </form> </td>';
		elseif($c_page == 'default')
			$return .= '<td class = "nb"> <form action="user_log_out.php" method="POST"> <input type="submit" value="Se déconnecter" name="log_out"> </form> </td>';
	}
	$return .= '</tr>';
	$return .= '</table>';
	return $return;
}

/*-----------------------------------------------------------------------------*/

//fonction d'affichage du formulaire de création d'associations
function createOrganizationForm() {
	$connection = db_connection();
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
	$connection->close();
	return $return;
}

//fonction de création d'associations
function createOrganization() {
	$connection = db_connection();
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
			$connection->close();
		} else {
			$return = 'Un nom d\'association est requis.';
		}
 	}
	$return = '<p>' .$return .'</p>';
	return $return;
}

/*--------------------------------------------------------------------------------------------------------------*/

//fonction de validation d'une chaîne de caractères
function stringVerify($string) {
	$not_allowed = array("\\", "/", ":", ";", ",", "*", "?", "\"", ">", "<", "|", ".");
    	$count = count($not_allowed);

    	for($i = 0; $i<$count; $i++){
     	$pos = strpos($string, $not_allowed[$i]);
		if($pos === false) {
			$verified = true;
		} else {
			$verified = false;
			return $verified;
		}
	}
	return $verified;
}

/*--------------------------------------------------------------------------------------------------------------*/

function createSchoolForm($nom="", $codePostal="", $ville="", $adress="", $titre="", $complement='', $nomBtn=""){
	$return = '<form action="#" method="post">';
	$return .= $titre;
	$return .= '<table>';
	$return .= '<tr>';
	$return .= '<td colspan=2 style="text-align: left"> Nom de l\'école : </td> <td colspan=2> <input type="text" style="width: 400px;" name="nomEcole" value="'.$nom.'"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> Code postal : </td> <td> <input type="text" style="width: 50px;" name="codePostal" value="'.$codePostal.'"> </td>';
	$return .= '<td style="text-align: left"> Ville : </td> <td style="text-align: right"> <input type="text" style="width: 330px;" name="villeEcole" value="'.$ville.'"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td colspan=2 style="text-align: left"> Adresse : </td> <td colspan=2> <input type="text" style="width: 400px;" name="adresseEcole" value="'.$adress.'"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td colspan=4> <input type="submit" style="width: 80px;" name="'.$nomBtn.'" value="'.$nomBtn.'">';
	$return .= '</tr> <tr>';
	$return .= '</table>';
	$return .= $complement;
	$return .= '</form>';

	return $return;
}

function createSchool() {
	$connection = db_connection();
	if(isset($_POST['Inscrire'])){
		$nomEcole = $_POST['nomEcole'];
		$codePostal = $_POST['codePostal'];
		$ville = $_POST['villeEcole'];
		$adresseEcole = $_POST['adresseEcole'];
		$return = "";
		$query = "INSERT INTO schools (school_name, nb_students, nb_organization, adress, code_postal, ville) VALUES ('$nomEcole', 0, 0, '$adresseEcole', '$codePostal', '$ville')";
		$result = $connection->query($query);

		if ($result === TRUE) {
		    $return = "Ecole correctement inscrite";
		} else {
		    $return = "Error: " . $query . "<br>" . $connection->error;
		}
		$connection->close();
		return $return;
	}
}

function alterSchoolForm() {
	$connection = db_connection();
	$return = '<form method="POST">';
	$return .= '<table>';
	$return .= '<tr>';
	$return .= '<td> <h3> Modifiez les informations de votre école </h3> </td>';
	$return .= '</tr><tr>';
	$return .= '<td> <select name="schoolSelect">';
		$return .= '<option value="" disabled selected hidden> Choisissez une école </option>';
		$query = "SELECT school_id,school_name FROM schools";
		$result = $connection->query($query);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$return .= '<option value="'.$row["school_id"];
				if (isset($_GET['id']) AND $row["school_id"] == $_GET['id']) {
					$return .= '" selected="selected';
				}
				$return .= '"> '.$row["school_name"].'</option>';
		    }
		}
	$return .= '</select></td>';
	$return .= '<td> <input type="submit" style="width: 80px;" name="chooseSchool" value="Valider"> </td>';
	$return .= '</tr>';
	if(isset($_GET['id'])){
		$id = $_GET['id'];
		$query = "SELECT school_name,adress,code_postal,ville FROM schools WHERE school_id='$id'";
		$result = $connection->query($query);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
				$return .= createSchoolForm($row["school_name"], $row["code_postal"], $row["ville"], $row["adress"], "", "", "Modifier");
			}
		}
	} else {
		$return .= createSchoolForm("", "", "", "", "", "", "Modifier");
	}
	$return .= '</table>';
	$return .= '</form>';

	return $return;
}

function alterSchool() {
	$connection = db_connection();
	if (isset($_POST['Modifier'])) {
		$nomEcole = $_POST['nomEcole'];
		$codePostal = $_POST['codePostal'];
		$ville = $_POST['villeEcole'];
		$adresseEcole = $_POST['adresseEcole'];
		$return = "";
		$id = $_GET['id'];

		$query = "UPDATE schools SET school_name = '$nomEcole', adress = '$adresseEcole', code_postal = '$codePostal', ville = '$ville' WHERE school_id = '$id' ";
		$result = $connection->query($query);

		if ($result === TRUE) {
		    $return = "Informations correctement modifiées";
		} else {
		    $return = "Error: " . $query . "<br>" . $connection->error;
		}
		return $return;
	}
}

function orgaPage() {
	$connection = db_connection();
	$id = $_SESSION['orgId'];
	$query = "SELECT * FROM members INNER JOIN organizations ON members.organization_id = '$id' AND organizations.organization_id = '$id'";
	$result = $connection->query($query);
	$return = '<table style="border-bottom:1px solid black;">';
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$return .= '<tr>';
		$return .= '<td> <h2> '.$row['organization_name'].'</h2> </td>';
		$return .= '</tr> <tr>';
		$return .= '<td> Membres dans l\'association '.$row['nb_members'].'</td>';
		$return .= '</tr> </table>';

		$return .= '<table style="border-bottom:1px solid black;">';
		$return .= '<tr>';
		$return .= '<td colspan=3> <h3> Membres de l\'association </h3> </td>';
		$return .= '</tr> <tr style="border-bottom:1px solid black;">';
		$return .= '<td> Prénom </td> <td> Nom </td> <td> Rôle </td>';
		$return .= '</tr>';
		$return .= '<tr>';
			$return .= '<td>'.$row['member_forename'].'</td>';
			$return .= '<td>'.$row['member_name'].'</td>';
			$return .= '<td>'.$row['member_role'].'</td>';
		$return .= '</tr>';

		while($row = $result->fetch_assoc()) {
			$return .= '<tr>';
			$return .= '<td>'.$row['member_forename'].'</td>';
			$return .= '<td>'.$row['member_name'].'</td>';
			$return .= '<td>'.$row['member_role'].'</td>';
			$return .= '</tr>';
		}
	} else {
		$query = "SELECT * FROM organizations WHERE organization_id = '$id'";
		$result = $connection->query($query);
		$row = $result->fetch_assoc();
		$return .= '<td> <h2> '.$row['organization_name'].'</h2> </td>';
		$return .= '</tr> <tr>';
		$return .= '<td> Membres dans l\'association '.$row['nb_members'].'</td>';
		$return .= '</tr> </table>';

		$return .= '<table style="border-bottom:1px solid black;">';
		$return .= '<tr>';
		$return .= '<td colspan=3> <h3> Membres de l\'association </h3> </td>';
		$return .= '</tr> <tr style="border-bottom:1px solid black;">';
		$return .= '<td> Prénom </td> <td> Nom </td> <td> Rôle </td>';
		$return .= '</tr> <tr>';
		$return .= '<td colspan=3> Aucun membres renseignés </td>';
		$return .= '</tr>';
	}
	$return .= '</table>';

	$return .= '<form method="POST">';
	$return .= '<input type="submit" name="gérer" value="Gérer l\'association">';
	$return .= '</form>';

	return $return;
}

function orgManagement() {
	$connection = db_connection();
	$id = $_SESSION['orgId'];
	$query = "SELECT * FROM members INNER JOIN organizations ON members.organization_id = organizations.organization_id WHERE members.organization_id = 1";
	$result = $connection->query($query);
	$return = '<form action=';

	if (isset($_POST['addMember'])) {
		$return .= '"#addMember" method="POST">';
	} else {
		$return .= '"#tableau" method="POST">';
	}
	$return .= '<table style="border-bottom:1px solid black;">';
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$return .= '<tr>';
		$return .= '<td> <h2> '.$row['organization_name'].'</h2> </td>';
		$return .= '</tr> <tr>';
		$return .= '<td> Membres dans l\'association '.$row['nb_members'].'</td>';
		$return .= '</tr> </table>';

		$return .= '<table id="tableau">';
		$return .= '<tr>';
		$return .= '<td colspan=5> <h3> Membres de l\'association </h3> </td>';
		$return .= '</tr> <tr style="border-bottom:1px solid black;">';
		$return .= '<td> Prénom </td> <td> Nom </td> <td> Rôle </td> <td> </td> <td> </td>';
		$return .= '</tr>';
		$return .= '<tr>';
			$return .= '<td>'.$row['member_forename'].'</td>';
			$return .= '<td>'.$row['member_name'].'</td>';
			$return .= '<td>'.$row['member_role'].'</td>';
			$return .= '<td> <input type="submit" name="modify'.$row['member_id'].'" value="Modifier"> </td>';
			$return .= '<td> <input type="submit" name="delete'.$row['member_id'].'" value="Supprimer"> </td>';
		$return .= '</tr>';

		while($row = $result->fetch_assoc()) {
			$return .= '<tr>';
			$return .= '<td>'.$row['member_forename'].'</td>';
			$return .= '<td>'.$row['member_name'].'</td>';
			$return .= '<td>'.$row['member_role'].'</td>';
			$return .= '<td> <input type="submit" name="modify'.$row['member_id'].'" value="Modifier"> </td>';
			$return .= '<td> <input type="submit" name="delete'.$row['member_id'].'" value="Supprimer"> </td>';
			$return .= '</tr>';
		}
	} else {
		$query = "SELECT * FROM organizations WHERE organization_id = '$id'";
		$result = $connection->query($query);
		$row = $result->fetch_assoc();
		$return .= '<td> <h2> '.$row['organization_name'].'</h2> </td>';
		$return .= '</tr> <tr>';
		$return .= '<td> Membres dans l\'association '.$row['nb_members'].'</td>';
		$return .= '</tr> </table>';

		$return .= '<table style="border-bottom:1px solid black;">';
		$return .= '<tr>';
		$return .= '<td colspan=3> <h3> Membres de l\'association </h3> </td>';
		$return .= '</tr> <tr style="border-bottom:1px solid black;">';
		$return .= '<td> Prénom </td> <td> Nom </td> <td> Rôle </td>';
		$return .= '</tr> <tr>';
		$return .= '<td colspan=3> Aucun membres renseignés </td>';
		$return .= '</tr>';
	}
	$return .= '<tr style="border-top:1px solid black;">';
	$return .= '<td colspan=5> <input type="submit" name="addMembers" value="Ajouter des membres"> </td>';
	$return .= '</tr>';
	$return .= '</table>';
	$return .= '</form>';

	return $return;
}

function addMembersForm($forename = "", $name = "", $role = "", $btn = "addMemberValidate", $value = "Ajouter") {
	$return = '<form action="#tableau" method="POST">';
	$return .= '<table id="addMember">';
	$return .= '<tr style="border-bottom:1px solid black;">';
	$return .= '<td> Prénom </td> <td> Nom </td> <td> Rôle </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> <input type="text" name="forename" value="'.$forename.'"> </td>';
	$return .= '<td> <input type="text" name="name" value="'.$name.'"> </td>';
	$return .= '<td> <input type="text" name="role" value="'.$role.'"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td colspan=3> <input type="submit" name="'.$btn.'" value="'.$value.'"> </td>';
	$return .= '</tr>';
	$return .= '</table>';
	$return .= '</form>';

	return $return;
}

function addMember() {
	$connection = db_connection();
	$name = $_POST['name'];
	$forename = $_POST['forename'];
	$role = $_POST['role'];
	$return = "";
	$id = $_SESSION['orgId'];

	$query = "INSERT INTO members (member_forename, member_name, member_role, organization_id) VALUES ('$forename', '$name', '$role', '$id')";
	$result = $connection->query($query);

	if ($result !== TRUE) {
	    $return = "Error: " . $query . "<br>" . $connection->error;
	}
	$connection->close();
	return $return;
}

function orgManagementDelete() {
	$connection = db_connection();
	$id = 1;
	$query = "SELECT * FROM members WHERE organization_id = '$id'";
	$result = $connection->query($query);
	if($result->num_rows > 0) {
		while($row = $result->fetch_assoc()){
			if (isset($_POST["delete".$row['member_id']])) {
				$memberID = $row['member_id'];
				$query = "DELETE FROM members WHERE member_id = '$memberID'";
				$result = $connection->query($query);
				header("Refresh: 0;url=org_management.php#tableau");
				exit();
			}
		}
	}
}

function orgManagementModify() {
	$connection = db_connection();
	$id = $_SESSION['orgId'];
	$query = "SELECT * FROM members WHERE organization_id = '$id'";
	$result = $connection->query($query);
	$return = "";
	if($result->num_rows > 0) {
		while($row = $result->fetch_assoc()){
			if (isset($_POST["modify".$row['member_id']])) {
				$forename = $row['member_forename'];
				$name = $row['member_name'];
				$role = $row['member_role'];
				$_SESSION['memberID'] = $row['member_id'];
				$return = addMembersForm($forename, $name, $role, "modifyMember", "Modifier");

			}
		}
	}
	return $return;
}

function org_modify(){
	$connection = db_connection();
	$forename = $_POST['forename'];
	$name = $_POST['name'];
	$role = $_POST['role'];
	$return = "";
	$orgId = $_SESSION['orgId'];
	$id = $_SESSION['memberID'];

	$query = "UPDATE members SET member_forename = '$forename', member_name = '$name', member_role = '$role', organization_id = '$orgId' WHERE member_id = '$id' ";
	$result = $connection->query($query);

	if ($result !== TRUE) {
	    $return = "Error: " . $query . "<br>" . $connection->error;
	}
	return $return;
}

function orgListForm() {
	$connection = db_connection();
	$id = $_SESSION['schoolId'];
	$query = "SELECT * FROM organizations INNER JOIN schools ON schools.school_id = organizations.school_id WHERE organizations.school_id = '$id'";
	$result = $connection->query($query);
	$return = '<table style="border-bottom:1px solid black;">';
	$return .= '<tr>';

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$return .= '<td> <h2> '.$row['school_name'].'</h2> </td>';
		$return .= '</tr> </table>';
		$return = '<form action="#" method="POST">';
		$return .= '<table> <tr>';
		$return .= '<td>'.$result->num_rows.' Associations trouvées</td>';
		$return .= '</tr> </table>';
		$return .= '<table style="border-bottom:1px solid black;">';
		$return .= '<tr style="border-bottom:1px solid black;">';
		$return .= '<td colspan=2> <h3> Associations </h3>  </td>';
		$return .= '</tr> <tr>';
		$return .= '<td>'.$row['organization_name'].'</td>';
		$return .= '<td> <input type="submit" name="org'.$row['organization_id'].'" value="Plus d\'informations"> </td>';
		$return .= '</tr>';
	} else {
		$query = "SELECT * FROM schools WHERE school_id='$id'";
		$result = $connection->query($query);
		$row = $result->fetch_assoc();
		$return .= '<td> <h2> '.$row['school_name'].'</h2> </td>';
		$return .= '</tr> <tr>';
		$return .= '<td> <h3> Aucun asociation renseignée pour cette école </h3> </td>';
		$return .= '</tr> </table>';

		return $return;
	}

	while($row = $result->fetch_assoc()) {
		$return .= '<tr>';
		$return .= '<td>'.$row['organization_name'].'</td>';
		$return .= '<td> <input type="submit" name="org'.$row['organization_id'].'" value="Plus d\'informations"> </td>';
		$return .= '</tr>';
	}
	$return .= '</table>';
	$return .= '</form>';

	return $return;
}

function orgList() {
	$connection = db_connection();
	$id = 1;
	$query = "SELECT * FROM organizations WHERE school_id = '$id'";
	$result = $connection->query($query);
	if($result->num_rows > 0) {
		for ($i=1; $i <= $result->num_rows; $i++) {
			if (isset($_POST["org$i"])) {
				$_SESSION['orgId'] = $i;
				header("Location: org_vizualisation.php");
			}
		}
	}
}

function backBtn(){
	$return = '<form method="POST">';
	$return .= '<input type="submit" name="back" value="Retour">';
	$return .= '</form>';

	return $return;
}
function searchSchoolForm(){
	$return = '<form action="#" method="POST" style="margin-bottom:300px">';
	$return .= '<table>';
	$return .= '<tr>';
	$return .= '<td colspan=2> <h3> Trouver votre école : </h3> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> <input type="text" id="school_id" onkeyup="autocomplet()" name="toSearch"> <ul id="school_list_id"></ul> </td> </td>';
	$return .= '<td> <input type="submit" name="search" value="Rechercher"> </td>';
	$return .= '</tr>';
	$return .= '</table>';
	$return .= '</form>';

	return $return;
}

function search_school(){
	if (isset($_POST['search']) && $_POST['toSearch'] != "") {
		$connection = db_connection();
		$toSearch = $_POST['toSearch'];
		$query = "SELECT * FROM schools WHERE school_name = '$toSearch'";
		$result = $connection->query($query);
		if($result->num_rows > 0){
			while ($row = $result->fetch_assoc()) {
				$_SESSION['schoolId'] = $row['school_id'];
				header('Location: org_list.php');
			}
		}	else {
			$return = "L'école recherchée n'existe pas";
		}
	}
	return $return;
}

?>
