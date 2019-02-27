<?php
session_start();
require("fonctions.php");
verification_identification();
verification_simple_membre();

?>

<html> 
<head> 
<title>Editition de la liste des rubriques
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<H2>Editition de la liste des rubriques
</H2>

<?php
// Bouton pour l'ajout d'une rubrique
print("<form action='ajout_rubriques.php' method='post'>\n");
print("<input class='bouton' type='submit' value='Ajouter une rubrique' />\n");
print("</form>  \n");
?>

<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Nom de la rubrique</th>
			<th width='100' colspan="1">Modifier</th>
			<th colspan="1">Supprimer</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<?php			
			// Remplissage du tableau
			$requete = mysqli_query($link, "select * from rubriques order by rubriques_nom;");
			while(($resultat = mysqli_fetch_array($requete)))
			{
				echo '<tr valign="middle">';
				// Nom
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat["rubriques_nom"];
				echo '</td>';
				// Modifier
				echo '<td colspan="1" rowspan="1" align="left">';
				echo "<form action='modif_rubriques.php' method='post'>\n";
				$rubriques_ID=$resultat['rubriques_ID'];
				echo "<input type=hidden name='rubriques_ID' value=$rubriques_ID >\n";
				//echo "<input class='bouton' type='submit' name='submit' value='Modifier' />\n";
				echo "<input type='image' name='submit' value='Modifier' src='9070.ico'>";
				echo "</form>  \n";
				echo '</td>';
				//  Supprimer
				echo '<td colspan="1" rowspan="1" align="left">';
				echo "<form action='suppression_rubriques.php' method='post'>\n";
				echo "<input type=hidden name='rubriques_ID' value=$rubriques_ID >\n";
				//echo "<input class='bouton' type='submit' name='submit' value='Supprimer' />\n";
				echo "<input type='image' name='suBMIT' value='Supprimer' src='picto_poubelle_big.gif'>";
				echo "</form>  \n";
				echo '</td>';
				echo "</tr>";
			}?>
		</tbody>
	</table>
			
</div>
</body>
</html>
