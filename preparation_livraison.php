<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);


$requete=mysqli_query($link, "select count(*) from commande where commande_commission='$commission';");
$resultat = mysqli_fetch_array($requete);
$nb_produits= $resultat['count(*)'];

$requete=mysqli_query($link, "select count(*) from commande,produits where commande_produit=produits_ID and produits_vrac>0 and commande_commission='$commission';");
$resultat = mysqli_fetch_array($requete);
$nb_produits_vrac= $resultat['count(*)'];

$requete=mysqli_query($link, "select count(*) from commande,produits where commande_produit=produits_ID and produits_vrac<0 and commande_commission='$commission';");
$resultat = mysqli_fetch_array($requete);
$nb_produits_lot= $resultat['count(*)'];
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

<H3>Infos pour la préparation de la commande pour la commission <?php echo $nom_commission[$commission]; ?></H3>

<H4>Temps de préparation</H4>

<?php
echo "Le nombre de total d'articles commandés est : $nb_produits.<p>";
echo "Parmis ces articles, $nb_produits_vrac sont en vrac et $nb_produits_lot sont en lot.<p>";
$temps_total=4*$nb_produits_vrac+$nb_produits;
$heures = floor($temps_total/60);
$minutes = fmod($temps_total,60);
echo "<b>Soit un temps de préparation de la livraison estimé à : $heures heures et $minutes minutes.</b>";
?>
<p>
<small>
Estimation basée sur : <p>
- 4 min de préparation par produit en vrac (ensachage) <p>
- 1 min de réparation par produit (regroupemnt des produits par famille)
</small>
 


<H4>Sacs et sachets</H4>

<?php
$requete=mysqli_query($link, "select commande_quantite,count(*),produits_udv from commande,produits where commande_produit=produits_ID and produits_vrac>0 and commande_commission=$commission group by commande_quantite,produits_udv;");
?>


<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Contenance</th>
			<th colspan="1">Unité</th>
			<th colspan="1">Nombre</th>
		</tr>
	</thead>
	<tbody valign="middle">
			<?php
			while(($resultat = mysqli_fetch_array($requete)))
			echo "<tr><td>".$resultat['commande_quantite']."</td><td>".$resultat['produits_udv']."</td><td>".$resultat['count(*)']."</td><tr>";
			
			?>
	</tbody>
</table>


<div align="center"><a href="gestion_commande.php?commission=<?php echo $commission; ?>">Retour à la gestion de commande</a></div>

</div>
 </body>
 </html>
