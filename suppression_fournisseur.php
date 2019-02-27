<?php
session_start();
require("fonctions.php");
verification_identification();
verification_simple_membre();

if (isset($_POST['oui']))
{
	mysqli_query($link, "delete from fournisseurs where fournisseurs_ID=".$_POST['fournisseurs_ID'].";");
	mysqli_query($link, "delete from produits where produits_fournisseur=".$_POST['fournisseurs_ID'].";");
	header("Location: $BASE_URL/gestion_fournisseurs.php");
}
elseif (isset($_POST['non']))
	header("Location: $BASE_URL/gestion_fournisseurs.php");

?>

<html> 
<head> 
<title>Suppression d'un fournisseur
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<font color='red'> Vous allez supprimer le fournisseur : </font><p>

<?php
$requete = mysqli_query($link, "select fournisseurs_nom from fournisseurs where fournisseurs_ID=".$_POST['fournisseurs_ID'].";");
$resultat = mysqli_fetch_array($requete);
echo $resultat["fournisseurs_nom"];

$requete = mysqli_query($link, "select produits_nom, produits_commission from produits where produits_fournisseur=".$_POST['fournisseurs_ID'].";");

if (mysqli_num_rows($requete))
{
?>
<p>

Cela supprimera également les produits suivants :

<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Produit</th>
			<th colspan="1">Commission</th>
		</tr>
	</thead>
	<tbody valign="middle">
<?php
$total=0;
$nb_commandes=0;
while (($resultat = mysqli_fetch_array($requete)))
{
		echo "<tr><td>".$resultat['produits_nom']."</td><td>".$nom_commission[$resultat['produits_commission']]."</td></tr>\n";
}	
echo "</tbody></table>";
}
?>

<p>
<font color='red'> Souhaitez vous poursuivre ? </font><p>

<form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>' method="post">
	<fieldset> 	
		<input type="hidden" name="fournisseurs_ID" value="<?php echo $_POST['fournisseurs_ID']; ?>">
		<input class="bouton" type="submit" name="oui" value="Oui" />        
		<input class="bouton" type="submit" name="non" value="Non" />        
	</fieldset>    
</form>  


</div>
</body>
</html>
