<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);


if (isset($_POST['valider_tableau']))
	{
	$fournisseur=isset($_POST['fournisseur'])? $_POST['fournisseur']:NULL;
    $produits_verif=isset($_POST['produits_verif'])? $_POST['produits_verif']:NULL;
    $commande_passee=isset($_POST['commande_passee'])? $_POST['commande_passee']:NULL;
    $commande_recue=isset($_POST['commande_recue'])? $_POST['commande_recue']:NULL;
    $facture_verif=isset($_POST['facture_verif'])? $_POST['facture_verif']:NULL;

	// On créé une entrée bidon par tableau pour le cas où il serait vide (pour régler une erreur chez Free avec in_array)
	$produits_verif[]=-1;
	$commande_passee[]=-1;
	$commande_recue[]=-1;
	$facture_verif[]=-1;

	for ($ii=0;$ii<$_POST['nb_fournisseurs'];$ii++)
		{
		(in_array($fournisseur[$ii],$produits_verif)) ? $pv=1 : $pv=0;
		(in_array($fournisseur[$ii],$commande_passee)) ? $cp=1 : $cp=0;
		(in_array($fournisseur[$ii],$commande_recue)) ? $cr=1 : $cr=0;
		(in_array($fournisseur[$ii],$facture_verif)) ? $fv=1 : $fv=0;
		mysqli_query($link, "update fournisseurs set fournisseurs_produits_verifies='$pv', fournisseurs_commande_passee='$cp', fournisseurs_commande_recue='$cr', fournisseurs_facture_verifiee='$fv' where fournisseurs_id='".$fournisseur[$ii]."';");
		//echo "update fournisseurs set 
		}
	}

if (isset($_POST['vider_tableau']))
	{
	$fournisseur=$_POST['fournisseur'];

	for ($ii=0;$ii<$_POST['nb_fournisseurs'];$ii++)
		{
		$pv=0;
		$cp=0;
		$cr=0;
		$fv=0;
		mysqli_query($link, "update fournisseurs set fournisseurs_produits_verifies='$pv', fournisseurs_commande_passee='$cp', fournisseurs_commande_recue='$cr', fournisseurs_facture_verifiee='$fv' where fournisseurs_id='".$fournisseur[$ii]."';");
		//echo "update fournisseurs set 
		}
	}

?>

<html> 
<head> 
<title>Tableau de gestion des commandes pour les fournisseurs de la commission <?php echo $nom_commission[$commission]; ?>
</title> 
<link rel="stylesheet" href="style.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  

<body>
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">


<H2>Tableau de gestion des commandes pour les fournisseurs de la commission <?php echo $nom_commission[$commission]; ?></H2>


<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?commission=".$commission; ?>" method="post">
<?php
$requete = mysqli_query($link, "select fournisseurs_id, fournisseurs_produits_verifies, fournisseurs_commande_passee, fournisseurs_commande_recue, fournisseurs_facture_verifiee, fournisseurs_nom from fournisseurs, produits where fournisseurs_ID=produits_fournisseur and produits_commission=$commission group by fournisseurs_nom order by fournisseurs_nom;");
echo "<input type='hidden' name='nb_fournisseurs' value='".mysqli_num_rows($requete)."'>"
?>
<table border='0' style="border: 0px;">
	<thead valign="middle">
		<tr>
			<th colspan="1">Fournisseur</th>
			<th colspan="1">Produits vérifiés</th>
			<th colspan="1">Commande passée</th>
			<th colspan="1">Commande reçue et vérifiée</th>
			<th colspan="1">Facture vérifiée </th>
		</tr>
	</thead>
	<tbody>
<?php 
while(($resultat = mysqli_fetch_array($requete)))
			{
			echo '<tr valign="middle">';
			echo '<td colspan="1" rowspan="1" align="center">';
			$id=$resultat['fournisseurs_id'];
			echo "<input type='hidden' name='fournisseur[]' value='$id'>";
			echo $resultat['fournisseurs_nom'];
			echo '</td>';
			echo '<td colspan="1" rowspan="1" align="center">';
			if ($resultat['fournisseurs_produits_verifies'])
				echo "<input type='checkbox' name='produits_verif[]' value=$id checked>";
			else
				echo "<input type='checkbox' name='produits_verif[]' value=$id>";
			echo '</td>';
			echo '<td colspan="1" rowspan="1" align="center">';
			if ($resultat['fournisseurs_commande_passee'])
				echo "<input type='checkbox' name='commande_passee[]' value=$id checked>";
			else
				echo "<input type='checkbox' name='commande_passee[]' value=$id>";
			echo '</td>';
			echo '<td colspan="1" rowspan="1" align="center">';
			if ($resultat['fournisseurs_commande_recue'])
				echo "<input type='checkbox' name='commande_recue[]' value=$id checked>";
			else
				echo "<input type='checkbox' name='commande_recue[]' value=$id>";
			echo '</td>';
			echo '<td colspan="1" rowspan="1" align="center">';
			if ($resultat['fournisseurs_facture_verifiee'])
				echo "<input type='checkbox' name='facture_verif[]' value=$id checked>";
			else
				echo "<input type='checkbox' name='facture_verif[]' value=$id>";
			echo '</td>';
			echo "</tr>";
			}
?>
</tbody>
</table>
<input class="bouton" type="submit" name="valider_tableau" value="Valider">
&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp<input class="bouton" type="submit" name="vider_tableau" value="Vider"> <font color='red'><small>Attention : pas de confirmation demandée</small></font>
</form>

</div>
</body>
</html>
