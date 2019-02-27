<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);

$actif_inactif_selectionne = 0;

// Si le filtrage a été demandé

$where_filtrage='';		// variable à ajouter à la requete de construction du tableau

if (isset($_POST['Filtrer']))
	{
	// On vient de modifier les conditions de filtrage

	// Remise à zero de la variable session avant éventuelle mise à jour
	if (isset($_SESSION['filtrage_fournisseur']))
		{
		unset($_SESSION['filtrage_fournisseur']);
		}

	if (isset($_SESSION['filtrage_rubrique']))
		{
		unset($_SESSION['filtrage_rubrique']);
		}

	if (isset($_SESSION['filtrage_actif_inactif']))
		{
		unset($_SESSION['filtrage_actif_inactif']);
		}

	if ($_POST['filtrage_fournisseur']!=0)
		{
		// positionnement du menu sur le fournisseur selectionné
		$fournisseur_selectionne=$_POST['filtrage_fournisseur'];
		
		// Stockage de l'info dans le tableau session
		$_SESSION['filtrage_fournisseur']=$fournisseur_selectionne;
		
		// Ajout des options de filtrage pour la requete		
		$where_filtrage.=" and produits_fournisseur=".$fournisseur_selectionne;
		}
	if ($_POST['filtrage_rubrique']!=0)
		{
		// positionnement du menu sur le fournisseur selectionné
		$rubrique_selectionne=$_POST['filtrage_rubrique'];
		
		// Stockage de l'info dans le tableau session
		$_SESSION['filtrage_rubrique']=$rubrique_selectionne;
		
		// Ajout des options de filtrage pour la requete		
		$where_filtrage.=" and produits_rubrique=".$rubrique_selectionne;
		}
	if ($_POST['filtrage_actif_inactif']!=0)
		{
		// positionnement du menu sur le type de produits sélectionné
		$actif_inactif_selectionne=$_POST['filtrage_actif_inactif'];

		// Stockage de l'info dans le tableau session
		$_SESSION['filtrage_actif_inactif']=$actif_inactif_selectionne;
		
		// Ajout des options de filtrage pour la requete		
		switch ($actif_inactif_selectionne)
			{
			case 1 :
				$where_filtrage.=" and produits_actif=1";
				break;
			case 2 :
				$where_filtrage.=" and produits_actif=0";
				break;
			}
		}
	}
else
	{
	// Si on ne vient pas de modifier les conditions de filtrage, on vérifie que la variable session ne contient pas des infos
	if (isset($_SESSION['filtrage_fournisseur']))
		{
		// positionnement du menu sur le fournisseur selectionné
		$fournisseur_selectionne=$_SESSION['filtrage_fournisseur'];
		// Ajout des options de filtrage pour la requete		
		$where_filtrage.=" and produits_fournisseur=".$fournisseur_selectionne;
		}

	if (isset($_SESSION['filtrage_rubrique']))
		{
		// positionnement du menu sur le fournisseur selectionné
		$rubrique_selectionne=$_SESSION['filtrage_rubrique'];
		
		// Ajout des options de filtrage pour la requete		
		$where_filtrage.=" and produits_rubrique=".$rubrique_selectionne;
		}	

	if (isset($_SESSION['filtrage_actif_inactif']))
		{
		// positionnement du menu sur le type de produits sélectionné
		$actif_inactif_selectionne=$_SESSION['filtrage_actif_inactif'];
		
		// Ajout des options de filtrage pour la requete		
		switch ($actif_inactif_selectionne)
			{
			case 1 :
				$where_filtrage.=" and produits_actif=1";
				break;
			case 2 :
				$where_filtrage.=" and produits_actif=0";
				break;
			}
		}	
	}
?>

<html> 
<head> 
<title>Editition de la liste des <?php echo $nom_commission[$commission];?>
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<H2>Edition de la liste des <?php echo $nom_commission[$commission]; ?>
</H2>

<?php 
// Bouton pour l'ajout d'un produit
echo "<form action='ajout_produit.php?commission=$commission' method='post'>\n";
if(isset($ajout_form_filtrage))
	echo $ajout_form_filtrage;
echo "<input class='bouton' type='submit' value='Ajouter un produit' />\n";
echo "</form>  \n";
?>

<form action='gestion_produits.php?commission=<?php echo $commission;?>' method='post'>
Filtrer les produits par fournisseur 
<select name='filtrage_fournisseur'>
<option value=0>Tous</option>
<?php
$requete = mysqli_query($link,"select fournisseurs_nom,fournisseurs_ID from fournisseurs,produits where fournisseurs_ID=produits_fournisseur and produits_commission=$commission group by fournisseurs_nom order by fournisseurs_nom;");
				while (($resultat = mysqli_fetch_array($requete)))
				{
					if ($fournisseur_selectionne==$resultat['fournisseurs_ID'])
						echo "<option value=".$resultat['fournisseurs_ID']." selected>".$resultat['fournisseurs_nom']."</option>";
					else
						echo "<option value=".$resultat['fournisseurs_ID'].">".$resultat['fournisseurs_nom']."</option>";
				}
?>
</select>
et/ou rubrique 
<select name='filtrage_rubrique'>
<option value=0>Tous</option>
<?php
$requete = mysqli_query($link,"select rubriques_nom,rubriques_ID from rubriques,produits where rubriques_ID=produits_rubrique and produits_commission=$commission group by rubriques_nom order by rubriques_nom;");
				while (($resultat = mysqli_fetch_array($requete)))
				{
					if ($rubrique_selectionne==$resultat['rubriques_ID'])
						echo "<option value=".$resultat['rubriques_ID']." selected>".$resultat['rubriques_nom']."</option>";
					else
						echo "<option value=".$resultat['rubriques_ID'].">".$resultat['rubriques_nom']."</option>";
				}
?>
</select>
et/ou actifs/inactifs
<select name='filtrage_actif_inactif'>
<option value=0>Tous</option>
<option value=1 <?php if ($actif_inactif_selectionne==1) {echo " selected";} ?> >Actifs</option>
<option value=2 <?php if ($actif_inactif_selectionne==2) {echo " selected";} ?> >Inactifs</option>
</select>
<input class='bouton' type='submit' name='Filtrer' value='Filtrer' >
</form>
			
				
<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Nom du produit</th>
			<th colspan="1">Fournisseur</th>
			<th colspan="1">Vrac ou lot ?</th>
			<th colspan="1">Conditionnement</th>
			<th colspan="1">Unité de vente</th>
			<th colspan="1">Prix comparable</th>
			<th colspan="1">Prix de l'U.D.V (HT)</th>
			<th colspan="1">TVA</th>
			<th colspan="1">Prix de l'U.D.V (TTC)</th>
			<th colspan="1">Actif ?</th>
			<th colspan="2">Modifier / Supprimer</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<?php
			
			// Remplissage du tableau
			$requete = "select * from (produits left join fournisseurs on produits.produits_fournisseur=fournisseurs.fournisseurs_ID  left join rubriques on produits.produits_rubrique=rubriques.rubriques_ID) where (produits_commission=$commission $where_filtrage) order by rubriques_nom,fournisseurs_nom,produits_nom;";

			$retour_requete=mysqli_query($link,$requete);

			$old_rubrique='';
			while(($resultat = mysqli_fetch_array($retour_requete)))
			{
				// Affichage de la rubrique au besoin
				if ($resultat['rubriques_nom']!=$old_rubrique)
				{
					$old_rubrique=$resultat['rubriques_nom'];
					echo "<tr valign='middle'>\n";
					echo '<td colspan="10" rowspan="1" align="center">';
					echo "<div id=".$resultat['rubriques_ID']."><strong><font color='blue'>$old_rubrique</font></strong></div>";
					echo "</td>\n";
					echo "</tr>\n";
				}
				echo '<tr valign="middle">';
				// Nom
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat["produits_nom"];
				if ($resultat['produits_description']!='')
					{
					$toto=$resultat['produits_ID'];
					$POPUP = <<<BIDON
<a href="javascript:void window.open('popup_info_produit.php?produits_ID=$toto','Fiche','toolbar=no,status=no,width=300 ,height=300,scrollbars=yes,location=no,resize=yes,menubar=no,top=200,left=400')"><img src='infos.gif'></a>\n 
BIDON;
					echo $POPUP;
					}
				echo '</td>';
				// Fournisseur
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat["fournisseurs_nom"];
				echo '</td>';
				//  Vrac ou lot ?
				echo '<td colspan="1" rowspan="1" align="left">';
				if ($resultat["produits_vrac"]==0)
					echo "Non";
				elseif ($resultat["produits_vrac"]<0)
					echo "Oui : Lot de ".abs($resultat["produits_vrac"])." U.D.V. par conditionnement";
				else
					echo "Oui : Vrac de ".$resultat["produits_vrac"]." U.D.V. par conditionnement";
				echo '</td>';
				//  Conditionnement
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat["produits_conditionnement"];
				echo '</td>';
				//  UDV
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat["produits_udv"];
				echo '</td>';
				//  Prix au kg
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat["produits_prix_comparable"];
				echo '</td>';
				//  Prix de l'UDV HT
				echo '<td colspan="1" rowspan="1" align="right">';
				echo number_format($resultat["produits_prix_udv"]/(1+$resultat["produits_TVA"]/100),2,',',' ');
				echo '</td>';
				//  TVA
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat["produits_TVA"];
				echo '</td>';
				//  Prix de l'UDV TTC
				echo '<td colspan="1" rowspan="1" align="right">';
				echo number_format($resultat["produits_prix_udv"],2,',',' ');
				echo '</td>';
				//  Actif
				echo '<td colspan="1" rowspan="1" align="center">';
				if ($resultat["produits_actif"])
					echo "<input type='checkbox' name='actif' value=1 checked disabled>";
				else
					echo "<input type='checkbox' name='actif' value=1 disabled>";
				echo '</td>';
				//  Modifier
				echo '<td colspan="1" rowspan="1" align="center" border-width=0>';
				echo "<form action='modif_produits.php?commission=$commission' method='post'>\n";
				$produit_ID=$resultat['produits_ID'];
				echo "<input type=hidden name='produit_ID' value=$produit_ID >\n";
				//echo "<input class='bouton' type='submit' name='submit' value='Modifier' />\n";
				echo "<input type='image' name='modifier' value='Valider' src='9070.ico'>";
				if(isset($ajout_form_filtrage))
					echo $ajout_form_filtrage;
				echo "</form>  \n";
				echo '</td>';
				//  Supprimer
				echo '<td colspan="1" rowspan="1" align="center">';
				echo "<form action='suppression_produit.php?commission=$commission' method='post'>\n";
				echo "<input type=hidden name='produit_ID' value=$produit_ID >\n";
				//echo "<input class='bouton' type='submit' name='submit' value='Supprimer' />\n";
				echo "<input type='image' name='supprimer' value='Valider' src='picto_poubelle_big.gif'>";
				if(isset($ajout_form_filtrage))
					echo $ajout_form_filtrage;
				echo "</form>  \n";
				echo '</td>';
				echo "</tr>";
			}?>
		</tbody>
	</table>
	
			
</div>
</body>
</html>
