<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);
	
$produits_ID=$_POST['produits_ID'];
$requete=mysqli_query($link, "select produits_udv,produits_nom,produits_conditionnement,produits_prix_udv,produits_vrac from produits where produits_ID=$produits_ID;");
$resultat=mysqli_fetch_array($requete);
$produits_nom=$resultat['produits_nom'];
$produits_conditionnement=$resultat['produits_conditionnement'];
$produits_prix_udv=$resultat['produits_prix_udv'];
$produits_udv=$resultat['produits_udv'];
$produits_vrac=abs($resultat['produits_vrac']);





require('fpdf_polo.php');
$pdf=new FPDF();
$pdf->SetAutoPageBreak(True);

$pdf->AddPage();
$pdf->SetFont('Arial','B',11);
$pdf->Cell_utf8_vers_iso_8859_15(150,20,'Répartition de la commande de '.$produits_nom.'  en '.$produits_conditionnement);
$pdf->Ln(10);
$pdf->SetFont('Arial','',10);
$pdf->Cell_utf8_vers_iso_8859_15(150,20,'Unité : '.$produits_udv);
$pdf->Ln(20);

$pdf->Cell_utf8_vers_iso_8859_15(40,6,'');
$pdf->Cell_utf8_vers_iso_8859_15(80,6,'Membre',1);
$pdf->Cell_utf8_vers_iso_8859_15(20,6,'Quantité',1);
$pdf->Ln();

$requete=mysqli_query($link, "select commande_quantite,nom_complet from commande,membres where (commande_membre=ID and commande_produit=$produits_ID) order by nom_complet;");

$pdf->SetFont('Arial','',8);
$total=0;

while(($resultat = mysqli_fetch_array($requete)))
	{
	$pdf->Cell_utf8_vers_iso_8859_15(40,6,'');
	$pdf->Cell_utf8_vers_iso_8859_15(80,6,$resultat['nom_complet'],1);
	$pdf->Cell_utf8_vers_iso_8859_15(20,6,$resultat['commande_quantite'],1,0,'R');
	$pdf->Ln();
	$total +=$resultat['commande_quantite'];
	}
$pdf->Cell_utf8_vers_iso_8859_15(40,6,'');
$pdf->Cell_utf8_vers_iso_8859_15(80,6,"Total",1,0,'C');
$pdf->Cell_utf8_vers_iso_8859_15(20,6,$total,1,0,'R');
$pdf->Output();
?>
