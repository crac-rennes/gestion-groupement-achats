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


<H3>Bon de commande final des <?php echo $nom_commission[$commission]; ?></H3>
	
<form action="pdf_final.php?commission=<?php echo $commission;?>" target='new' method="post">
<input class='bouton' type='submit' name='validation_pdf' value='Créer au format PDF'>
</form>

<?php

//$requete = mysqli_query($link, "select * from (produits left join fournisseurs on produits.produits_fournisseur=fournisseurs.fournisseurs_ID left join commande on produits.produits_ID=commande.commande_produit left join membres on commande_membre=ID) where (produits_commission=$commission and produits_actif=1) order by nom_complet,fournisseurs_nom;");
$requete = mysqli_query($link, "select ID,nom_complet,fournisseurs_ID,fournisseurs_nom,produits_nom,produits_conditionnement,produits_udv,produits_prix_udv,commande_quantite,round(produits_prix_udv*commande_quantite,2) as total_produit from produits,fournisseurs,commande,membres where (produits_fournisseur=fournisseurs_ID and produits_ID=commande_produit and commande_membre=ID and produits_commission=$commission and produits_actif=1) order by nom_complet,fournisseurs_nom,produits_nom;");

$membre_old='';

// Si la requete est non vide
if (mysqli_num_rows($requete)!=0)
{
	while(($resultat = mysqli_fetch_array($requete)))
	{
		if ($membre_old=='')
		{
			$membre_old=$resultat['ID'];
			// premier membre
			echo "<H3>".$resultat['nom_complet']."</H3>";
			echo "<table border='2'>\n";
			echo "<thead valign='middle'>\n";
			echo "<tr>\n";
			echo "<th colspan='1'>Nom du produit</th>\n";
			echo "<th colspan='1'>Conditionnement</th>\n";
			echo "<th colspan='1'>Quantité</th>\n";
			echo "<th colspan='1'>Unité de vente</th>\n";
			echo "<th colspan='1'>Prix de l'U.D.V (en €)</th>\n";
			echo "<th colspan='1'>Total (en €)</th>\n";
			echo "</tr>\n";
			echo "</thead>\n";
			echo "<tbody valign='middle'>\n";
			// Initialisation fournisseur
			$fournisseur_old=$resultat['fournisseurs_ID'];
			echo "<tr><td colspan='6' align='center'><font size=+1>".$resultat['fournisseurs_nom']."</font></td></tr>";
			// Premier produit
			$total=0;
			$total_fournisseur=0;
			echo "<tr>\n";
			echo "<td>".$resultat['produits_nom']."</td>\n";
			echo "<td>".$resultat['produits_conditionnement']."</td>\n";
			echo "<td align='center'>".$resultat['commande_quantite']."</td>\n";
			echo "<td>".$resultat['produits_udv']."</td>\n";
			echo "<td align='right'>".$resultat['produits_prix_udv']."</td>\n";
//			$prix=round($resultat['commande_quantite']*$resultat['produits_prix_udv'],2);
$prix=$resultat['total_produit'];
			$total+=$prix;
			$total_fournisseur+=$prix;
			echo "<td align='right'>".number_format($prix,2,',','')."</td>\n";
			echo "</tr>\n";
		}
		elseif ($resultat['ID']!==$membre_old)
		{
			// On cloture le tableau précédent
			// total fournisseur
			echo "<tr><td colspan='5' align='right'><b>Total fournisseur</b></td><td align='right'>".number_format($total_fournisseur,2,',','')."</td></tr>";
			// Contrib_caisse
			if ($contrib_caisse != 0)
 			{
    				echo "<tr><td colspan='5' align='right'><b>Contribution caisse</b></td><td align='right'>$contrib_caisse</td></tr>";
        				$total +=$contrib_caisse;
       			}	
			// Total
			echo "<tr><td colspan='5' align='right'><b>Total commande</b></td><td align='right'>".number_format($total,2,',','')."</td></tr>";
			echo "</tbody>\n";
			echo "</table>\n";
			echo "<p><p><p>\n";
			
			// Nouveau membre
			echo "<H3>".$resultat['nom_complet']."</H3>";
			$membre_old=$resultat['ID'];
			// Nouveau tableau
			echo "<table border='2'>\n";
			echo "<thead valign='middle'>\n";
			echo "<tr>\n";
			echo "<th colspan='1'>Nom du produit</th>\n";
			echo "<th colspan='1'>Conditionnement</th>\n";
			echo "<th colspan='1'>Quantité</th>\n";
			echo "<th colspan='1'>Unité de vente</th>\n";
			echo "<th colspan='1'>Prix de l'U.D.V (en €)</th>\n";
			echo "<th colspan='1'>Total (en €)</th>\n";
			echo "</tr>\n";
			echo "</thead>\n";
			echo "<tbody valign='middle'>\n";
			// Initialisation fournisseur
			$fournisseur_old=$resultat['fournisseurs_ID'];
			echo "<tr><td colspan='6' align='center'><font size=+1>".$resultat['fournisseurs_nom']."</font></td></tr>";
			// Premier produit
			$total=0;
			$total_fournisseur=0;
			echo "<tr>\n";
			echo "<td>".$resultat['produits_nom']."</td>\n";
			echo "<td>".$resultat['produits_conditionnement']."</td>\n";
			echo "<td align='center'>".$resultat['commande_quantite']."</td>\n";
			echo "<td>".$resultat['produits_udv']."</td>\n";
			echo "<td align='right'>".$resultat['produits_prix_udv']."</td>\n";
//			$prix=round($resultat['commande_quantite']*$resultat['produits_prix_udv'],2);
$prix=$resultat['total_produit'];
			$total+=$prix;
			$total_fournisseur+=$prix;
			echo "<td align='right'>".number_format($prix,2,',','')."</td>\n";
			echo "</tr>\n";
		}
		else
		{
			if ($fournisseur_old==$resultat['fournisseurs_ID'])
			{
				// Nouvelle ligne produit
				echo "<tr>\n";
				echo "<td>".$resultat['produits_nom']."</td>\n";
				echo "<td>".$resultat['produits_conditionnement']."</td>\n";
				echo "<td align='center'>".$resultat['commande_quantite']."</td>\n";
				echo "<td>".$resultat['produits_udv']."</td>\n";
				echo "<td align='right'>".$resultat['produits_prix_udv']."</td>\n";
//				$prix=round($resultat['commande_quantite']*$resultat['produits_prix_udv'],2);
$prix=$resultat['total_produit'];
				$total+=$prix;
				$total_fournisseur+=$prix;
				echo "<td align='right'>".number_format($prix,2,',','')."</td>\n";
				echo "</tr>\n";
			}
			else
			{
				// total fournisseur précédent
				echo "<tr><td colspan='5' align='right'><b>Total fournisseur</b></td><td align='right'>".number_format($total_fournisseur,2,',','')."</td></tr>";
				// Initialisation fournisseur
				$fournisseur_old=$resultat['fournisseurs_ID'];
				echo "<tr><td colspan='6' align='center'><font size=+1>".$resultat['fournisseurs_nom']."</font></td></tr>";
				// Premier produit du nouveau fournisseur
				$total_fournisseur=0;
				echo "<tr>\n";
				echo "<td>".$resultat['produits_nom']."</td>\n";
				echo "<td>".$resultat['produits_conditionnement']."</td>\n";
				echo "<td align='center'>".$resultat['commande_quantite']."</td>\n";
				echo "<td>".$resultat['produits_udv']."</td>\n";
				echo "<td align='right'>".$resultat['produits_prix_udv']."</td>\n";
//				$prix=round($resultat['commande_quantite']*$resultat['produits_prix_udv'],2);
$prix=$resultat['total_produit'];
				$total+=$prix;
				$total_fournisseur+=$prix;
				echo "<td align='right'>".number_format($prix,2,',','')."</td>\n";
				echo "</tr>\n";
			}
		}
	}
// On cloture le tableau précédent
// total fournisseur
echo "<tr><td colspan='5' align='right'><b>Total fournisseur</b></td><td align='right'>".number_format($total_fournisseur,2,',','')."</td></tr>";
// Contrib_caisse
if ($contrib_caisse != 0)
{
	echo "<tr><td colspan='5' align='right'><b>Contribution caisse</b></td><td align='right'>$contrib_caisse</td></tr>";
	$total +=$contrib_caisse;
}
// Total
echo "<tr><td colspan='5' align='right'><b>Total commande</b></td><td align='right'>".number_format($total,2,',','')."</td></tr>";
echo "</tbody>\n";
echo "</table>\n";
}
?>

<div align="center"><a href="gestion_commande.php?commission=<?php echo $commission; ?>">Retour à la gestion de commande</a></div>

</div>
 </body>
 </html>
