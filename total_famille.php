<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);

// Récupération de la contribution à la caisse
$requete=mysqli_query($link, "select commissions_statut_commande,commissions_contrib_caisse from commissions where commissions_ID=$commission;");
$resultat = mysqli_fetch_array($requete);
$contrib_caisse=$resultat['commissions_contrib_caisse'];

$requete=mysqli_query($link, " select nom_complet,sum(round(commande_quantite*produits_prix_udv,2)) as montant from commande,produits,membres where commande_membre=ID and commande_produit=produits_ID and commande_commission=$commission and produits_actif=1 group by nom_complet order by nom_complet;");
?>

<html>
<head>
<link rel="stylesheet" href="style.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>
<body>
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">


<H3> Total de la commande par famille  pour la commission <?php echo $nom_commission[$commission]; ?></H3>

<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Famille</th>
			<th colspan="1">Montant (TTC)</th>
		</tr>
	</thead>
	<tbody valign="middle">
<?php
$total=0;
$nb_commandes=0;
while (($resultat = mysqli_fetch_array($requete)))
{
	if ($contrib_caisse == 0)
	{
		$total+=$resultat['montant'];
		$nb_commandes++;
		echo "<tr><td>".$resultat['nom_complet']."</td><td align='right'>".$resultat['montant']."</td></tr>\n";
	}
	else
	{
		$total_membre=$resultat['montant']+$contrib_caisse;
  		$total+=$total_membre;
		$nb_commandes++;
		echo "<tr><td>".$resultat['nom_complet']."</td><td align='right'>".$total_membre."</td></tr>\n";	
	}
}
?>
	<tr><td align='right'><b>Total</b></td><td align='right'><?php echo $total;?></td></tr>
	</tbody>
</table>

<p><p>Nombre de commandes : <?php echo $nb_commandes; ?><p><p>

<H4>Liste des membres n'ayant pas commandé</H4>
<b>Noms</b><p>
<?php

$requete=mysqli_query($link, "select nom_complet,email from membres where (ID not in (select commande_membre from commande where commande_commission=$commission)) AND (statut!=".pow(2,12).") order by nom_complet;");
if (mysqli_num_rows($requete))
{
	$liste_membre_sans_commande="";
	$liste_email_membre_sans_commande="";
	while ($resultat = mysqli_fetch_array($requete))
		{
		$liste_membre_sans_commande.=$resultat['nom_complet'].", ";
		$liste_email_membre_sans_commande.=$resultat['email'].", ";
		}
	echo substr($liste_membre_sans_commande,0,strlen($liste_membre_sans_commande)-2);
	echo "<p><b>Adresses électroniques</b><p>";
	echo substr($liste_email_membre_sans_commande,0,strlen($liste_email_membre_sans_commande)-2);
	}
else
	{
	echo "Tous les membres ont commandé !";
	}
?>

<H4>Liste des membres ayant commandé</H4>
<b>Noms</b><p>
<?php

$requete=mysqli_query($link, "select nom_complet,email from membres where ID in (select commande_membre from commande where commande_commission=$commission) order by nom_complet;");
if (mysqli_num_rows($requete))
{
	$liste_membre_sans_commande="";
	$liste_email_membre_sans_commande="";
	while ($resultat = mysqli_fetch_array($requete))
		{
		$liste_membre_sans_commande.=$resultat['nom_complet'].", ";
		$liste_email_membre_sans_commande.=$resultat['email'].", ";
		}
	echo substr($liste_membre_sans_commande,0,strlen($liste_membre_sans_commande)-2);
	echo "<p><b>Adresses électroniques</b><p>";
	echo substr($liste_email_membre_sans_commande,0,strlen($liste_email_membre_sans_commande)-2);
	}
else
	{
	echo "Personne n'a commandé !";
	}
?>



<div align="center"><a href="gestion_commande.php?commission=<?php echo $commission; ?>">Retour à la gestion de commande</a></div>

</div>
 </body>
 </html>
