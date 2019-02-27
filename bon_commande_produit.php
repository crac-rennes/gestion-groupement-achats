<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);

// Si on vient de la page d'ajustement automatique
if (isset($_POST['valid_ajust']))
	if ($_POST['valid_ajust']=='Oui')
		{
		$choisis=unserialize(rawurldecode($_POST['choisis']));
		$nb_choisis=$_POST['nb_choisis'];
		$produit_ID=$_POST['produits_ID'];
		if (mysqli_num_rows(mysqli_query($link, "select * from commande where (commande_produit=$produit_ID  and commande_ajustement<>0);"))) //pour empecher un probleme si on recharge la page...
			{
			mysqli_query($link, "update commande set commande_ajustement=0 where (commande_produit=$produit_ID and commande_commission=$commission);");
			for ($i=1;$i<=$nb_choisis;$i++)
				{
				$membre=$choisis[$i]['membre_ID'];
				$ajustement=$choisis[$i]['ajustement_retenu'];
				mysqli_query($link, "update commande set commande_quantite=commande_quantite+$ajustement where (commande_produit=$produit_ID and commande_membre=$membre and commande_commission=$commission);");
				}
		// Suppression des commandes pour lesquelles la quantité est nulle :
		mysqli_query($link, "delete from commande where (commande_quantite=0 and commande_commission=$commission);");
			}
		}
	/*else 
		echo "Modification annulée<p>";*/

if (isset($_POST['nettoyer_ajustements']))
	{
	$produit_ID=$_POST['produits_ID'];
	mysqli_query($link, "update commande set commande_ajustement=0 where (commande_produit=$produit_ID and commande_commission=$commission);");
	}
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


<?php
$produits_ID=$_POST['produits_ID'];
$requete=mysqli_query($link, "select produits_udv,produits_nom,produits_conditionnement,produits_prix_udv,produits_vrac from produits where produits_ID=$produits_ID;");
$resultat=mysqli_fetch_array($requete);
$produits_nom=$resultat['produits_nom'];
$produits_conditionnement=$resultat['produits_conditionnement'];
$produits_prix_udv=$resultat['produits_prix_udv'];
$produits_udv=$resultat['produits_udv'];
$produits_vrac=abs($resultat['produits_vrac']);
echo "<H3>Répartition des commandes de $produits_nom en $produits_conditionnement.</H3><p>";
	
$requete=mysqli_query($link, "select commande_quantite, commande_ajustement, nom_complet from commande,membres where (commande_membre=ID and commande_produit=$produits_ID) order by nom_complet;");

// Initlalisation pour ajustement automatique
if ($produits_vrac!=0)
{
	$ajustement_plus=0;
	$ajustement_moins=0;
}
?>
<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Membre</th>
			<th colspan="1">Quantite <?php echo $produits_udv; ?></th>
			<?php
			if ($produits_vrac!=0)
			{
				echo '<th colspan="1">Ajustement</th>';
			}?>
			<th colspan="1">Prix (en €)</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<?php
		$total_quantite=0;
		while (($resultat = mysqli_fetch_array($requete)))
		{
			echo '<tr valign="middle">';
			// Nom
			echo '<td colspan="1" rowspan="1" align="left">';
			echo $resultat['nom_complet'];
			print("</td>\n");
			// quantite
			$total_quantite +=$resultat['commande_quantite'];
			echo '<td colspan="1" rowspan="1" align="right">';
			echo $resultat['commande_quantite'];
			print("</td>\n");
			// Ajustement
			if ($produits_vrac!=0)
			{
				echo '<td colspan="1" rowspan="1" align="right">';
				// Ajustement automatique
				if ($resultat['commande_ajustement']>0)
				{
					$ajustement_plus += $resultat['commande_ajustement'];
					echo "+";
				}
				else
					$ajustement_moins -= $resultat['commande_ajustement'];
				echo $resultat['commande_ajustement'];
				echo "</td>\n";
			}
			// Prix
			echo '<td colspan="1" rowspan="1" align="right">';
			echo $produits_prix_udv*$resultat['commande_quantite'];
			print("</td>\n");
			echo '</tr>';
		}
	?>
	<tr valign="middle">
		<td colspan="1" rowspan="1" align="left">Total : </td>
		<td colspan="1" rowspan="1" align="right"><?php echo $total_quantite; ?></td>
		<?php 
		if ($produits_vrac!=0)
			echo "<td colspan='1' rowspan='1' align='right'>\n+$ajustement_plus/-$ajustement_moins\n</td>"; ?>
		<td colspan="1" rowspan="1" align="right"><?php echo $total_quantite*$produits_prix_udv; ?></td>
	</tr>
	</tbody>
</table>

<?php 
if ($produits_vrac!=0)
{
	$nb_conditionnement=floor($total_quantite/$produits_vrac);
	$reste_conditionnement=fmod($total_quantite,$produits_vrac);
	echo "Cela fait $nb_conditionnement conditionnement entier et il reste $reste_conditionnement.<p>\n";
	if ($reste_conditionnement==0)
	{
		if ( ($ajustement_plus==0) and ($ajustement_moins==0) )
		{
			// Commande déjà ajustée
			echo "Commande ajustée<p>\n";
		}
		else
		{
			echo "Pile-poil. Petit veinard !<p>\n";
			echo "<form action='".htmlspecialchars($_SERVER['PHP_SELF'])."?commission=$commission' method='post'>\n";
			echo "Remettre à 0 les ajustements ?\n";
			echo "<input type='hidden' name='produits_ID' value=$produits_ID>\n";
			echo "<input class='bouton' type='submit' name='nettoyer_ajustements' value='Valider'>\n";
			echo "</form>\n";
		}
	}
	elseif (($produits_vrac-$reste_conditionnement)<=$ajustement_plus)
	{
		// ajustement vers le haut
		$nb_conditionnement++;
		echo "Ajustement possible à la hausse avec $nb_conditionnement  conditionnement(s)<p>\n";
		// Formulaire
		echo "<form action='ajustement_automatique.php?commission=$commission' method='post'>\n";
		echo "<input type='hidden' name='produits_ID' value=$produits_ID>\n";
		echo "<input type='hidden' name='a_regler' value=".($produits_vrac-$reste_conditionnement).">\n";
		echo "<input class='bouton' type='submit' name='ajustement_haut' value='Ajustement automatique'>\n";
		echo "</form>\n";

		echo "<form action='ajustement_automatique_par_demi.php?commission=$commission' method='post'>\n";
		echo "<input type='hidden' name='produits_ID' value=$produits_ID>\n";
		echo "<input type='hidden' name='a_regler' value=".($produits_vrac-$reste_conditionnement).">\n";
		echo "<input class='bouton' type='submit' name='ajustement_haut' value='Ajustement automatique par demi UDV'>\n";
		echo "</form>\n";
	}
	elseif ($reste_conditionnement<=$ajustement_moins)
	{
		echo "Ajustement possible à la baisse avec $nb_conditionnement  conditionnement(s)<p>\n";
		// Formulaire
		echo "<form action='ajustement_automatique.php?commission=$commission' method='post'>\n";
		echo "<input type='hidden' name='produits_ID' value=$produits_ID>\n";
		echo "<input type='hidden' name='a_regler' value=$reste_conditionnement>\n";
		echo "<input class='bouton' type='submit' name='ajustement_bas' value='Ajustement automatique'>\n";
		echo "</form>\n";

		echo "<form action='ajustement_automatique_par_demi.php?commission=$commission' method='post'>\n";
		echo "<input type='hidden' name='produits_ID' value=$produits_ID>\n";
		echo "<input type='hidden' name='a_regler' value=$reste_conditionnement>\n";
		echo "<input class='bouton' type='submit' name='ajustement_bas' value='Ajustement automatique par demi UDV'>\n";
		echo "</form>\n";

	}
	else
		echo "Ajustement impossible :-( <p>\n";

}
		
// Modification manuelle
echo "<form action='ajustement_manuel.php?commission=$commission' method='post'>\n";
echo "<input type='hidden' name='produits_ID' value=$produits_ID>\n";
echo "<input class='bouton' type='submit' name='ajustement_manuel' value='Modifier manuellement'>\n";
echo "</form>\n";

// Basculement vers un autre produit
echo "<form action='basculement_commande.php?commission=$commission' method='post'>\n";
echo "Basculer tout ou partie de la commande vers un autre produit";
echo "<input type='hidden' name='produits_ID' value=$produits_ID>\n";
echo "<input class='bouton' type='submit' name='ajustement_manuel' value='Basculer'>\n";
echo "</form>\n";

// Suppression des commandes pour ce produit
echo "<form action='suppression_commande_produit.php?commission=$commission' method='post'>\n";
echo "Supprimer toutes les commandes de ce produit";
echo "<input type='hidden' name='produit_ID' value=$produits_ID>\n";
echo "<input class='bouton' type='submit' name='supprimer_commande_produit' value='OK'>\n";
echo "</form>\n";
?>

<p></p>

<form action="pdf_produit.php?commission=<?php echo $commission;?>" target='new' method="post">
<input type='hidden' name='produits_ID' value=<?php echo $produits_ID; ?> >
<input class='bouton' type='submit' name='validation_pdf' value='Créer au format PDF'>
</form>

<div align="center"><a href="gestion_commande.php?commission=<?php echo $commission; ?>">Retour à la gestion de commande</a></div>

</div>
</body>
 </html>
