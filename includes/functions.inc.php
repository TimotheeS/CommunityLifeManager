<?php

/*--------------------------------------------------------------------------------------------------------------*/
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
/*--------------------------------------------------------------------------------------------------------------*/

function signInChoiceForm() {
    $return = null;
    $return = '<form action="#" method="POST">';
    $return .= '<table>';
    $return .= '<tr>';
    $return .= '<td> <h3> Créer un compte </h3>';
    $return .= '</tr> <tr>';
    $return .= '<td> Sélectionner le profil à créer : </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> <input type="radio" name="user_choice" value="1"> Utilisateur </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> <input type="radio" name="user_choice" value="2"> Manager VA </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> <input type="radio" name="user_choice" value="3"> Admin CML </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> <input type="submit" name="user_next" value="Suivant">';
    $return .= '</tr>';
    $return .= '</table>';
    $return .= '</form>';
    return $return;
}

function signInChoice() {
    if(isset($_POST['user_next'])) {
        if(isset($_POST['user_choice'])) {
            $user_choice = $_POST['user_choice'];
            if($user_choice == 1)
                header('location: user_sign_in.php?account=1');
            if($user_choice == 2)
                header('location: user_sign_in.php?account=2');
            if($user_choice == 3)
                header('location: user_sign_in.php?account=3');
        } else {
            return '<p style="color: red"> Veuillez séléctionner le type de compte à créer svp </p>';
        }
    }
}

function signInForm() {
    $return = '<form action="#" method="POST">';
    $return .= '<table>';
    $return .= '<tr>';
    $user_role = $_GET['account'];
    if($user_role == 1)
        $return .= '<td colspan=2> <h3> Créer un compte utilisateur : </h3>';
    else if($user_role == 2)
        $return .= '<td colspan=2> <h3> Créer un compte manager VA : </h3>';
    else if($user_role == 3)
        $return .= '<td colspan=2> <h3> Créer un compte administrateur CLM : </h3>';
    $return .= '</tr> <tr>';
    $return .= '<td> Nom : </td> <td> <input type="text" name="user_name"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Prénom </td> <td> <input type="text" name="user_forename"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Identifiant </td> <td> <input type="text" name="user_log"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Mot de passe </td> <td> <input type="password" name="user_pass1"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Mot de passe </td> <td> <input type="password" name="user_pass2"> </td>';
    $return .= '</tr> <tr>';
    if($user_role != 3) {
        $connection = db_connection();
        $query = "SELECT school_id, school_name FROM schools ORDER BY school_name";
        $results = $connection->query($query);
        $return .= '<td> Sélectionnez votre école </td>';
        $return .= '<td> <select name="user_school">';
        $return .= '<option value="" disabled selected hidden> Choisissez une école </option>';
        while($row = $results->fetch_assoc()) {
    		$return .= '<option value="' .$row['school_id'] .'">' .$row['school_name'] .'</option>';
    	}
        $return .= '</select> </td>';
        $return .= '</tr> <tr>';
        $connection->close();
    }
    if($user_role == 2) {
        $return .= '<td colspan=2> Joindre un justificatif de votre rôle dans la vie associative de cette école : </td>';
        $return .= '</tr> <tr>';
        $return .= '<td colspan=2> <input id="real_button" hidden="hidden" type="file" name="org_report_upl"/>';
    	$return .= '<button type="button" id="fake_button"> Choisir un fichier </button> <span id="fake_text"> Aucun fichier choisi. </span> </td>';
        $return .= '</tr> <tr>';
    }
    $return .= '<td colspan=2> <input type="submit" name="sign_in" value="S\'inscrire"> </td>';
    $return .= '</tr>';
    $return .= '</table>';
    $return .= '</form>';
    $return .= '<script src="../includes/upload_btn.js"></script>';
    return $return;
}

function signIn() {
    $connection = db_connection();
    $return = null;
    if(isset($_POST['sign_in'])) {
        $user_name = $_POST['user_name'];
        $user_forename = $_POST['user_forename'];
        $user_log = $_POST['user_log'];
        $user_pass1 = $_POST['user_pass1'];
        $user_pass2 = $_POST['user_pass2'];
        $user_role = $_GET['account'];
        if($user_role == 1)
            $user_validated = 1;
        else
            $user_validated = 0;
        if(isset($_POST['user_school'])) {
            $user_school = $_POST['user_school'];
            if($user_name != "" AND $user_forename !="" AND $user_log != "" AND $user_pass1 != "" AND $user_pass2 != "") {
                if($user_pass1 === $user_pass2) {
                    if(stringVerify($user_name) AND stringVerify($user_forename) AND stringVerify($user_log)) {
                        $query = "SELECT user_login FROM users WHERE user_login = '$user_log'";
            			$results = $connection->query($query);
            			if($results->num_rows == 0) {
                            $query = "INSERT INTO users(user_name, user_forename, user_login, user_pass, user_role, user_validated, user_school_id) VALUES ('$user_name', '$user_forename', '$user_log', '$user_pass1', '$user_role', '$user_validated', '$user_school')";
                            $results = $connection->query($query);
        					if ($connection->affected_rows == 1) {
                                if($user_role == 1)
                                    $return = 'Inscription effectuée.';
                                else
                                    $return = 'Demande d\'inscription effectuée.';
        					} else {
        						$return = 'Erreur lors de l\'inscription:' .$query .'<br>' .$connection->error;
        					}
                        } else {
                            $return = 'Cet identifiant est déja utilisé.';
                        }
                        $connection->close();
                    } else {
                        $return = 'Le nom, le prénom et l\'identifiants ne peuvent pas contenir de caractères spéciaux';
                    }
                } else {
                    $return = 'Les mots de passe ne correspondent pas.';
                }
            } else {
                $return = 'Veuillez remplir les champs requis.';
            }
        } else {
            $return = 'Veuillez remplir les champs requis.';
        }
    }
    $return = '<p style="color: red;">' .$return .'</p>';
    return $return;
}

//fonction d'affichage du formulaire de connexion
function logInForm() {
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
function logIn() {
	$connection = db_connection();
	$return = null;
	if(isset($_POST['log_in'])) {
		if($_POST['login'] != "" && $_POST['password'] != "") {
			$login = $_POST['login'];
			$password = $_POST['password'];
            $query = "SELECT * FROM users WHERE user_login = '$login' AND user_pass = '$password' AND user_validated = 0";
            $result = $connection->query($query);
            if($result->num_rows == 0) {
    			$query = "SELECT * FROM users WHERE user_login = '$login' AND user_pass = '$password' AND user_validated = 1";
    			$results = $connection->query($query);
    			if($results->num_rows > 0) {
    				while($row = $results->fetch_assoc()) {
    					$return = 'Identifiants corrects.';
    					session_start();
    					$_SESSION['user_connected'] = true;
    					$_SESSION['user_login'] = $row['user_log'];;
						$_SESSION['user_name'] = $row['user_name'];
						$_SESSION['user_forename'] = $row['user_forename'];
                        $_SESSION['user_role'] = $row['user_role'];
    					header('location: ../index.php');
    				}
    			} else {
    				$return = 'Identifiants incorrects.';
    			}
            } else {
                $return = 'Votre demande d\'inscription n\'a pas encore été validée.';
            }
			$connection->close();
		}
		else {
			if($_POST['login'] == "" && $_POST['password'] != "") {
				$return = 'Veuillez renseigner votre identifiant svp.';
			}
			else if($_POST['login'] != "" && $_POST['password'] == "") {
				$return = 'Veuillez renseigner votre mot de passe svp.';
			}
			else {
				$return = 'Veuillez renseigner votre identifiant et votre mot de passe svp.';
			}
		}
	}
	$return = '<p style="color: red;">' .$return .'</p>';
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
        switch($_SESSION['user_role']) {
               case 1 :
               $user_role = 'Utilisateur';
               break;
               case 2 :
               $user_role = 'Manager VA';
               break;
               case 3:
               $user_role = 'Administrateur';
               break;
               default:
               $user_role = 'Utilisateur';
               break;
        }
        $return .= '<td class = "nb"> Profil : ' .$user_role .'</td>';
		if($c_page == 'index')
		      $return .= '<td class = "nb"> <form action="pages/user_log_out.php" method="POST"> <input type="submit" value="Se déconnecter" name="log_out"> </form> </td>';
		else if($c_page == 'default')
		      $return .= '<td class = "nb"> <form action="user_log_out.php" method="POST"> <input type="submit" value="Se déconnecter" name="log_out"> </form> </td>';
	}
	$return .= '</tr>';
	$return .= '</table>';
	return $return;
}

/*--------------------------------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/

function createSchoolForm() {
	$return = '<form action="#" method="post">';
	$return .= '<table>';
	$return .= '<tr>';
	$return .= '<td colspan=4> <h3> Inscrire son école </td>';
	$return .= '</tr> <tr>';
	$return .= '<td colspan=2> Nom de l\'école : </td> <td colspan=2> <input type="text" name="school_name"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td> Code postal : </td> <td> <input type="text" name="school_postal"> </td>';
	$return .= '<td> Ville : </td> <td> <input type="text" name="school_city"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td colspan=2> Adresse : </td> <td colspan=2> <input type="text" name="school_address"> </td>';
	$return .= '</tr> <tr>';
	$return .= '<td colspan=4> <input type="submit" name="school_create" value="Inscire mon école">';
	$return .= '</tr> <tr>';
	$return .= '</table>';
	$return .= '</form>';

	return $return;
}

function createSchool() {
	$connection = db_connection();
	$return = "";
	if(isset($_POST['school_create'])) {
		$school_name = $_POST['school_name'];
		$school_postal = $_POST['school_postal'];
		$school_city = $_POST['school_city'];
		$school_address = $_POST['school_address'];
		if($school_name != "" AND $school_postal != "" AND $school_city != "" AND $school_address != "") {
			$query = "SELECT school_name FROM schools WHERE school_name = '$school_name'";
			$results = $connection->query($query);
			if($results->num_rows == 0) {
				if(is_numeric($school_postal)) {
					$school_complete_address = $school_address .' ' .$school_postal .' ' .$school_city;
					$query = "INSERT INTO schools (school_name, school_nb_students, school_nb_organization, school_adress) VALUES ('$school_name', 0, 0, '$school_complete_address')";
					$results = $connection->query($query);

					if ($connection->affected_rows == 1) {
						$return = "Ecole correctement inscrite";
					} else {
						$return = "Error: " . $query . "<br>" . $connection->error;
					}
					$connection->close();

				} else {
					$return = "Le code postal doit être composé de chiffres.";
				}
			} else {
				$return = "Une école du même nom est déja inscrite.";
			}
		} else {
				$return = "Veuillez remplir les champs requis.";
		}
	}
	$return = '<p style="color: red;">' .$return .'</p>';
	return $return;
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
		$query = "SELECT school_name, school_adress, school_postal, school_city FROM schools WHERE school_id='$id'";
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

		$query = "UPDATE schools SET school_name = '$nomEcole', school_adress = '$adresseEcole', school_postal = '$codePostal', school_city = '$ville' WHERE school_id = '$id' ";
		$result = $connection->query($query);

		if ($result === TRUE) {
			$return = "Informations correctement modifiées";
		} else {
			$return = "Error: " . $query . "<br>" . $connection->error;
		}
		return $return;
	}
}

/*--------------------------------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/


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
    $return .= '<script src="../includes/upload_btn.js"></script>';
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

function orgaPage() {
	$connection = db_connection();
	$id = $_SESSION['orgId'];
	$query = "SELECT * FROM members INNER JOIN organizations ON members.member_organization_id = '$id' AND organizations.organization_id = '$id'";
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
	$query = "SELECT * FROM members INNER JOIN organizations ON members.member_organization_id = organizations.organization_id WHERE members.member_organization_id = 1";
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

	$query = "INSERT INTO members(member_forename, member_name, member_role, member_organization_id) VALUES ('$forename', '$name', '$role', '$id')";
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
	$query = "SELECT * FROM members WHERE member_organization_id = '$id'";
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
	$query = "SELECT * FROM members WHERE member_organization_id = '$id'";
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

	$query = "UPDATE members SET member_forename = '$forename', member_name = '$name', member_role = '$role', member_organization_id = '$orgId' WHERE member_id = '$id' ";
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
	$query = "SELECT * FROM organizations WHERE organization_school_id = '$id'";
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

/*--------------------------------------------------------------------------------------------------------------*/
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
/*--------------------------------------------------------------------------------------------------------------*/

function displayBackBtn(){
	$return = '<form action="' .$_SERVER["HTTP_REFERER"] .'" method="POST" >';
	$return .= '<input type="submit" name="back_page" value="Retour">';
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
