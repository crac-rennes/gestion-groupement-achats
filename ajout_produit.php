<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);

if (isset($_POST['valider']))
{
	$produits_nom=$_POST['produits_nom'];
	$fournisseurs_ID=$_POST['fournisseurs_ID'];
	$rubriques_ID=$_POST['rubriques_ID'];
	$produits_conditionnement=$_POST['produits_conditionnement'];
	$produits_udv=$_POST['produits_udv'];
	$produits_prix_udv=$_POST['produits_prix_udv'];
	$produits_prix_comparable=$_POST['produits_prix_comparable'];
	$produits_commission=$_POST['commission'];
	$produits_description=$_POST['produits_description'];
	$produits_vrac=$_POST['produits_vrac'];
	$produits_TVA=$_POST['produits_TVA'];
	// Si vrac décoché 

	// Ni vrac ni lot
	if  ($_POST['vrac']=="normal")
		$produits_vrac=0;
	
	// Lot
	if  ($_POST['vrac']=="lot")
		$produits_vrac=-$_POST['produits_vrac'];

	
	if ( (is_numeric($produits_prix_udv)) and (is_numeric($produits_vrac)) and (is_numeric($produits_TVA)) )
	{
		mysqli_query($link, "insert into produits (produits_commission, produits_nom, produits_fournisseur, produits_rubrique, produits_conditionnement, produits_udv, produits_prix_udv, produits_prix_comparable,produits_description,produits_vrac, produits_actif, produits_TVA) values ($produits_commission, '".gestion_apostrophe($produits_nom)."', $fournisseurs_ID, $rubriques_ID, '".gestion_apostrophe($produits_conditionnement)."' ,'".gestion_apostrophe($produits_udv)."' ,'$produits_prix_udv' ,'$produits_prix_comparable','".gestion_apostrophe($produits_description)."',$produits_vrac,1,$produits_TVA);");
		header("Location: $BASE_URL/gestion_produits.php?commission=$commission");
	}
	else
	{
		echo "<font color='red'>Le prix de l'U.D.V., le nombre d'U.D.V par conditionnement et la TVA doivent être des nombres<p>";
		echo "Le séparateur décimal est le point : \".\" .</font>";
	}
}
elseif (isset($_POST['annuler']))
	header("Location: $BASE_URL/gestion_produits.php?commission=$commission");
else
{
	//echo "Pas de post de produit";
	$produits_nom='';
	$fournisseurs_ID='';
	$rubriques_ID='';
	$produits_conditionnement='';
	$produits_udv='';
	$produits_prix_udv='';
	$produits_prix_comparable='';
	$produits_commission='';
	$produits_description='';
	$produits_vrac=0;
	$produits_TVA=5.5;
}	
?>

<html> 
<head> 
<title>Ajout d'un produit dans la commission des <?php echo $nom_commission[$commission]; ?>
</title> 
<script language="javascript">
function enable_vrac()
{
document.getElementById("produits_vrac").disabled=false;
}

function disable_vrac()
{
document.getElementById("produits_vrac").disabled=true;
}
</script>
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" > 
<link rel="icon" type="image/png" href="logo.png" />

</head> 

<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<H2>Ajout d'un produit dans la commission des <?php echo $nom_commission[$commission]; ?>
</H2>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?commission=".$_GET['commission']; ?>" method="post" ID="def_produit">
<input type='hidden' name='commission' value='<?php echo $commission;?>'>
<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Nom du produit</th>
			<th colspan="1">Fournisseur</th>
			<th colspan="1">Rubrique</th>
			<th colspan="1">Vrac ou lot</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<tr valign="middle">
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='produits_nom' maxlength='200' value="<?php echo $produits_nom; ?>">
			</td>
			<td colspan="1" rowspan="1" align="left">
				<select name='fournisseurs_ID'>
				<?php
/*				if ($fournisseurs_ID!='')
				{
					$requete = mysqli_query($link, "select fournisseurs_ID,fournisseurs_nom from fournisseurs where fournisseurs_ID=$fournisseurs_ID;");
					$resultat = mysqli_fetch_array($requete);
					echo "<option value=".$fournisseurs_ID.">".$resultat['fournisseurs_nom']."</option>";
				}*/
				$requete = mysqli_query($link, "select fournisseurs_ID,fournisseurs_nom from fournisseurs order by fournisseurs_nom;");
				while (($resultat = mysqli_fetch_array($requete)))
				{
					if ($fournisseurs_ID==$resultat['fournisseurs_ID'])
						echo "<option value=".$resultat['fournisseurs_ID']." selected>".$resultat['fournisseurs_nom']."</option>";
					else
						echo "<option value=".$resultat['fournisseurs_ID'].">".$resultat['fournisseurs_nom']."</option>";
				}
				?>
				</select>
			</td>
			<td colspan="1" rowspan="1" align="left">
				<select name='rubriques_ID'>
				<?php
				if ($rubriques_ID!='')
				{
					$requete = mysqli_query($link, "select rubriques_ID,rubriques_nom from rubriques where rubriques_ID=$rubriques_ID;");
					$resultat = mysqli_fetch_array($requete);
					echo "<option value=".$rubriques_ID.">".$resultat['rubriques_nom']."</option>";
				}
				$requete = mysqli_query($link, "select rubriques_ID,rubriques_nom from rubriques order by rubriques_nom;");
				while (($resultat = mysqli_fetch_array($requete)))
				{
					echo "<option value=".$resultat['rubriques_ID'].">".$resultat['rubriques_nom']."</option>";
				}
				?>
				</select>
			</td>
			<td colspan="1" rowspan="1" align="center">
				<input type="radio" name="vrac" value="normal" checked="checked" onclick="disable_vrac()" />Ni vrac ni lot / 
				<input type="radio" name="vrac" value="lot" onclick="enable_vrac()" />Lot / 
				<input type="radio" name="vrac" value="vrac" onclick="enable_vrac()" />Vrac
			</td>
		</tr>
	</tbody>
</table>
<p><p>
<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Nb d'UDV / conditionnement</th>
			<th colspan="1">Conditionnement</th>
			<th colspan="1">Unité de vente</th>
			<th colspan="1">Pour comparer</th>
			<th colspan="1">Prix de l'U.D.V (en €)</th>
			<th colspan="1">TVA (%)</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<tr valign="middle">
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='produits_vrac' id='produits_vrac' maxlength='3' size='3' value='0' disabled>
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='produits_conditionnement' maxlength='100' value="<?php echo $produits_conditionnement; ?>">
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='produits_udv' maxlength='100' value="<?php echo $produits_udv; ?>" size=20>
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='float' name='produits_prix_comparable' maxlength='100' value="<?php echo $produits_prix_comparable; ?>" size=15>
			</td>
			<td colspan="1" rowspan="1" align="center">
				<input type='text' name='produits_prix_udv' maxlength='100' value="<?php echo $produits_prix_udv; ?>" size=5>
			</td>
			<td colspan="1" rowspan="1" align="center">
				<input type='float' style='text-align:center;' name='produits_TVA' maxlength='5' value="<?php echo $produits_TVA; ?>" size=5>
			</td>
		</tr>
	</tbody>
</table>




<H3>Description du produit</H3>

<textarea rows='3' cols='50' name='produits_description'><?php echo $produits_description;?></textarea>
<input class='bouton' type='submit' name='valider' value='Valider'>
<input class='bouton' type='submit' name='annuler' value='Annuler'>
</form>

</div>
</body>
</html>	
