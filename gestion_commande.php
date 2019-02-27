<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);

// Ajout de la fonction strripos //

if(!function_exists("stripos")){
    function stripos(  $str, $needle, $offset = 0  ){
        return strpos(  strtolower( $str ), strtolower( $needle ), $offset  );
    }/* endfunction stripos */
}/* endfunction exists stripos */

if(!function_exists("strripos")){
    function strripos(  $haystack, $needle, $offset = 0  ) {
        if(  !is_string( $needle )  )$needle = chr(  intval( $needle )  );
        if(  $offset < 0  ){
            $temp_cut = strrev(  substr( $haystack, 0, abs($offset) )  );
        }
        else{
            $temp_cut = strrev(    substr(   $haystack, 0, max(  ( strlen($haystack) - $offset ), 0  )   )    );
        }
        if(   (  $found = stripos( $temp_cut, strrev($needle) )  ) === FALSE   )return FALSE;
        $pos = (   strlen(  $haystack  ) - (  $found + $offset + strlen( $needle )  )   );
        return $pos;
    }/* endfunction strripos */
}/* endfunction exists strripos */

if (isset($_POST['figer']))
{
	mysqli_query($link, "update commissions set commissions_statut_commande=0 where commissions_ID=$commission;");
	//header("Location: $BASE_URL/gestion_commande.php?commission=".$_GET['commission']);
}
elseif (isset($_POST['debloquer']))
{
	mysqli_query($link, "update commissions set commissions_statut_commande=1	where commissions_ID=$commission;");
	//header("Location: $BASE_URL/gestion_commande.php?commission=".$_GET['commission']);
}
elseif (isset($_POST['modif_infos']))
{
	mysqli_query($link, "update commissions set commissions_infos='".($_POST['infos'])."' where commissions_ID=$commission;");
	echo "<font color='green'>Modification effectuée.</font>\n";
}
?>

<html> 
<head> 
<title>Gestion de la commande des <?php echo $nom_commission[$commission]; ?>
</title> 
<link rel="stylesheet" href="style.css">
<SCRIPT LANGUAGE="Javascript"> 
function alerte_backup()
{
	alert("Penser, si nécessaire, à faire une sauvegarde de la base (voir barre de menu à gauche) et une copie de l'état initial des commandes (Document commission plus bas dans la page)");
}
function alerte_historique()
{
	alert("Avez vous pensé, si nécessaire, à créer une archive de la dernière commande (voir 'Historique des commandes' plus bas dans la page). Si nécessaire, répondre Non à la question de la page suivante.");
}
</SCRIPT>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  

<body>
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">


<H2>Gestion de la commande des <?php echo $nom_commission[$commission]; ?></H2>

<table border='0' style="border: 0px;">
<tbody>
<tr border='0' style="border: 0px;">
<td border='0' style="border: 0px; width: 230px;">
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?commission=".$commission; ?>" method="post">

<?php 
// Récupération du statut de la liste
$requete = mysqli_query($link, "select commissions_statut_commande from commissions where commissions_ID=$commission;");
$resultat = mysqli_fetch_array($requete);
$etat_commande=$resultat['commissions_statut_commande'];

if ($etat_commande)
	echo "<input class='bouton' type='submit' name='figer' value='Figer le bon de commande' onClick=alerte_backup()>";
else
	echo "<input class='bouton' type='submit' name='debloquer' value='Débloquer le bon de commande'>";
?>
</form>
</td>
<td border='0' style="border: 0px;">
<form action="vider_bon_commande.php?commission=<?php echo $commission; ?>" method="post"  onClick=alerte_historique()>
<input class='bouton' type='submit' value='Vider le bon de commande'>
</form>
</td>
</tbody>
</table>

<p><p>

<H3>Gérer l'avancement de la commande pour chaque fournisseur :</H3>

<?php echo "<form action='tableau_avancement.php?commission=".$commission."' method='post'>"; ?>

<input class='bouton' type='submit' name='afficher' value='Afficher le tableau'>
</form>



<H3>Sortir le bon de commande pour un fournisseur :</H3>
<?php
	// selection des fournisseurs pour lesquels il y a des produits commandés et non desactivés
	$requete=mysqli_query($link, "select fournisseurs_nom,fournisseurs_ID from commande,fournisseurs,produits where (commande_produit=produits_ID and produits_fournisseur=fournisseurs_ID and produits_commission=$commission and produits_actif=1) group by fournisseurs_ID order by fournisseurs_nom;");
	// Si la selection n'est pas vide
	if (mysqli_num_rows($requete)!=0)
	{
		echo '<form action="bon_commande_fournisseur.php?commission='.$commission.'" method="post">';
		echo "Choix du fournisseur :";
		echo "<select name='fournisseurs_ID'>\n";
		while(($resultat = mysqli_fetch_array($requete)))
			echo "<option value=".$resultat['fournisseurs_ID'].">".$resultat['fournisseurs_nom']."</option>";
		echo "</select>\n<input class='bouton' type='submit' name='simple' value='Bon de commande fournisseur'>\n";
		echo "<input class='bouton' type='submit' name='detail' value='Version detaillée'>";
		echo "<input class='bouton' type='submit' name='tableau' value='Tableau'>\n</form>\n";
	}
	else
	{
		echo "Pas de produits commandés dans cette commission.\n";
	}
?>

<form action="bilan_par_fournisseur.php?commission=<?php echo $commission;?>" method="post">Bilan de la commande par fournisseur : <input class='bouton' type='submit' value='Voir'></form>
	

<H3>Voir les commandes par produits / Gérer l'ajustement :</H3>
<?php
	// selection des produits pour lesquels la commande est non nulle et non desactivés
	$requete=mysqli_query($link, "select
	produits_nom,produits_ID,produits_conditionnement from commande,produits where (commande_produit=produits_ID and produits_actif=1 and produits_commission=$commission) group by produits_ID order by produits_nom;");
	// si la requete est non vide
	if (mysqli_num_rows($requete)!=0)
	{
		echo '<form action="bon_commande_produit.php?commission='.$commission.'" method="post">';
		echo "Choix du produit :\n";
		echo "<select name='produits_ID'>\n";
		while(($resultat = mysqli_fetch_array($requete)))
			echo "<option value=".$resultat['produits_ID'].">".$resultat['produits_nom']."-".$resultat['produits_conditionnement']."</option>";
		echo "</select>\n<input class='bouton' type='submit' value='Bon de commande produit'>\n</form>\n";
	}
	else
	{
		echo "Pas de produits commandés dans cette commission.\n";
	}

	// selection des produits pour lesquels l'ajustement n'est pas fait
	$requete=mysqli_query($link, "select * from (select produits_nom,produits_ID,produits_conditionnement,sum(commande_quantite) as total, produits_vrac from commande,produits where (commande_produit=produits_ID and produits_actif=1 and produits_commission=$commission and produits_vrac!=0) group by produits_ID) as interm where mod(total,abs(produits_vrac))!=0 order by produits_nom;");
	// si la requete est non vide
	if (mysqli_num_rows($requete)!=0)
	{
		echo '<form action="bon_commande_produit.php?commission='.$commission.'" method="post">';
		echo "Produits non ajustés :\n";
		echo "<select name='produits_ID'>\n";
		while(($resultat = mysqli_fetch_array($requete)))
			echo "<option value=".$resultat['produits_ID'].">".$resultat['produits_nom']."-".$resultat['produits_conditionnement']."</option>";
		echo "</select>\n<input class='bouton' type='submit' value='Bon de commande produit'>\n</form>\n";

		// Ajustement automatique pour tout les produits pour lesquel la procédure s'applique
		echo "<H4>Ajustement automatique</H4>\n";
		echo '<form action="liste_produits_ajustable_automatiquement.php?commission='.$commission.'" method="post">';
		echo "Liste des produits ajustables et non ajustables automatiquement\n";
		echo "<input class='bouton' type='submit' value='Afficher'>\n</form>\n";
		echo '<form action="ajustement_automatique_global.php?commission='.$commission.'" method="post">';
		echo "Ajustement automatique de tous les produits pour lesquels c'est possible\n";
		echo "<input class='bouton' type='submit' value='Lancer'>\n</form>\n";
	}
	else
	{
		echo "Tous les produits sont ajustés !\n";
	}
	
?>

<H3>Préparer la livraison aux familles</H3>

<?php 
// Existe-t-il des produits en vrac
$requete=mysqli_query($link, "select commande_produit from commande,produits where (commande_produit=produits_ID and produits_actif=1 and produits_commission=$commission and produits_vrac>0);");
?>

<form action="pdf_tous_produits_vrac.php?commission=<?php echo $commission; ?>" method="post">
Détail des commandes pour les produits : <?php if (mysqli_num_rows($requete)>0) echo "<input class='bouton' type='submit' name='bouton' value='en vrac'>"; ?><input class='bouton' type='submit' name='bouton' value='tous'>
</form>


Bon de commande par famille :
<form action="bon_commande_final.php?commission=<?php echo $commission; ?>" method="post">
<input class='bouton' type='submit' name='submit' value='Document famille'> (en accompagnement de la commande)
</form>
<form action="pdf_final.php?commission=<?php echo $commission; ?>" target='new' method="post">
<input class='bouton' type='submit' name='pour_commission' value='Document commission'> (à sauvegarder avant les ajustements)
</form>



<form action="total_famille.php?commission=<?php echo $commission; ?>" method="post">
Montant total de la commande par famille :
<input class='bouton' type='submit' value='Voir'>
</form>


<form action="preparation_livraison.php?commission=<?php echo $commission; ?>" method="post">
Estimation du travail de préparation :
<input class='bouton' type='submit' value='Voir'>
</form>


<H3>Modifier les infos commande</H3>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?commission=".$commission; ?>" method="post">
	<?php 
	$requete=mysqli_query($link, "select commissions_infos from commissions where commissions_ID=$commission;");
	if ($resultat=mysqli_fetch_array($requete))
		$infos=$resultat['commissions_infos'];
	else
		$infos='';
	?>
	<textarea rows='3' cols='100' name='infos'><?php echo $infos; ?></textarea>
	<input class='bouton' type='submit' name='modif_infos' value='Modifier'>
 </form>

<H3>Historique les commandes</H3>

<form action="ajout_historique.php?commission=<?php echo $commission; ?>" method="post">
	<input class='bouton' type='submit' name='ajout_historique' value="Ajouter la commande à l'historique">
	<p><font color='red'>L'appui sur ce bouton créera un fichier au nom de la commission et à la date d'aujourd'hui contenant l'ensemble des commandes fournisseurs actuelles ainsi que le détail des commandes par famille. </font><p>
 </form>

<table style="border:0px">
	<tr>
<?php
$ligne=0;
$liste_fichier=scandir("./historique",1);
$chaine=str_replace(",","",str_replace("'","_",str_replace(" ","_",$nom_commission[$commission])));

foreach ($liste_fichier as $fichier)
{
	$pos=strripos($fichier,$chaine);
	if ($pos)
	{
		$nom=substr($fichier,$pos+strlen($chaine)+1,strlen($fichier)-$pos-strlen($chaine)-5);
		echo "<td style='border:0px;padding: 15px;'><a href='historique/$fichier'>$nom</a></td>";
		$ligne++;
		if ($ligne==4)
		{
			echo "</tr>\n<tr>";
			$ligne=0;
		}
	}
}
?>
</tr></table>


</div>
</body>
</html>
