<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);
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
$fournisseur_ID=$_POST['fournisseurs_ID'];
//echo $fournisseur_ID."<p>";
$requete=mysqli_query($link, "select fournisseurs_nom from fournisseurs where fournisseurs_ID=$fournisseur_ID;");
$resultat=mysqli_fetch_array($requete);
$fournisseurs_nom=$resultat['fournisseurs_nom'];

//echo $fournisseur_ID." ".$commission;

if (isset($_POST['simple']))
{
	$requete=mysqli_query($link, "select produits_vrac,commande_produit,sum(commande_quantite),produits_nom,produits_prix_udv,produits_conditionnement,produits_TVA from commande,produits,fournisseurs where (commande_produit=produits_ID and produits_fournisseur=fournisseurs_ID and produits_fournisseur=fournisseurs_ID and fournisseurs_ID=$fournisseur_ID and produits_commission=$commission and produits_actif=1) group by commande_produit order by produits_nom, produits_conditionnement;");
	?>
	
	<table border="2">
		<thead valign="middle">
			<tr>
				<th colspan="1">Nom du produit</th>
				<th colspan="1">Conditionnement</th>
				<th colspan="1">Prix U.D.V. (HT)</th>
				<th colspan="1">Quantité</th>
				<th colspan="1">Prix (HT)</th>
				<th colspan="1">Prix (TTC)</th>
			</tr>
		</thead>
		<tbody valign="middle">
			<?php
			$total_HT=0;
			$total_TTC=0;
			while (($resultat = mysqli_fetch_array($requete)))
			{
				echo '<tr valign="middle">';
				// Nom
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat['produits_nom'];
				print("</td>\n");
				// Conditionnement
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat['produits_conditionnement'];
				print("</td>\n");
				$produits_vrac=abs($resultat['produits_vrac']);
	                        if ($produits_vrac!=0)
	                                {
	                                // Produit en vrac
	                                // prix d'un conditionnement
	                                echo '<td colspan="1" rowspan="1" align="right">';
	                                echo number_format(round($resultat['produits_prix_udv']*$produits_vrac/(1+$resultat['produits_TVA']/100),2),2,',',' ');
	                                echo '</td>';
	                                // on calcule le nombre de conditionnement a partir de la quantite commandee et du nombre d UDV par conditionnement
	                                echo '<td colspan="1" rowspan="1" align="center">';
	                                echo round($resultat['sum(commande_quantite)']/$produits_vrac,2);
	                                echo '</td>';
	                                // prix total
	                                echo '<td colspan="1" rowspan="1" align="right">';
									$total_HT +=round($resultat['produits_prix_udv']*$resultat['sum(commande_quantite)']/(1+$resultat['produits_TVA']/100),2);
	                                echo number_format(round($resultat['produits_prix_udv']*$resultat['sum(commande_quantite)']/(1+$resultat['produits_TVA']/100),2),2,',',' ');
	                                echo '</td>';
					echo '<td colspan="1" rowspan="1" align="right">';
					$total_TTC +=$resultat['produits_prix_udv']*$resultat['sum(commande_quantite)'];
	                                echo number_format($resultat['produits_prix_udv']*$resultat['sum(commande_quantite)'],2,',',' ');
	                                echo '</td>';
	                                }
	                        else
	                                {
	                                echo '<td colspan="1" rowspan="1" align="right">';
	                                echo number_format(round($resultat['produits_prix_udv']/(1+$resultat['produits_TVA']/100),2),2,',',' ');
	                                echo '</td>';
	                                echo '<td colspan="1" rowspan="1" align="center">';
	                                echo round($resultat['sum(commande_quantite)'],2);
	                                echo '</td>';
	                                echo '<td colspan="1" rowspan="1" align="right">';
	                                $total_HT +=round($resultat['produits_prix_udv']*$resultat['sum(commande_quantite)']/(1+$resultat['produits_TVA']/100),2);
	                                echo number_format(round($resultat['produits_prix_udv']*$resultat['sum(commande_quantite)']/(1+$resultat['produits_TVA']/100),2),2,',',' ');
					echo '</td>';
	                                echo '<td colspan="1" rowspan="1" align="right">';
	                                $total_TTC += $resultat['produits_prix_udv']*$resultat['sum(commande_quantite)'];
	                                echo number_format($resultat['produits_prix_udv']*$resultat['sum(commande_quantite)'],2,',',' ');
					echo '</td>';
	                                }
				echo '</tr>';
			}
		?>
		<tr valign="middle">
			<td colspan="4" rowspan="1" align="right">Total : </td>
			<td colspan="1" rowspan="1" align="right"><?php echo number_format($total_HT,2,',',' '); ?></td>
			<td colspan="1" rowspan="1" align="right"><?php echo number_format($total_TTC,2,',',' '); ?></td>
		</tr>
		</tbody>
	</table>
	<p></p>
	
	<b>Sortir au format PDF</b>
	<form action="pdf_fournisseur.php?commission=<?php echo $commission;?>" target='new' method="post">
	<input type='hidden' name=fournisseurs_ID value=<?php echo $fournisseur_ID; ?> >
	Entrer les coordonnées du représentant du groupement ?<input type='checkbox' name='entrer_coordonnees'>
	<p>
	<input class='bouton' type='submit' name='validation_pdf' value='OK'>
	</form>
<?php
}

if (isset($_POST['tableau']))
{
?>
	<H2>Bon de commande détaillé pour <?php echo $fournisseurs_nom; ?></H2>
	
	<?php
	// *********************************************
	// ** 		Bon détaillé en tableau      	  **
	// *********************************************
	
	echo "<h3>Tableau récapitulatif</h3>";
	
	$requete_commande = "select ID,produits_ID,produits_prix_udv,nom_complet,commande_quantite from produits,commande,membres where (produits_fournisseur=$fournisseur_ID and produits_ID=commande_produit and commande_membre=ID and produits_commission=$commission and produits_actif=1) order by nom_complet,produits_nom;";
	$requete_produits = "select produits_ID,produits_nom,produits_udv, produits_prix_udv from produits,commande where (produits_fournisseur=$fournisseur_ID and produits_ID=commande_produit and produits_commission=$commission and produits_actif=1) group by produits_nom,produits_udv,produits_conditionnement order by produits_nom;";
	
	$resultat_requete=mysqli_query($link,$requete_produits);
		
	// Si la requete est non vide
	if (mysqli_num_rows($resultat_requete)!=0)
	{
		echo "<table border='2'>\n";
		echo "<thead valign='middle'>\n";
		echo "<tr>\n";
		echo "<th>Nom</th>\n";
		$liste_produits=array();
		$nb_produits=0;
		while (($resultat = mysqli_fetch_array($resultat_requete)))
		{
			echo "<th>".$resultat['produits_nom']." (".$resultat['produits_prix_udv']." / ".$resultat['produits_udv'].")</th>\n";
			// Création d'un tableau contenant la liste des produits l'index correspondant à la colonne du tableau
			$liste_produits[++$nb_produits]=$resultat['produits_ID'];
		}
		echo "<th>Total</th>";
		echo "</tr>\n";
		echo "</thead>";
		
		// Remplissage des cases
		$resultat_requete=mysqli_query($link,$requete_commande);
		$resultat = mysqli_fetch_array($resultat_requete);
		$membre_old=$resultat['ID'];
		$total_TTC=0;
		$contenu_ligne=array_fill(1,$nb_produits,"");
		// Remplissage du tableau contenu ligne à partir du produit commandé
		for ($i=1;$i<=$nb_produits;$i++)
			{
				if ($resultat['produits_ID']==$liste_produits[$i])
				{
					$contenu_ligne[$i]=$resultat['commande_quantite'];
					$total_TTC+=$resultat['commande_quantite']*$resultat['produits_prix_udv'];
				}
			}
		echo "<tr>\n";
		echo "<td>".$resultat['nom_complet']."</td>";
		while($resultat = mysqli_fetch_array($resultat_requete))
		{	
			
			if ($membre_old!=$resultat['ID'])
			{
				// Remplissage du tableau à aficher avec les valeurs mémorisée dans le tableau $contenu_ligne
				for ($i=1;$i<=$nb_produits;$i++)
					echo "<td>".$contenu_ligne[$i]."</td>";
				echo "<td>".affiche_euro($total_TTC)."</td>";	
				echo "</tr>";	// Fermeture de la ligne 
				$contenu_ligne=array_fill(1,$nb_produits,"");	// Reset du tableau
				$membre_old=$resultat['ID'];
				$total_TTC=0;
				// Nouvelle ligne du tableau
				echo "<tr>\n";
				echo "<td>".$resultat['nom_complet']."</td>";
			}
			// Remplissage du tableau contenu ligne à partir du produit commandé
			for ($i=1;$i<=$nb_produits;$i++)
			{
				if ($resultat['produits_ID']==$liste_produits[$i])
				{
					$contenu_ligne[$i]=$resultat['commande_quantite'];
					$total_TTC+=$resultat['commande_quantite']*$resultat['produits_prix_udv'];
				}
			}
			
		}
		// Remplissage du tableau avec les valeurs mémorisée dans le tableau
		for ($i=1;$i<=$nb_produits;$i++)
			echo "<td>".$contenu_ligne[$i]."</td>";
		echo "<td>".affiche_euro($total_TTC)."</td>";	
		echo "</tr></td>";
		echo "</table>\n";
	}
	//print_r($liste_produits);
}

	// *********************************************
	// ** 		Bon détaillé par commande famille      	  **
	// *********************************************

if (isset($_POST['detail']))
{
	
	echo "<h3>Bon de commande par famille pour $fournisseurs_nom</h3>";
	
	$requete = "select ID,nom_complet,produits_nom,produits_conditionnement,produits_udv,produits_prix_udv,commande_quantite from produits,commande,membres where (produits_fournisseur=$fournisseur_ID and produits_ID=commande_produit and commande_membre=ID and produits_commission=$commission and produits_actif=1) order by nom_complet,produits_nom;";


	//echo $requete;
	
	$resultat_requete=mysqli_query($link,$requete);
	$membre_old='';
	
	// Si la requete est non vide
	if (mysqli_num_rows($resultat_requete)!=0)
	{
		while($resultat = mysqli_fetch_array($resultat_requete))
		{
			if ($membre_old=='')
			{
				$membre_old=$resultat['ID'];
				// premier membre
				echo "<H3>".$resultat['nom_complet']."</H3>";
				echo "<table border='2'>\n";
				echo "<thead valign='middle'>\n";
				echo "<tr>\n";
				echo "<th>Nom du produit</th>\n";
				echo "<th>Conditionnement</th>\n";
				echo "<th>Quantité</th>\n";
				echo "<th>Unité de vente</th>\n";
				echo "<th>Prix de l'U.D.V (en €)</th>\n";
				echo "<th>Total (en €)</th>\n";
				echo "</tr>\n";
				echo "</thead>\n";
				echo "<tbody valign='middle'>\n";
				// Premier produit
				$total=0;
				echo "<tr>\n";
				echo "<td>".$resultat['produits_nom']."</td>\n";
				echo "<td>".$resultat['produits_conditionnement']."</td>\n";
				echo "<td align='center'>".$resultat['commande_quantite']."</td>\n";
				echo "<td>".$resultat['produits_udv']."</td>\n";
				echo "<td align='right'>".affiche_euro($resultat['produits_prix_udv'])."</td>\n";
				$prix=$resultat['commande_quantite']*$resultat['produits_prix_udv'];
				$total+=$prix;
				echo "<td align='right'>$prix</td>\n";
				echo "</tr>\n";
			}
			elseif ($resultat['ID']!==$membre_old)
			{
				// On cloture le tableau précédent
				// total fournisseur
				echo "<tr><td colspan='5' align='right'><b>Total commande</b></td><td align='right'>".affiche_euro($total)."</td></tr>";
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
				echo "<th>Nom du produit</th>\n";
				echo "<th>Conditionnement</th>\n";
				echo "<th>Quantité</th>\n";
				echo "<th>Unité de vente</th>\n";
				echo "<th>Prix de l'U.D.V (en €)</th>\n";
				echo "<th>Total (en €)</th>\n";
				echo "</tr>\n";
				echo "</thead>\n";
				echo "<tbody valign='middle'>\n";
				// Premier produit
				$total=0;
				echo "<tr>\n";
				echo "<td>".$resultat['produits_nom']."</td>\n";
				echo "<td>".$resultat['produits_conditionnement']."</td>\n";
				echo "<td align='center'>".$resultat['commande_quantite']."</td>\n";
				echo "<td>".$resultat['produits_udv']."</td>\n";
				echo "<td align='right'>".affiche_euro($resultat['produits_prix_udv'])."</td>\n";
				$prix=$resultat['commande_quantite']*$resultat['produits_prix_udv'];
				$total+=$prix;
				echo "<td align='right'>".affiche_euro($prix)."</td>\n";
				echo "</tr>\n";
			}
			else
			{
				// Nouvelle ligne produit
				echo "<tr>\n";
				echo "<td>".$resultat['produits_nom']."</td>\n";
				echo "<td>".$resultat['produits_conditionnement']."</td>\n";
				echo "<td align='center'>".$resultat['commande_quantite']."</td>\n";
				echo "<td>".$resultat['produits_udv']."</td>\n";
				echo "<td align='right'>".affiche_euro($resultat['produits_prix_udv'])."</td>\n";
				$prix=$resultat['commande_quantite']*$resultat['produits_prix_udv'];
				$total+=$prix;
				echo "<td align='right'>".affiche_euro($prix)."</td>\n";
				echo "</tr>\n";
			}
		}
	// On cloture le tableau précédent
	echo "<tr><td colspan='5' align='right'><b>Total commande</b></td><td align='right'>".affiche_euro($total)."</td></tr>";
	echo "</tbody>\n";
	echo "</table>\n";
	}

}?>



<div align="center"><a href="gestion_commande.php?commission=<?php echo $commission; ?>">Retour à la gestion de commande</a></div>

</div>
 </body>
 </html>
