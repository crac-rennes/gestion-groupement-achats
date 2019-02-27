<?php
session_start();
require("fonctions.php");
verification_identification();
verification_simple_membre();
	
if (isset($_POST['fournisseurs_nom']))
{
// mysqli_query($link, "insert into fournisseurs (fournisseurs_nom,fournisseurs_adresse,fournisseurs_commune,fournisseurs_courriel,fournisseurs_telephone,fournisseurs_infos) values ('".gestion_apostrophe($_POST['fournisseurs_nom'])."','".gestion_apostrophe($_POST['fournisseurs_adresse'])."','".gestion_apostrophe($_POST['fournisseurs_commune'])."','".gestion_apostrophe($_POST['fournisseurs_courriel'])."','".gestion_apostrophe($_POST['fournisseurs_telephone'])."','".gestion_apostrophe($_POST['fournisseurs_infos'])."',0,0,0,0);");

mysqli_query($link, "insert into fournisseurs (fournisseurs_nom,fournisseurs_adresse,fournisseurs_commune,fournisseurs_courriel,fournisseurs_telephone,fournisseurs_infos) values ('".gestion_apostrophe($_POST['fournisseurs_nom'])."','".gestion_apostrophe($_POST['fournisseurs_adresse'])."','".gestion_apostrophe($_POST['fournisseurs_commune'])."','".gestion_apostrophe($_POST['fournisseurs_courriel'])."','".gestion_apostrophe($_POST['fournisseurs_telephone'])."','".gestion_apostrophe($_POST['fournisseurs_infos'])."');");



header("Location: $BASE_URL/gestion_fournisseurs.php");
}
?>

<html> 
<head> 
<title>Ajout d'un fournisseur
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  

<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<H2>Ajout d'un fournisseur</H2>

<form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>' method='post'>
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
		<tr valign="middle">
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='fournisseurs_nom' maxlength='100'>
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='fournisseurs_adresse' maxlength='200'>
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='fournisseurs_commune' maxlength='100'>
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='fournisseurs_courriel' maxlength='100' size='30'>
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='fournisseurs_telephone' maxlength='100' size='15'> </textarea>
			</td>
		</tr>
	</tbody>
</table>
<h4>Informations diverses </h4>
<textarea name='fournisseurs_infos' maxlength='500' rows='10' cols='100'> </textarea>
<p>
<input class='bouton' type='submit' value='Valider'>
</form>

</div>
</body>
</html>	
