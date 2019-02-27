<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];                          

$seuil_produit=40;
$seuil_udv=35;
$seuil_conditionnement=40;

//require('fpdf_polo.php');
require('fpdf_mc_table.php');		// Pour le problème des saut de pages avec multi-cell
$pdf=new PDF_MC_Table();
$pdf->SetAutoPageBreak(True);

$pdf->AddPage();
$pdf->SetFont('Arial','B',8);
$pdf->SetWidths(array(40,35,40,25,20,25));		// Paramétrage des largeurs colonnes
$pdf->Row(array('Nom du produit','Fournisseur','Conditionnement','Unité de vente',"Prix de l'UDV",'Pour comparer'));

$pdf->SetFont('Arial','',8);

$requete = mysqli_query($link, "select * from (produits left join fournisseurs on produits.produits_fournisseur=fournisseurs.fournisseurs_ID left join rubriques on produits.produits_rubrique=rubriques.rubriques_ID)  where (produits_commission=$commission and produits_actif=1) order by rubriques_nom,fournisseurs_nom,produits_nom;");

while(($resultat = mysqli_fetch_array($requete)))
	{
	$pdf->Row(array($resultat['produits_nom'],$resultat['fournisseurs_nom'],$resultat['produits_conditionnement'],$resultat['produits_udv'],number_format($resultat['produits_prix_udv'],2,',',' '),"soit ".str_replace("€","",$resultat['produits_prix_comparable'])));
	}

$pdf->Output();
?>
