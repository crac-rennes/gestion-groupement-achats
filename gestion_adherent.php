<?php
session_start();
require("fonctions.php");
verification_identification();
verification_admin();
?>

<html>

<head> 
<title>Gestion des membres
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  

<body>
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">


<form action='ajout_adherent.php' method='post'>
<input class='bouton' type='submit' value='Ajouter un membre' />
</form>


<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Nom</th>
			<th colspan="1">Statut</th>
			<th colspan="1">Email principal</th>
			<th colspan="1">Emails secondaires</th>
			<th colspan="1">Adresse</th>
			<th colspan="1">Téléphone</th>
			<th colspan="1">Modifier</th>
			<th colspan="1">Supprimer</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<?php
			// Remplissage du tableau
			$requete = mysqli_query($link, "select * from membres order by nom_complet;");
			while(($resultat = mysqli_fetch_array($requete)))
			{
				echo '<tr valign="middle">';
				// Nom
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat["nom_complet"];
				echo '</td>';
				// Statut
				echo '<td colspan="1" rowspan="1" align="left">';
				if ($resultat["statut"]==0)
					echo "Membre";
				else if ( is_admin($resultat["statut"]) )
					echo "Administrateur";
     				else
    				{
    					foreach ($nom_commission as $id => $nom_com)
						{
     						//echo $resultat['statut'];
     						if ( is_resp_comm($id, $resultat['statut']) )
							{
     							echo "Resp. $nom_com<p>";
     							}
           					}
           				
           				if (is_associe($resultat['statut']))
							echo "Membre associé";
     				}
     				echo '</td>';
				//  Email
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat["email"];
				echo '</td>';
				//  Email secondaire
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat["mail_second"];
				echo '</td>';
				//  Adresse
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat["adresse"];
				echo '</td>';
				//  Telephone
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $resultat["telephone"];
				echo '</td>';
				//  Modifier
				echo '<td colspan="1" rowspan="1" align="left">';
				print("<form action='modif_adherent.php' method='post'>\n");
				$ID=$resultat['ID'];
				print("<input type=hidden name='ID' value=$ID >\n");
				echo "<input type='image' name='modifier' value='Valider' src='9070.ico'>";
				print("</form>  \n");
				echo '</td>';
				//  Supprimer
				echo '<td colspan="1" rowspan="1" align="left">';
				print("<form action='suppression_adherent.php' method='post'>\n");
				print("<input type=hidden name='ID' value=$ID >\n");
				echo "<input type='image' name='supprimer' value='Valider' src='picto_poubelle_big.gif'>";
				print("</form>  \n");
				echo '</td>';
				echo "</tr>";
			}?>
	</tbody>
</table>
</div>
</body>
</html>
