<?php
session_start();
require("fonctions.php");
verification_identification();
?>

<html> 
<head> 
<title>Documents
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  

<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<?php

# Affichage des compte-rendus
if (is_dir("./CR"))
{
	?>
	<H2>Historique des comptes-rendus des réunions du groupement</H2>

	<table style="border:0px">
		<tr>
	<?php
	$ligne=0;
	$dh  = opendir("./CR");
	$liste_fichiers=array();
	$nb_fichiers=0;
	while ( ($fichier = readdir($dh)) ) 
	{
		//echo $fichier." ".strpos($fichier,".")."<p>";
		if (strpos($fichier,".")!=0)
		{
			$liste_fichiers[$nb_fichiers]=$fichier;
			$nb_fichiers++;
		}
	}
	sort($liste_fichiers);
	for ($i=$nb_fichiers-1;$i>=0;$i--)
	{
		echo "<td style='border:0px;padding: 15px;'><a href='CR/$liste_fichiers[$i]'>$liste_fichiers[$i]</a></td>";
		$ligne++;
		if ($ligne==4)
		{
			echo "</tr>\n<tr>";
			$ligne=0;
		}	
	}
	?>
	</tr></table>
<?php
} # Fin affichage des compte-rendus

# Affichage des autres infos
if (is_dir("./fichiers"))
{
	?>

	<H2>Documents en vrac</H2>
	<p>
	<a href='fichiers/Charte.pdf'>Charte</a><p>

	<a href='fichiers/Tuto_infos_personnelles.pdf'>Modification des informations personnelles et contact des autres membres</a><p>

	<a href='fichiers/Retro-planning_commission_sec.odt'>Retro-planning de la commission sec</a><p>
		
	<a href='fichiers/fiche_livraison.odt'>Fiche livraison pour la commission sec</a><p>

	<H2>Recettes</H2>
	<br>
	<a href="./fichiers/TableauCuissonCereale.jpg">Tableau de cuisson des céréales </a>
<?php
} # Fin affichage des autres fichiers
?>

</div>
</body>
</html>
