<?php
session_start();
require("fonctions.php");
verification_identification();
verification_admin();
header("Content-type: application/force-download");
header("Content-Disposition: attachment; filename=sauvegarde_base_globale_".date("Y-m-d").".sql");

$requete=mysqli_query($link, 'show tables;');

while($table=mysqli_fetch_array($requete))
{
$nom_table=$table["Tables_in_".str_replace('.','_',$SQL_BASE)];

// Suppression de la table existante
echo "-- Suppression de la table : ".$nom_table." --\r\n";
echo "DROP table if exists $nom_table;\r\n\r\n";

// Creation de la table
echo "-- Structure de la table : ".$nom_table." --\r\n";
$requete_structure=mysqli_query($link, "show create table $nom_table;");

$resultat_structure=mysqli_fetch_array($requete_structure);
echo $resultat_structure['Create Table'].";\r\n\r\n";
echo "-- Contenu de la table : ".$nom_table." --\r\n";
// récupération des noms des champs
$requete_champs=mysqli_query($link, "describe $nom_table;");
$nb_champs=0;
if ($champ=mysqli_fetch_array($requete_champs))
	{
	echo "INSERT INTO `$nom_table` (`".$champ["Field"]."`";
	$nom_champ[$nb_champs]=$champ["Field"];
	if ((strpos($champ["Type"],"char")==TRUE) || (strpos($champ["Type"],"time")==TRUE))
		$champ_texte[$nb_champs]="'";
	else
		$champ_texte[$nb_champs]="";
	$nb_champs++;
	}
while ($champ=mysqli_fetch_array($requete_champs))
	{
	echo ",`".$champ["Field"]."`";
	$nom_champ[$nb_champs]=$champ["Field"];
	if ((strpos($champ["Type"],"char")==TRUE) || (strpos($champ["Type"],"time")==TRUE))
		$champ_texte[$nb_champs]="'";
	else
		$champ_texte[$nb_champs]="";
	$nb_champs++;
	}
echo ") VALUES ";
// création des "VALUES"
$requete_contenu=mysqli_query($link, "select * from $nom_table;");
if ($entree=mysqli_fetch_array($requete_contenu))
	{
	echo "(".$champ_texte[0].str_replace("'","''",$entree[$nom_champ[0]]).$champ_texte[0];
	for ($ii=1;$ii<$nb_champs;$ii++)
		{
		echo ",".$champ_texte[$ii].str_replace("'","''",$entree[$nom_champ[$ii]]).$champ_texte[$ii];
		}
	echo ")";
	}
while ($entree=mysqli_fetch_array($requete_contenu))
	{
	echo ",(".$champ_texte[0].str_replace("'","''",$entree[$nom_champ[0]]).$champ_texte[0];
	for ($ii=1;$ii<$nb_champs;$ii++)
		{
		echo ",".$champ_texte[$ii].str_replace("'","''",$entree[$nom_champ[$ii]]).$champ_texte[$ii];
		}
	echo ")";
	}
echo ";\r\n\r\n";

}

?>
