<?php
session_start();
require("fonctions.php");
verification_identification();
verification_simple_membre();

?>

<html> 
<head> 
<title>Editition de la liste des fournisseurs
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<H2>Editition de la liste des fournisseurs
</H2>

<?php
// Bouton pour l'ajout d'un fournisseur
echo "<form action='ajout_fournisseur.php' method='post'>\n";
echo "<input class='bouton' type='submit' value='Ajouter un fournisseur' />\n";
echo "</form>  \n";
?>

<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Fournisseur</th>
			<th colspan="1">Adresse</th>
			<th colspan="1">Commune</th>
			<th colspan="1">Courriel</th>
			<th colspan="1">Téléphone</th>
			<th colspan="1">Infos diverses</th>
			<th colspan="1">Modifier</th>
			<th colspan="1">Supprimer</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<?php
			// Remplissage du tableau
			$requete = mysqli_query($link, "select * from fournisseurs order by fournisseurs_nom;");
			while(($resultat = mysqli_fetch_array($requete)))
			{
				echo '<tr valign="middle">';
				// Nom
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat["fournisseurs_nom"];
				echo '</td>';
				// adresse
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat["fournisseurs_adresse"];
				echo '</td>';
				//  Commune
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat["fournisseurs_commune"];
				echo '</td>';
				//  Courriel
				echo '<td colspan="1" rowspan="1" align="left">';
				if ($resultat["fournisseurs_courriel"]==NULL)
					echo "Non renseigné";
				else
					echo $resultat["fournisseurs_courriel"];
				echo '</td>';
				//  Telephone
				echo '<td colspan="1" rowspan="1" align="left">';
				if ($resultat["fournisseurs_telephone"]==NULL)
					echo "Non renseigné";
				else
					echo $resultat["fournisseurs_telephone"];
				echo '</td>';
				//  Infos diverses
				echo '<td colspan="1" rowspan="1" align="left">';
				if ($resultat["fournisseurs_telephone"]!==NULL)
					echo $resultat["fournisseurs_infos"];
				echo '</td>';
				// Modifier
				echo '<td colspan="1" rowspan="1" align="left">';
				echo "<form action='modif_fournisseurs.php' method='post'>\n";
				$fournisseurs_ID=$resultat['fournisseurs_ID'];
				echo "<input type=hidden name='fournisseurs_ID' value=$fournisseurs_ID >\n";
				echo "<input type='image' name='submit' value='Modifier' src='9070.ico'/>\n";
				echo "</form>  \n";
				echo '</td>';
				//  Supprimer
				echo '<td colspan="1" rowspan="1" align="left">';
				echo "<form action='suppression_fournisseur.php' method='post'>\n";
				echo "<input type=hidden name='fournisseurs_ID' value=$fournisseurs_ID >\n";
				echo "<input type='image' name='submit' value='Supprimer' src='picto_poubelle_big.gif'/>\n";
				echo "</form>  \n";
				echo '</td>';
				echo "</tr>";
			}?>
		</tbody>
	</table>
			
</div>
</body>
</html>
