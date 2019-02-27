<?php
session_start();
require("fonctions.php");
verification_identification();

$commission=$_GET['commission'];

require('fpdf.php');
$pdf=new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,'Nom du produit',1);
$pdf->Cell(35,6,'Fournisseur',1);
$pdf->Cell(40,6,'Conditionnement',1);
$pdf->Cell(30,6,'Unité de vente',1);
$pdf->Cell(20,6,"Prix de l'UDV",1);
$pdf->Cell(25,6,'Pour comparer',1);
$pdf->Ln();
$pdf->SetFont('Arial','',8);

$requete = mysqli_query($link, "select * from (produits left join fournisseurs on produits.produits_fournisseur=fournisseurs.fournisseurs_ID left join rubriques on produits.produits_rubrique=rubriques.rubriques_ID)  where (produits_commission=$commission and produits_actif=1) order by rubriques_nom,fournisseurs_nom,produits_nom;");

while(($resultat = mysqli_fetch_array($requete)))
	{
	$pdf->Cell(40,6,$resultat['produits_nom'],1);
	$pdf->Cell(35,6,$resultat['fournisseurs_nom'],1);
	$pdf->Cell(40,6,$resultat['produits_conditionnement'],1);
	$pdf->Cell(30,6,$resultat['produits_udv'],1);
	$pdf->Cell(20,6,$resultat['produits_prix_udv'],1);
	$pdf->Cell(25,6,"soit ".$resultat['produits_prix_comparable'],1);
	$pdf->Ln();
	}

$pdf->Output();
?>
