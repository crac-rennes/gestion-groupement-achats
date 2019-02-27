<?php

require("variables.php");
 

/* 
function DieInfo($action, $script = "", $ligne = 0) {
  global $MESSAGE_ERREUR;
  $p = ereg_replace("%m", $action, $MESSAGE_ERREUR);
  $p = ereg_replace("%s", $script, $p);
  $p = ereg_replace("%l", (string) $ligne, $p);
  die($p);
}

# Initialisations et test des connexions et tables
$mid = @ mysql_connect($SQL_HOTE, $SQL_LOGIN, $SQL_SECRET) or die("Connexion au serveur $SQL_HOTE impossible", __FILE__, __LINE__);
@ mysqli_select_db($SQL_BASE) or die("Connexion à  la base $SQL_BASE impossible", __FILE__, __LINE__);

mysqli_query($link, "SET NAMES UTF8");
*/

/*var_dump($SQL_HOTE);
var_dump($SQL_LOGIN);
var_dump($SQL_SECRET);
var_dump($SQL_BASE);*/

$link = mysqli_connect($SQL_HOTE, $SQL_LOGIN, $SQL_SECRET, $SQL_BASE);
/* Vérification de la connexion */

if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
/* TEST ; Retourne le nom de la base de données courante */
if ($result = mysqli_query($link, "SELECT DATABASE()")) {
    $row = mysqli_fetch_row($result);
    //printf("test : La base de données courante est %s.\n", $row[0]);
    mysqli_free_result($result);
}


function affiche_euro($valeur)
{
	return number_format(round($valeur,2),2,',',' ');
}



// Nom des commissions et nombre
$result = mysqli_query($link,"select commissions_ID,commissions_nom from commissions;");
$nom_commission=array();
$nb_commissions=0;
while($row=mysqli_fetch_assoc($result))
{
	$nom_commission[$row['commissions_ID']] = $row['commissions_nom'];
}
//printf ("%s (%s)\n",$row[0],$row[1]);

function is_admin($statut)
{
	if ( $statut == pow(2,10) )
 		return(1);
   	else
    	return(0);
}


// Fonction d'association statut / commission
function is_resp_comm($id_commission,$statut)
{
	// si le bit correspondant est �  1 ou il s'agit de l'admin
	if ( ( pow(2,$id_commission-1) & $statut ) == pow(2,$id_commission-1) )
 		return(1);
   	else
    	return(0);
}

function is_associe($statut)
{
	if ( $statut == pow(2,12) )
 		return(1);
   	else
    	return(0);
}


function verification_identification()
{
	if(empty($_SESSION['email'])) 
		{ 
		//header('Location: http://127.0.0.1/erreur.php');
  		header('Location: '.$BASE_URL.'/erreur.php');  
		exit();
		}
}

function verification_reponsable($commission)
{
	if ( !(is_resp_comm($commission,$_SESSION['statut'])) and !(is_admin($_SESSION['statut'])) )
		{ 
  		//header('Location: http://127.0.0.1/erreur_droits.php');  
		header('Location: '.$BASE_URL.'/erreur_droits.php');
  		exit();
		}
}

function verification_simple_membre()
{
	if ($_SESSION['statut']==0)
		{ 
		//header('Location: http://127.0.0.1/erreur_droits.php');
  		header('Location: '.$BASE_URL.'/erreur_droits.php');
		exit();
		}
}


function verification_admin()
{
	if ( !(is_admin($_SESSION['statut'])) )
		{ 
		//header('Location: http://127.0.0.1/erreur_droits.php');  
  		header('Location: '.$BASE_URL.'/erreur_droits.php');
		exit();
		}
}

// Fonction de protection des ' dans les noms des produits
function gestion_apostrophe($chaine)
{
	//return(str_replace("'","''",$chaine));
 	return($chaine);
}


?>
