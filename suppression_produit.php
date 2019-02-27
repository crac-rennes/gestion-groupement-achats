<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);

if (isset($_POST['oui']))
{
	$produit_ID=$_POST['produit_ID'];
	$requete = mysqli_query($link, "delete from produits where produits_ID=$produit_ID;");
	header("Location: $BASE_URL/gestion_produits.php?commission=".$_GET['commission']);
}
elseif (isset($_POST['non']))
{
	header("Location: $BASE_URL/gestion_produits.php?commission=".$_GET['commission']);
}
?>

<html> 
<head> 
<title>Suppression du produit
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<font color='red'> Vous allez supprimer le produit : </font><p>

<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Nom du produit</th>
			<th colspan="1">Fournisseur</th>
			<th colspan="1">Conditionnement</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<?php
		$produit_ID=$_POST['produit_ID'];
		
		// Remplissage du tableau
		$requete = mysqli_query($link, "select produits_nom,fournisseurs_nom,produits_conditionnement from (produits left join fournisseurs on produits.produits_fournisseur=fournisseurs.fournisseurs_ID)  where produits_ID=$produit_ID;");
		$resultat = mysqli_fetch_array($requete);
		echo '<tr valign="middle">';
		// Nom
		echo '<td colspan="1" rowspan="1" align="left">';
		echo $resultat["produits_nom"];
		echo "</td>\n";
		// Fournisseur
		echo '<td colspan="1" rowspan="1" align="left">';
		echo $resultat["fournisseurs_nom"];
		echo "</td>\n";
		//  Conditionnement
		echo '<td colspan="1" rowspan="1" align="left">';
		echo $resultat["produits_conditionnement"];
		echo "</td>\n";
		echo "</tr>\n";
		?>
	</tbody>
</table>

<font color='red'> Souhaitez vous poursuivre ? </font><p>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?commission=".$_GET['commission']; ?>" method="post">
	<fieldset style="width: 120px; border-color:#FF0000">
		<center>       
		<input type="hidden" name="produit_ID" value="<?php echo $produit_ID; ?>">
		<input class="bouton" type="submit" name="oui" value="Oui" />        
		<input class="bouton" type="submit" name="non" value="Non" />        
		</center>
	</fieldset>    
</form>  


</div>
</body>
</html>
