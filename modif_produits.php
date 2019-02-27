<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);


// Après validation ou annulation : application éventuelle des modifs et retour à la gestion des produits
if (isset($_POST['valider']))
{
	
	// Ni vrac ni lot
	if  ($_POST['vrac']=="normal")
		$_POST['produits_vrac']=0;
	
	// Lot
	if  ($_POST['vrac']=="lot")
		$_POST['produits_vrac']=-$_POST['produits_vrac'];
	
	
	if (isset($_POST['actif']))
		$actif=1;
	else
		$actif=0;
		
	if ( (is_numeric($_POST['produits_prix_udv'])) and (is_numeric($_POST['produits_vrac'])) and (is_numeric($_POST['produits_TVA'])) )
	{
		$requete="update produits set produits_nom='".gestion_apostrophe($_POST['produits_nom'])."', produits_fournisseur='".$_POST['fournisseurs_ID']."', produits_rubrique='".$_POST['rubriques_ID']."', produits_conditionnement='".gestion_apostrophe($_POST['produits_conditionnement'])."', produits_udv='".gestion_apostrophe($_POST['produits_udv'])."', produits_prix_comparable='".$_POST['produits_prix_comparable']."', produits_prix_udv='".$_POST['produits_prix_udv']."', produits_description='".gestion_apostrophe($_POST['produits_description'])."', produits_vrac=".$_POST['produits_vrac'].",produits_TVA=".$_POST['produits_TVA'].", produits_actif=$actif where produits_ID=".$_POST['produits_ID'].";";
		//echo "*".$_POST['produits_description']."*<p>".$requete;		  		
		mysqli_query($link,$requete);
		header("Location: $BASE_URL/gestion_produits.php?commission=$commission#".$_POST['rubriques_ID']);
	}
	else
	{
		echo "<font color='red'>Le prix de l'U.D.V., le nombre d'U.D.V par conditionnement et la TVA doivent être des nombres<p>";
		echo "Le séparateur décimal est le point : \".\" .</font>";
		echo "Exemple : taper 1.5 et pas 1,5</font><p>";
		
		$produit['produits_nom']=$_POST['produits_nom'];
		$produit['produits_fournisseur']=$_POST['fournisseurs_ID'];
		$produit['produits_rubrique']=$_POST['rubriques_ID'];
		$produit['produits_conditionnement']=$_POST['produits_conditionnement'];
		$produit['produits_udv']=$_POST['produits_udv'];
		$produit['produits_prix_comparable']=$_POST['produits_prix_comparable'];
		$produit['produits_prix_udv']=$_POST['produits_prix_udv'];
		$produit['produits_ID']=$_POST['produits_ID'];
		$produit['produits_description']=$_POST['produits_description'];
		$produit['produits_actif']=$_POST['actif'];
		$produit['produits_TVA']=$_POST['produits_TVA'];
		if (is_numeric($_POST['produits_vrac']))
			$produit['produits_vrac']=$_POST['produits_vrac'];
		else
			$produit['produits_vrac']=0;
	}
}
elseif (isset($_POST['annuler']))
	header("Location: $BASE_URL/gestion_produits.php?commission=".$_GET['commission']."#".$_POST['rubriques_ID']);
else
{
	// Première fois sur la page :
	// Récupération des informations du produit modifié
	$requete = mysqli_query($link,"select * from produits where produits_ID=".$_POST['produit_ID'].";");
	$produit = mysqli_fetch_array($requete);
}

?>

<html> 
<head> 
<title>Modification d'un produit
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
	
<body onload="check()"> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<H2>Modification d'un produit
</H2>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?commission=".$_GET['commission']; ?>" method="post">
<input type=hidden name='produits_ID' value='<?php echo $_POST['produit_ID'];?>' >
<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Nom du produit</th>
			<th colspan="1">Fournisseur</th>
			<th colspan="1">Rubrique</th>
			<th colspan="1">Vrac ou lot</th>
			<th colspan="1">Nb d'UDV / cond.</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<tr valign="middle">
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='produits_nom' maxlength='200' value="<?php echo $produit['produits_nom'];?>">
			</td>
			<td colspan="1" rowspan="1" align="left">
				<select name='fournisseurs_ID'>
				<?php
				$requete = mysqli_query($link,"select fournisseurs_ID,fournisseurs_nom from fournisseurs order by fournisseurs_nom;");
				while (($resultat = mysqli_fetch_array($requete)))
				{
					$fournisseurs_ID=$resultat['fournisseurs_ID'];
					$fournisseurs_nom=$resultat['fournisseurs_nom'];
					if ($produit['produits_fournisseur']==$fournisseurs_ID)
						echo "<option value=".$fournisseurs_ID." selected>".$fournisseurs_nom."</option>";
					else
						echo "<option value=".$fournisseurs_ID.">".$fournisseurs_nom."</option>";
				}
				?>
				</select>
			</td>
			<td colspan="1" rowspan="1" align="left">
				<select name='rubriques_ID'>
				<?php
				$requete = mysqli_query($link,"select rubriques_ID,rubriques_nom from rubriques order by rubriques_nom;");
				while (($resultat = mysqli_fetch_array($requete)))
				{
					if ($produit['produits_rubrique']==$resultat['rubriques_ID'])
						echo "<option value=".$resultat['rubriques_ID']." selected>".$resultat['rubriques_nom']."</option>";
					else
						echo "<option value=".$resultat['rubriques_ID'].">".$resultat['rubriques_nom']."</option>";
				}
				?>
				</select>
			</td>
			<td colspan="1" rowspan="1" align="center">
				<input type="radio" name="vrac" value="normal"  <?php if ($produit['produits_vrac']==0) echo 'checked="checked"';?> onclick="disable_vrac()" />Ni vrac ni lot / 
				<input type="radio" name="vrac" value="lot"  <?php if ($produit['produits_vrac']<0) echo 'checked="checked"';?> onclick="enable_vrac()" />Lot / 
				<input type="radio" name="vrac" value="vrac"  <?php if ($produit['produits_vrac']>0) echo 'checked="checked"';?> onclick="enable_vrac()" />Vrac
			</td>
			<td colspan="1" rowspan="1" align="center">
				<input type='text' style='text-align:center;' name='produits_vrac' id='produits_vrac' maxlength='5' value="<?php echo abs($produit['produits_vrac']);?>" <?php if ($produit['produits_vrac']==0) echo 'disabled';?> size=2>
			</td>

		</tr>
	</tbody>
</table>
 <p><p>
<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Actif ?</th>
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
			<?php
			if ($produit["produits_actif"])
					echo "<input type='checkbox' name='actif' value=1 checked>";
				else
					echo "<input type='checkbox' name='actif' value=1>";
			?>
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' style='text-align:center;' name='produits_conditionnement' maxlength='100' value="<?php echo $produit['produits_conditionnement'];?>">
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' style='text-align:center;' name='produits_udv' maxlength='100' value="<?php echo $produit['produits_udv'];?>" size=15>
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' style='text-align:center;' name='produits_prix_comparable' maxlength='20' value="<?php echo $produit['produits_prix_comparable'];?>" size=15>
			</td>
			<td colspan="1" rowspan="1" align="center">
				<input type='float' style='text-align:center;' name='produits_prix_udv' maxlength='10' value="<?php echo $produit['produits_prix_udv'];?>" size=4>
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='float' style='text-align:center;' name='produits_TVA' maxlength='5' value="<?php echo $produit['produits_TVA'];?>" size=5>
			</td>
		</tr>
	</tbody>
</table>


<H3>Description du produit</H3>

<textarea rows='3' cols='50' name='produits_description'><?php 
if ($produit['produits_description']==NULL)
	echo "";
else 
	echo $produit['produits_description'];
?>
</textarea>

<?php 

if (isset($ajout_form_filtrage))
	echo $ajout_form_filtrage; 

?>


<input class='bouton' type='submit' name='valider' value='Valider'>
<input class='bouton' type='submit' name='annuler' value='Annuler'>
</form>


</div>
</body>
</html>	
