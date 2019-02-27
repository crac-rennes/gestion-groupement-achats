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


<H3>Bilan de la commande par fournisseur</H3>
	
<?php
$requete=mysqli_query($link, "select fournisseurs_nom,round(sum(produits_prix_udv*commande_quantite),2) as total_fournisseur from fournisseurs, produits, commande where ( fournisseurs_ID=produits_fournisseur and produits_ID=commande_produit and commande_commission=$commission and produits_actif=1) group by fournisseurs_nom order by fournisseurs_nom;");
?>

<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Fournisseur</th>
			<th colspan="1">Total fournisseur (TTC)</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<?php
		$total=0;
		while (($resultat = mysqli_fetch_array($requete)))
		{
			echo '<tr valign="middle">';
			// Nom
			echo '<td colspan="1" rowspan="1" align="left">';
			echo $resultat['fournisseurs_nom'];
			print("</td>\n");
			// Total fournisseur
                        echo '<td colspan="1" rowspan="1" align="right">';
                        echo $resultat['total_fournisseur'];
                        echo '</td>';
			$total+=$resultat['total_fournisseur'];
		}
  
	if ($contrib_caisse != 0)
 	{
  		// Nombre de personne ayant commandé
		$requete="select commande_membre from commande where commande_commission=$commission group by commande_membre;";
  		$resultat_requete=mysqli_query($requete);
    		$nombre_commande = mysqli_num_rows($resultat_requete);
      		$montant_caisse = $nombre_commande*$contrib_caisse;
      		echo '<tr><td align="left">Contribution caisse</td><td align="right">'.number_format($montant_caisse,2,',',' ').'</td></tr>';
        		$total += $montant_caisse;
 	}
  	?>
	<tr valign="middle">
		<td colspan="1" rowspan="1" align="right">Total : </td>
		<td colspan="1" rowspan="1" align="right"><?php echo $total; ?></td>
	</tr>
	</tbody>
</table>
<p></p>

<div align="center"><a href="gestion_commande.php?commission=<?php echo $commission; ?>">Retour a la gestion de commande</a></div>

</div>
 </body>
 </html>
