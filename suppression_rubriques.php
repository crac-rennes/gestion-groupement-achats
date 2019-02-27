<?php
session_start();
require("fonctions.php");
verification_identification();
verification_simple_membre();

if (isset($_POST['oui']))
{
	mysqli_query($link, "delete from rubriques where rubriques_ID=".$_POST['rubriques_ID'].";");
	header("Location: $BASE_URL/gestion_rubriques.php");
}
elseif (isset($_POST['non']))
	header("Location: $BASE_URL/gestion_rubriques.php");

?>

<html> 
<head> 
<title>Suppression d'une rubrique
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<font color='red'> Vous allez supprimer la rubrique : </font><p>

<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Nom de la rubrique</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<?php
		$rubriques_ID=$_POST['rubriques_ID'];
		
		// Remplissage du tableau
		$requete = mysqli_query($link, "select * from rubriques where rubriques_ID=$rubriques_ID;");
		$resultat = mysqli_fetch_array($requete);
		echo '<tr valign="middle">';
		// Nom
		echo '<td colspan="1" rowspan="1" align="left">';
		echo $resultat["rubriques_nom"];
		echo "</td>\n";
		?>
	</tbody>
</table>

<font color='red'> Souhaitez vous poursuivre ? </font><p>

<form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>' method="post">
	<fieldset> 	
		<input type="hidden" name="rubriques_ID" value="<?php echo $rubriques_ID; ?>">
		<input class="bouton" type="submit" name="oui" value="Oui" />        
		<input class="bouton" type="submit" name="non" value="Non" />        
	</fieldset>    
</form>  


</div>
</body>
</html>
