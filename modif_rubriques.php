<?php
session_start();
require("fonctions.php");
verification_identification();
verification_simple_membre();

if (isset($_POST['valider']))
{
	//echo "update rubriques set rubriques_nom='".$_POST['rubriques_nom']."' where rubriques_ID=".$_POST['rubriques_ID'].";";
	mysqli_query($link, "update rubriques set rubriques_nom='".gestion_apostrophe($_POST['rubriques_nom'])."' where rubriques_ID=".$_POST['rubriques_ID'].";");
	header("Location: $BASE_URL/gestion_rubriques.php");
}
elseif (isset($_POST['annuler']))
	header("Location: $BASE_URL/gestion_rubriques.php");

?>

<html> 
<head> 
<title>Modification d'une rubrique
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<H2>Modification d'une rubrique
</H2>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Nom de la rubrique</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<tr valign="middle">
		<td colspan="1" rowspan="1" align="left">
		<input type='text' maxlength=100 name='rubriques_nom' value="<?php
		$requete = mysqli_query($link, "select * from rubriques where rubriques_ID=".$_POST['rubriques_ID'].";");
		$resultat = mysqli_fetch_array($requete);
		echo "".$resultat["rubriques_nom"];?>">
		</td>
		</tbody>
	</table>
<input type='hidden' name='rubriques_ID' value='<?php echo $_POST['rubriques_ID'];?>'>
<input class='bouton' type='submit' name='valider' value='Valider'>
<input class='bouton' type='submit' name='annuler' value='Annuler'>
</form>
			
</div>
</body>
</html>
