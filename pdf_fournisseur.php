<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);

$fournisseurs_ID=$_POST['fournisseurs_ID'];
	
if (isset($_POST['entrer_coordonnees']))
	{
	echo "<html><head><link rel='stylesheet' href='style.css'></head>";
	echo "<body>";

	$nom=$_SESSION['nom_complet'];
	echo "<form action='".htmlspecialchars($_SERVER['PHP_SELF'])."?commission=$commission' method='post'>";
	echo "<input type='hidden' name='fournisseurs_ID' value=$fournisseurs_ID>";
	echo "Nom : <input type='text' name='nom_membre' value='$nom' size='30'><p>\n";
	echo "Adresse : <input type='text' name='adresse_membre' value='' size='50'><p>\n";
	echo "<input class='bouton' type='submit' name='coordonnees_entrees' value='OK'>";
	
	echo "<div align='center'><a href='gestion_commande.php?commission=$commission'>Retour a la gestion de commande</a></div>";
	echo "</body></html>";

	}
else
	{
	if (isset($_POST['coordonnees_entrees']))
		{
		$nom=$_POST['nom_membre'];
		$adresse=$_POST['adresse_membre'];
		}
	else
		{
		$nom='.........................................';
		$adresse='..................................................';
		}
	
	$requete=mysqli_query($link, "select fournisseurs_nom,fournisseurs_adresse,fournisseurs_commune from fournisseurs where fournisseurs_ID=$fournisseurs_ID;");
	$resultat=mysqli_fetch_array($requete);
	$fournisseurs_nom=$resultat['fournisseurs_nom'];
	$fournisseurs_adresse=$resultat['fournisseurs_adresse'];
	$fournisseurs_commune=$resultat['fournisseurs_commune'];

		
	$requete=mysqli_query($link, "select commande_produit,sum(commande_quantite),produits_nom,produits_prix_udv,produits_conditionnement,produits_vrac, produits_TVA from commande,produits,fournisseurs where (commande_produit=produits_ID and produits_fournisseur=fournisseurs_ID and produits_fournisseur=fournisseurs_ID and fournisseurs_ID=$fournisseurs_ID and produits_commission=$commission and produits_actif=1) group by commande_produit order by produits_nom;");
	
	
	
	require('fpdf_polo.php');
	$pdf=new FPDF();
	$pdf->SetAutoPageBreak(True);

	$pdf->SetTitle("Bon_de_commande_".str_replace(" ","_",$fournisseurs_nom));
	//$pdf->SetTitle("Bon_de_commande_".str_replace(" ","_",$fournisseurs_nom));
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',8);
	
	// Entete
	$pdf->SetXY(10,20);
	$pdf->Cell_utf8_vers_iso_8859_15(85,30,'',1);

/*	$pdf->SetXY(10,20);
	$pdf->Cell_utf8_vers_iso_8859_15(85,10,"Groupement d'achats de Corps-Nuds");
	$pdf->Ln();
	$pdf->SetXY(10,30);
	$pdf->Cell_utf8_vers_iso_8859_15(85,10,"Représenté par : ".$nom);
	$pdf->Ln();
	$pdf->SetXY(10,40);
	$pdf->Cell_utf8_vers_iso_8859_15(00,10,"Adresse : ".$adresse);
	$pdf->Ln();
*/
	$pdf->SetXY(10,20);
	$pdf->Cell_utf8_vers_iso_8859_15(85,10,$nom);
	$pdf->Ln();
	$pdf->SetXY(10,35);
	$pdf->Cell_utf8_vers_iso_8859_15(00,10,$adresse);
	$pdf->Ln();

	$pdf->SetXY(104,20);
	$pdf->Cell_utf8_vers_iso_8859_15(6,30,'à');
	$pdf->SetXY(110,20);
	$pdf->Cell_utf8_vers_iso_8859_15(50,30,'',1);
	$pdf->SetXY(110,20);
	$pdf->Cell_utf8_vers_iso_8859_15(50,10,$fournisseurs_nom);
	$pdf->Ln();
	$pdf->SetXY(110,30);
	$pdf->Cell_utf8_vers_iso_8859_15(50,10,$fournisseurs_adresse);
	$pdf->Ln();
	$pdf->SetXY(110,40);
	$pdf->Cell_utf8_vers_iso_8859_15(50,10,$fournisseurs_commune);
	
	$pdf->SetXY(40,57);
	$date="Corps Nuds, le ".date("d/m/y");
	$pdf->Cell_utf8_vers_iso_8859_15(50,10,$date);
	
	$pdf->SetY(70);
	$pdf->Ln();
	$pdf->SetFont('Arial','B',8);
	$pdf->Cell_utf8_vers_iso_8859_15(40,6,'Nom du produit',1);
	$pdf->Cell_utf8_vers_iso_8859_15(40,6,'Conditionnement',1);
	$pdf->Cell_utf8_vers_iso_8859_15(30,6,'Prix unitaire( HT)',1);
	$pdf->Cell_utf8_vers_iso_8859_15(15,6,'Quantité',1);
	$pdf->Cell_utf8_vers_iso_8859_15(25,6,'Prix ( HT)',1);
	$pdf->Cell_utf8_vers_iso_8859_15(25,6,'Prix ( TTC)',1);
	$pdf->Ln();
	$pdf->SetFont('Arial','',8);
	
	$total_TTC=0;
	$total_HT=0;
	
	while(($resultat = mysqli_fetch_array($requete)))
		{
		$long_produit = $pdf->GetStringWidth($resultat['produits_nom']);
		if ($long_produit<40)
		{
			$pdf->Cell_utf8_vers_iso_8859_15(40,6,$resultat['produits_nom'],1);
			$pdf->Cell_utf8_vers_iso_8859_15(40,6,$resultat['produits_conditionnement'],1);
			if ($resultat['produits_vrac']!=0)
				{
				// Produit en vrac
				// prix d'un conditionnement
				$pdf->Cell_utf8_vers_iso_8859_15(30,6,number_format(round($resultat['produits_prix_udv']*abs($resultat['produits_vrac']/(1+$resultat['produits_TVA']/100)),2),2,',',' '),1,0,'R');
				// on calcule le nombre de conditionnement à partir de la quantité commandée et du nombre d'UDV par conditionnement
				$pdf->Cell_utf8_vers_iso_8859_15(15,6,round($resultat['sum(commande_quantite)']/abs($resultat['produits_vrac']),2),1,0,'R');
				// prix total
				$pdf->Cell_utf8_vers_iso_8859_15(25,6,number_format(round($resultat['produits_prix_udv']*$resultat['sum(commande_quantite)']/(1+$resultat['produits_TVA']/100),2),2,',',' '),1,0,'R');
				$pdf->Cell_utf8_vers_iso_8859_15(25,6,number_format(round($resultat['produits_prix_udv']*$resultat['sum(commande_quantite)'],2),2,',',' '),1,0,'R');
				}
			else
				{
				$pdf->Cell_utf8_vers_iso_8859_15(30,6,number_format(round($resultat['produits_prix_udv']/(1+$resultat['produits_TVA']/100),2),2,',',' '),1,0,'R');
				$pdf->Cell_utf8_vers_iso_8859_15(15,6,round($resultat['sum(commande_quantite)'],2),1,0,'R');
				$pdf->Cell_utf8_vers_iso_8859_15(25,6,number_format(round($resultat['produits_prix_udv']*$resultat['sum(commande_quantite)']/(1+$resultat['produits_TVA']/100),2),2,',',' '),1,0,'R');
				$pdf->Cell_utf8_vers_iso_8859_15(25,6,number_format(round($resultat['produits_prix_udv']*$resultat['sum(commande_quantite)'],2),2,',',' '),1,0,'R');
				}
			$pdf->Ln();
		}
		else
		{
			$pos_y=$pdf->gety();
			$pos_x=$pdf->getx();
			$pdf->MultiCell_utf8_vers_iso_8859_15(40,6,$resultat['produits_nom'],1);
			$pdf->setxy($pos_x+40,$pos_y);
			$pdf->MultiCell_utf8_vers_iso_8859_15(40,12,$resultat['produits_conditionnement'],1);
			$pdf->setxy($pos_x+80,$pos_y);
			$pos_x=$pdf->getx();
			if ($resultat['produits_vrac']!=0)
				{
				// Produit en vrac
				// prix d'un conditionnement
				$pdf->MultiCell_utf8_vers_iso_8859_15(30,12,number_format(round($resultat['produits_prix_udv']*abs($resultat['produits_vrac']/(1+$resultat['produits_TVA']/100)),2),2,',',' '),1,'R');
				$pdf->setxy($pos_x+30,$pos_y);	
				// on calcule le nombre de conditionnement à partir de la quantité commandée et du nombre d'UDV par conditionnement
				$pdf->MultiCell_utf8_vers_iso_8859_15(15,12,$resultat['sum(commande_quantite)']/abs($resultat['produits_vrac']),1,'R');
				$pdf->setxy($pos_x+45,$pos_y);
				// prix total
				$pdf->MultiCell_utf8_vers_iso_8859_15(25,12,number_format(round($resultat['produits_prix_udv']*$resultat['sum(commande_quantite)']/(1+$resultat['produits_TVA']/100),2),2,',',' '),1,'R');
				$pdf->setxy($pos_x+70,$pos_y);
				$pdf->MultiCell_utf8_vers_iso_8859_15(25,12,number_format(round($resultat['produits_prix_udv']*$resultat['sum(commande_quantite)'],2),2,',',' '),1,'R');
				}
			else
				{
				$pdf->MultiCell_utf8_vers_iso_8859_15(30,12,number_format(round($resultat['produits_prix_udv']/(1+$resultat['produits_TVA']/100),2),2,',',' '),1,'R');
				$pdf->setxy($pos_x+30,$pos_y);
				$pdf->MultiCell_utf8_vers_iso_8859_15(15,12,$resultat['sum(commande_quantite)'],1,'R');
				$pdf->setxy($pos_x+45,$pos_y);
				$pdf->MultiCell_utf8_vers_iso_8859_15(25,12,number_format(round($resultat['produits_prix_udv']*$resultat['sum(commande_quantite)']/(1+$resultat['produits_TVA']/100),2),2,',',' '),1,'R');
				$pdf->setxy($pos_x+70,$pos_y);
				$pdf->MultiCell_utf8_vers_iso_8859_15(25,12,number_format(round($resultat['produits_prix_udv']*$resultat['sum(commande_quantite)'],2),2,',',' '),1,'R');
				}
		}		
		$total_HT +=$resultat['produits_prix_udv']*$resultat['sum(commande_quantite)']/(1+$resultat['produits_TVA']/100);
		$total_TTC +=$resultat['produits_prix_udv']*$resultat['sum(commande_quantite)'];
		}
	$pdf->Cell_utf8_vers_iso_8859_15(125,6,"Total",1,0,'R');
	$pdf->Cell_utf8_vers_iso_8859_15(25,6,number_format(round($total_HT,2),2,',',' '),1,0,'R');
	$pdf->Cell_utf8_vers_iso_8859_15(25,6,number_format(round($total_TTC,2),2,',',' '),1,0,'R');
	$pdf->Output("Bon de commande ".$fournisseurs_nom." du ".date("d_m_Y").".pdf","I");
	}
?>
