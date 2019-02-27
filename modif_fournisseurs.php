<?php
session_start();
require("fonctions.php");
verification_identification();
verification_simple_membre();


if (isset($_POST['valider']))
	{
	mysqli_query($link, "update fournisseurs set fournisseurs_nom='".gestion_apostrophe($_POST['fournisseurs_nom'])."', fournisseurs_adresse='".gestion_apostrophe($_POST['fournisseurs_adresse'])."', fournisseurs_commune='".gestion_apostrophe($_POST['fournisseurs_commune'])."', fournisseurs_courriel='".gestion_apostrophe($_POST['fournisseurs_courriel'])."', fournisseurs_telephone='".gestion_apostrophe($_POST['fournisseurs_telephone'])."', fournisseurs_infos='".gestion_apostrophe($_POST['fournisseurs_infos'])."' where fournisseurs_ID=".$_POST['fournisseurs_ID'].";");
	header("Location: $BASE_URL/gestion_fournisseurs.php");
	}
elseif (isset($_POST['annuler']))
	header("Location: $BASE_URL/gestion_fournisseurs.php");
	
if (isset($_POST['Tout_activer']))
{
	mysqli_query($link, "update produits set produits_actif=1 where produits_fournisseur=".$_POST['fournisseurs_ID'].";");
	echo "<font color=green> Tous les produits de ce fournisseur ont été activés </font>\n<p><p>\n\n";
}

if (isset($_POST['Tout_desactiver']))
{
	mysqli_query($link, "update produits set produits_actif=0 where produits_fournisseur=".$_POST['fournisseurs_ID'].";");
	echo "<font color=green> Tous les produits de ce fournisseur ont été désactivés </font>\n<p><p>\n\n";
}

?>

<html> 
<head> 
<title>Modification d'un fournisseur
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<H2>Modification d'un fournisseur
</H2>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Fournisseur</th>
			<th colspan="1">Adresse</th>
			<th colspan="1">Commune</th>
			<th colspan="1">Courriel</th>
			<th colspan="1">Téléphone</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<?php
			
			// Remplissage du tableau
			$requete = mysqli_query($link, "select * from fournisseurs where fournisseurs_ID=".$_POST['fournisseurs_ID'].";");
			$resultat = mysqli_fetch_array($requete);
			
			echo "<tr valign='middle'>\n";
			// Nom
			echo '<td colspan="1" rowspan="1" align="left">';
			echo '<input type="text" maxlength=100 name="fournisseurs_nom" value="'.$resultat["fournisseurs_nom"].'">';
			echo "</td>\n";
			// adresse
			echo '<td colspan="1" rowspan="1" align="left">';
			echo '<input type="text" maxlength=100 name="fournisseurs_adresse" value="'.$resultat["fournisseurs_adresse"].'">';
			echo "</td>\n";
			//  Commune
			echo '<td colspan="1" rowspan="1" align="left">';
			echo '<input type="text" maxlength=100 name="fournisseurs_commune" value="'.$resultat["fournisseurs_commune"].'">';
			echo "</td>\n";
			//  Courriel
			echo '<td colspan="1" rowspan="1" align="left">';
			echo '<input type="text" maxlength=100 name="fournisseurs_courriel" value="'.$resultat["fournisseurs_courriel"].'">';
			echo "</td>\n";
			//  Telephone
			echo '<td colspan="1" rowspan="1" align="left">';
			echo '<input type="text" maxlength=30 size=15 name="fournisseurs_telephone" value="'.$resultat["fournisseurs_telephone"].'">';
			echo "</td>\n";
			?>
		</tbody>
	</table>
<h4>Informations diverses </h4>
<textarea name='fournisseurs_infos' maxlength='500' rows='10' cols='100'><?php echo $resultat["fournisseurs_infos"]; ?></textarea>
<p>

<input type='hidden' name='fournisseurs_ID' value='<?php echo $_POST['fournisseurs_ID'];?>'>
<input class='bouton' type='submit' name='valider' value='Valider'>
<input class='bouton' type='submit' name='annuler' value='Annuler'>

<h4>Activer/desactiver tous les produits </h4>
<input class='bouton' type='submit' name='Tout_activer' value='Tout activer'>
<input class='bouton' type='submit' name='Tout_desactiver' value='Tout désactiver'>


</form>

			
</div>
</body>
</html>
