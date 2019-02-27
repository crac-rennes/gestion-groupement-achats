<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);
header("Content-type: application/force-download");
header("Content-Disposition: attachment; filename=sauvegarde_base_".str_replace("'","_",str_replace(",","",str_replace(" ","_",$nom_commission[$commission])))."_".date("Y-m-d").".sql");

echo "-- Suppression des produits mémorisés pour cette commission --\r\n";
echo "delete from produits where produits_commission=$commission;\r\n";

echo "-- Restauration des produits sauvegardés --\r\n";

$requete_champs=mysqli_query($link, "describe produits;");
$nb_champs=0;
if ($champ=mysqli_fetch_array($requete_champs))
	{
	echo "INSERT INTO `produits` (`".$champ["Field"]."`";
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
$requete_contenu=mysqli_query($link, "select * from produits where produits_commission=$commission;");
if ($entree=mysqli_fetch_array($requete_contenu))
	{
	//echo "(".$champ_texte[0].str_replace("'","''",$entree[$nom_champ[0]]).$champ_texte[0];
	echo "(".$champ_texte[0].mysqli_real_escape_string($entree[$nom_champ[0]]).$champ_texte[0];
	for ($ii=1;$ii<$nb_champs;$ii++)
		{
		//echo ",".$champ_texte[$ii].str_replace("'","''",$entree[$nom_champ[$ii]]).$champ_texte[$ii];
		echo ",".$champ_texte[$ii].mysqli_real_escape_string($entree[$nom_champ[$ii]]).$champ_texte[$ii];
		}
	echo ")";
	}
while ($entree=mysqli_fetch_array($requete_contenu))
	{
	//echo ",(".$champ_texte[0].str_replace("'","''",$entree[$nom_champ[0]]).$champ_texte[0];
	echo ",(".$champ_texte[0].mysqli_real_escape_string($entree[$nom_champ[0]]).$champ_texte[0];
	for ($ii=1;$ii<$nb_champs;$ii++)
		{
		//echo ",".$champ_texte[$ii].str_replace("'","''",$entree[$nom_champ[$ii]]).$champ_texte[$ii];
		echo ",".$champ_texte[$ii].mysqli_real_escape_string($entree[$nom_champ[$ii]]).$champ_texte[$ii];
		}
	echo ")";
	}
echo ";\r\n\r\n";


echo "-- Suppression des commandes mémorisées pour cette commission --\r\n";
echo "delete from commande where commande_commission=$commission;\r\n";

echo "-- Restauration des commandes sauvegardées --\r\n";

$requete_champs=mysqli_query($link, "describe commande;");
$nb_champs=0;
if ($champ=mysqli_fetch_array($requete_champs))
	{
	echo "INSERT INTO `commande` (`".$champ["Field"]."`";
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
$requete_contenu=mysqli_query($link, "select * from commande where commande_commission=$commission;");
if ($entree=mysqli_fetch_array($requete_contenu))
	{
	//echo "(".$champ_texte[0].str_replace("'","''",$entree[$nom_champ[0]]).$champ_texte[0];
	echo "(".$champ_texte[0].mysqli_real_escape_string($entree[$nom_champ[0]]).$champ_texte[0];
	for ($ii=1;$ii<$nb_champs;$ii++)
		{
		//echo ",".$champ_texte[$ii].str_replace("'","''",$entree[$nom_champ[$ii]]).$champ_texte[$ii];
		echo ",".$champ_texte[$ii].mysqli_real_escape_string($entree[$nom_champ[$ii]]).$champ_texte[$ii];
		}
	echo ")";
	}
while ($entree=mysqli_fetch_array($requete_contenu))
	{
	//echo ",(".$champ_texte[0].str_replace("'","''",$entree[$nom_champ[0]]).$champ_texte[0];
	echo ",(".$champ_texte[0].mysqli_real_escape_string($entree[$nom_champ[0]]).$champ_texte[0];
	for ($ii=1;$ii<$nb_champs;$ii++)
		{
		//echo ",".$champ_texte[$ii].str_replace("'","''",$entree[$nom_champ[$ii]]).$champ_texte[$ii];
		echo ",".$champ_texte[$ii].mysqli_real_escape_string($entree[$nom_champ[$ii]]).$champ_texte[$ii];
		}
	echo ")";
	}
echo ";\r\n\r\n";
?>

