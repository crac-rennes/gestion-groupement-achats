<script type="text/javascript" language="javascript" src="fonction.js"></script>

<?php
verification_identification();

// Variables de construction du menu

$DECONNEXION = <<<BIDON
<p> <a href="contact_membres.php">Contacter des membres du groupement</a> 
<form action="deconnexion.php" method="post">
		<input class="bouton" type="submit" name="submit" value="Déconnexion" />        
</form>  
BIDON;

$MENU_ADHERENT = <<<BIDON
<p> <a href="saisie_commande.php?commission=%c"> Commande %s </a> 
BIDON;

$MENU_COMMISSION = <<<BIDON
<p> <a href="gestion_produits.php?commission=%c"> Gestion des produits%s </a> 
<p> <a href="gestion_commande.php?commission=%c"> Gestion de la commande%s </a> 
<p> <a href="backup_partiel_commission.php?commission=%c"> Sauvegarde de la base %s </a> 
BIDON;

$MENU_RUBRIQUES = <<<BIDON
<p> <a href="gestion_fournisseurs.php"> Gestion des fournisseurs </a> 
<p> <a href="gestion_rubriques.php"> Gestion des rubriques </a> 
BIDON;

$MENU_ADMIN = <<<BIDON
<p> <a href="gestion_adherent.php"> Gestion des membres </a> 
<p> <a href="gestion_commissions.php"> Gestion des commissions </a> 
<p> <a href="backup.php"> Sauver la base </a> 
/ <a href="restore.php"> Restaurer la base </a> 
BIDON;

// Fin des variables du menu

$req_menu=mysqli_query($link,"select commissions_ID from commissions order by commissions_ID");
//$x = mysqli::query("select commissions_ID from commissions order by commissions_ID;");
//var_dump($link);
//var_dump($x);
//var_dump($req_menu);
$nb_commissions=0;
$num_max_commission=0;

/*$x=mysqli_fetch_assoc ($req_menu);
var_dump($x);
die;*/

while($resultat=mysqli_fetch_array($req_menu,MYSQLI_BOTH))
{
	//var_dump($resultat);
	if ($num_max_commission<$resultat['commissions_ID'])
		$num_max_commission=$resultat['commissions_ID'];
	$nb_commissions++;
	$tab_commission[$nb_commissions]=$resultat['commissions_ID'];
}
//var_dump($tab_commission);
?>

<?php  echo '<b>Bienvenue ', $_SESSION['nom_complet'],"</b>\n"; 

	$MENU='<p><a href="infos.php">Informations</a>';

$POPUP = <<<BIDON
 / <a href="javascript:void newWindow('aide_membres.php',1)">De l'aide ?</a>\n 
BIDON;

$MENU=$MENU.$POPUP;

if (is_dir("./CR") OR is_dir("./fichiers") )
{
	$MENU=$MENU.'<p><a href="documents.php">Documents</a>';
}
$POPUP_RESP_COM = <<<BIDON
<p>Aide pour les responsables de commission <a href="javascript:void newWindow('aide_responsable_commission.php',0)">debutant </a> / <a href="javascript:void newWindow('aide_responsable_commission_expert.php',0)">expert </a> \n 
BIDON;
	$MENU_RUBRIQUES=$MENU_RUBRIQUES.$POPUP_RESP_COM;
	
	$MDP="<form action='modif_mdp.php' method='post'><input type='hidden' name='ID' value=".$_SESSION['gpt_ID']."><input class='bouton' type='submit' value='Modifier les\ninformations personnelles'></form>";
	if ($_SESSION['statut']==0)
		{
		for ($i=1;$i<=$nb_commissions;$i++)
			{
			$j=$tab_commission[$i];
			$MENU= $MENU.$MENU_ADHERENT;
			$MENU=preg_replace('#%c#',"$j",$MENU);
			$MENU=preg_replace('#%s#',"$nom_commission[$j]",$MENU);
			}
		$MENU=$MENU.$MDP;
		}
	else if ( is_admin($_SESSION['statut']) )	// Cas de l'administrateur
		{
		for ($i=1;$i<=$nb_commissions;$i++)
			{
			$j=$tab_commission[$i];
			$MENU= $MENU.$MENU_ADHERENT;
			$MENU=preg_replace('#%c#',"$j",$MENU);
//var_dump($nom_commission);
				$MENU=preg_replace('#%s#',"$nom_commission[$j]",$MENU);
			
			}
		for ($i=1;$i<=$nb_commissions;$i++)
			{
			$j=$tab_commission[$i];
			$MENU=$MENU.preg_replace('#%c#', "$j",$MENU_COMMISSION);
			
				$MENU=preg_replace('#%s#'," ($nom_commission[$j])",$MENU);
			
			}
		$MENU=$MENU.$MENU_RUBRIQUES;
		$MENU=$MENU.$MENU_ADMIN;
		}
  	else if (!is_associe($_SESSION['statut']))
		{		
		for ($i=1;$i<=$nb_commissions;$i++)
			{
			$j=$tab_commission[$i];
			$MENU= $MENU.$MENU_ADHERENT;
			$MENU=preg_replace('#%c#',"$j",$MENU);
			$MENU=preg_replace('#%s#',"$nom_commission[$j]",$MENU);
			}
		for ($i=1;$i<=$nb_commissions;$i++)
		{
			$j=$tab_commission[$i];
  			if ( is_resp_comm($j,$_SESSION['statut']) )
				{
				$MENU=$MENU.preg_replace('#%c#', "$j",$MENU_COMMISSION);
				$MENU=preg_replace('#%s#'," ($nom_commission[$j])",$MENU);
				}  
		}
		$MENU=$MENU.$MENU_RUBRIQUES;
		$MENU=$MENU.$MDP;
		}
	echo $MENU;
	echo $DECONNEXION;
?> 
