<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);	

if ($_POST['bouton']=='en vrac')
{
	$requete_liste_produits=mysqli_query($link, "select produits_ID from commande,produits where commande_produit=produits_ID and produits_vrac>0 and commande_commission=$commission and produits_actif=1 group by produits_ID order by produits_nom;");
	echo mysqli_num_rows($requete_liste_produits);
}
else
{
	$requete_liste_produits=mysqli_query($link, "select produits_ID from commande,produits where commande_produit=produits_ID and commande_commission=$commission and produits_actif=1 group by produits_ID order by produits_nom;");
}
if (mysqli_num_rows($requete_liste_produits)!=0)
{
	
	require('fpdf_polo.php');
	$pdf=new FPDF();

	while(($resultat_liste_produits = mysqli_fetch_array($requete_liste_produits)))
	{

		$produits_ID=$resultat_liste_produits['produits_ID'];

		$requete=mysqli_query($link, "select produits_udv,produits_nom,produits_conditionnement,produits_prix_udv,produits_vrac,fournisseurs_nom from produits,fournisseurs where produits_fournisseur=fournisseurs_ID and produits_ID=$produits_ID;");
		$resultat=mysqli_fetch_array($requete);
		$produits_nom=$resultat['produits_nom'];
		$produits_conditionnement=$resultat['produits_conditionnement'];
		$produits_prix_udv=$resultat['produits_prix_udv'];
		$produits_udv=$resultat['produits_udv'];
		$produits_vrac=abs($resultat['produits_vrac']);
		$produits_fournisseur=$resultat['fournisseurs_nom'];

		$pdf->AddPage();
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell_utf8_vers_iso_8859_15(150,20,'Répartition de la commande de '.$produits_nom.' de chez '.$produits_fournisseur.'  en '.$produits_conditionnement);
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
		}
	$pdf->Output();
	}
?>
