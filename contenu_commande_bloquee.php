<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];

// Récupération de la contribution à la caisse
$requete=mysqli_query($link,"select commissions_statut_commande,commissions_contrib_caisse from commissions where commissions_ID=$commission;");
$resultat = mysqli_fetch_array($requete);
$contrib_caisse=$resultat['commissions_contrib_caisse'];
?>


<html> 
<head> 
<title>Contenu de la commande</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">


<?php
// Récupération du nom pour détecter les éventuels bugs de sessions
$gpt_ID=$_SESSION['gpt_ID'];
$requete = mysqli_query($link,"SELECT * FROM membres WHERE ID=$gpt_ID;");
$resultat = mysqli_fetch_array($requete);
$nom_membre=$resultat['nom_complet'];

print("<H1> Résumé de votre commande de $nom_commission[$commission], $nom_membre</H1>\n");

$requete = mysqli_query($link,"select * from (produits left join fournisseurs on produits.produits_fournisseur=fournisseurs.fournisseurs_ID left join commande on produits.produits_ID=commande.commande_produit) where (commande_membre=$gpt_ID and produits_commission=$commission and produits_actif=1) order by fournisseurs_nom, produits_nom;");

if (mysqli_num_rows($requete))
{
	print("<table border='2'>\n");
	print("<thead valign='middle'>\n");
	print("<tr>\n");
	print("<th colspan='1'>Nom du produit</th>\n");
	print("<th colspan='1'>Fournisseur</th>\n");
	print("<th colspan='1'>Conditionnement</th>\n");
	print("<th colspan='1'>Unité de vente</th>\n");
	print("<th colspan='1'>Prix de l'U.D.V (en €)</th>\n");
	print("<th colspan='1'>Quantité</th>\n");
	print("<th colspan='1'>Ajustement</th>\n");
	print("<th colspan='1'>Total</th>\n");
	print("</tr>\n");
	print("</thead>\n");
	print("<tbody valign='middle'>\n");
	
	// Initialisations
	$montant_total=0;
	$nom_fournisseur_old='';
	$nb_produits=0;
	while(($resultat = mysqli_fetch_array($requete)))
	if ($resultat["commande_quantite"]!==NULL)
		{
			$nb_produits++;
			// Test pour le regroupement par fournisseur :
			if ($nom_fournisseur_old=='')
			{
				$nom_fournisseur_old=$resultat["fournisseurs_nom"];
				$sous_total_fournisseur=0;
			}
			elseif ($nom_fournisseur_old<>$resultat["fournisseurs_nom"])
			{
				echo '<tr valign="middle">';
				echo '<td colspan="7" rowspan="1" align="right">';
				echo "<b>Total $nom_fournisseur_old </b>";
				echo '</td>';
				echo '<td colspan="1" rowspan="1" align="right">';
				echo "<b>$sous_total_fournisseur </b>";
				echo '</td>';
				echo "</tr>\n";
				$sous_total_fournisseur=0;
				$nom_fournisseur_old=$resultat["fournisseurs_nom"];
			}

			echo '<tr valign="middle">';
			// Nom
			echo '<td colspan="1" rowspan="1" align="left">';
			echo $resultat["produits_nom"];
			echo '</td>';
			// Fournisseur
			echo '<td colspan="1" rowspan="1" align="left">';
			echo $resultat["fournisseurs_nom"];
			echo '</td>';
			//  Conditionnement
			echo '<td colspan="1" rowspan="1" align="center">';
			echo $resultat["produits_conditionnement"];
			echo '</td>';
			//  UDV
			echo '<td colspan="1" rowspan="1" align="center">';
			echo $resultat["produits_udv"];
			echo '</td>';
			//  Prix de l'UDV
			echo '<td colspan="1" rowspan="1" align="right">';
			echo $resultat["produits_prix_udv"];
			echo '</td>';
			//  Quantité
			echo '<td colspan="1" rowspan="1" align="center">';
			echo $resultat["commande_quantite"];
			echo '</td>';
			//  Ajustement
			echo '<td colspan="1" rowspan="1" align="right">';
			echo $resultat["commande_ajustement"];
			echo '</td>';
			//  Total
			echo '<td colspan="1" rowspan="1" align="right">';
			$sous_total=$resultat["commande_quantite"]*$resultat["produits_prix_udv"];
			$montant_total+=$sous_total;
			echo $sous_total;
			$sous_total_fournisseur+=$sous_total;
			echo '</td>';
			echo "</tr>";
	}
	// Si le nombre de produit est non nul on ajoute le sous total du dernier fournisseur :
	if ($nb_produits!=0)
	{
		echo '<tr valign="middle">';
		echo '<td colspan="7" rowspan="1" align="right">';
		echo "<b>Total $nom_fournisseur_old</b>";
		echo '</td>';
		echo '<td colspan="1" rowspan="1" align="right">';
		echo "<b>$sous_total_fournisseur</b>";
		echo '</td>';
		echo "</tr>\n";
	}

	if ($contrib_caisse != 0)
		{
		$montant_total += $contrib_caisse;
		?>
		<tr>
		<td colspan="7" rowspan="1" align="right">Contribution caisse : </td>
		<td colspan="1" rowspan="1" align="right"><?php echo $contrib_caisse;?></td>
		</tr>
		<?php	}
	 
	print("</tbody>\n");
	print("</table>\n");
	print("<H3> Montant total de la commande : $montant_total € </H3>\n");
}
else
{
	print("Vous n'avez pas passé de commande dans cette commission.");
}
?>


</div>
</body>
</html>
