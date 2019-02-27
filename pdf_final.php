<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);

// Récupération de la contribution à la caisse
$requete=mysqli_query($link, "select commissions_statut_commande,commissions_contrib_caisse from commissions where commissions_ID=$commission;");
$resultat = mysqli_fetch_array($requete);
$contrib_caisse=$resultat['commissions_contrib_caisse'];

require('fpdf_polo.php');



function ecrit_header(&$pdf,$nom)
{
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',11);
	$pdf->Cell_utf8_vers_iso_8859_15(150,6,$nom);
	$pdf->Ln(10);
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell_utf8_vers_iso_8859_15(45,6,'Nom du produit',1);
	$pdf->Cell_utf8_vers_iso_8859_15(35,6,'Conditionnement',1);
	$pdf->Cell_utf8_vers_iso_8859_15(30,6,'Unité de vente',1);
	$pdf->Cell_utf8_vers_iso_8859_15(25,6,"Prix U.D.V ()",1);
	$pdf->Cell_utf8_vers_iso_8859_15(20,6,'Quantité',1);
	if (!isset($_POST['pour_commission']))
		{
		$pdf->Cell_utf8_vers_iso_8859_15(20,6,'Total ()',1);
		}
	else
		{
		$pdf->Cell_utf8_vers_iso_8859_15(20,6,'Ajustements',1);
		}
	$pdf->Ln();
}

function ecrit_produit(&$pdf,$resultat,&$total,&$total_fournisseur)
{
	$pdf->SetFont('Arial','',8);
	$nom_affichage=$resultat['produits_nom'];
	if ($pdf->GetStringWidth($nom_affichage)>44)
	{
		while  ($pdf->GetStringWidth($nom_affichage)>40)
			{
			$nom_affichage=substr($nom_affichage,0,strlen($nom_affichage)-1);
			}
	$nom_affichage=$nom_affichage."...";
	}
	$pdf->Cell_utf8_vers_iso_8859_15(45,5.5,$nom_affichage,1);
	$pdf->Cell_utf8_vers_iso_8859_15(35,5.5,$resultat['produits_conditionnement'],1);
	$pdf->Cell_utf8_vers_iso_8859_15(30,5.5,$resultat['produits_udv'],1);
	$pdf->Cell_utf8_vers_iso_8859_15(25,5.5,$resultat['produits_prix_udv'],1,0,'R');
	$pdf->Cell_utf8_vers_iso_8859_15(20,5.5,$resultat['commande_quantite'],1,0,'R');
	if (!isset($_POST['pour_commission']))
	{
		$prix=$resultat['total_produit'];
		$total+=$prix;
		$total_fournisseur+=$prix;
		$pdf->Cell_utf8_vers_iso_8859_15(20,5.5,number_format($prix,2,",",""),1,0,'R');
	}
	else
	{
	$pdf->Cell_utf8_vers_iso_8859_15(20,5.5,$resultat['commande_ajustement'],1,0,'R');
	}
	$pdf->Ln();
}	

function ecrit_cloture_fournisseur(&$pdf,$total_fournisseur,$fournisseur)
{
	if (!isset($_POST['pour_commission']))
	{
	$pdf->Cell_utf8_vers_iso_8859_15(155,6,'Total '.$fournisseur,1,0,'R');
	$pdf->Cell_utf8_vers_iso_8859_15(20,6,number_format($total_fournisseur,2,",",""),1,0,'R');
	$pdf->Ln();	
	}
}

function ecrit_cloture_membre(&$pdf,$total)
{
	if (!isset($_POST['pour_commission']))
	{
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell_utf8_vers_iso_8859_15(155,6,'Total commande',1,0,'R');
	$pdf->Cell_utf8_vers_iso_8859_15(20,6,number_format($total,2,",",""),1,0,'R');
	}
}

function ecrit_nouveau_fournisseur(&$pdf,$nom)
{
	$pdf->Cell_utf8_vers_iso_8859_15(175,0.3,'',1,0,'C');
	$pdf->Ln();
	$pdf->SetFont('Arial','I',10);
	$pdf->SetFillColor(255,255,200);
	$pdf->Cell_utf8_vers_iso_8859_15(175,6,$nom,1,0,'C',1);
//	$pdf->Cell_utf8_vers_iso_8859_15(175,6,$nom,1,0,'C');
	$pdf->Ln();
}

function ecriture_contrib_caisse(&$pdf,$contrib_caisse)
{
	if (!isset($_POST['pour_commission']))
	{
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell_utf8_vers_iso_8859_15(155,6,'Contribution caisse',1,0,'R');
	$pdf->Cell_utf8_vers_iso_8859_15(20,6,$contrib_caisse,1,0,'R');
	$pdf->Ln();
	}
}

$requete = mysqli_query($link, "select ID,nom_complet,fournisseurs_ID,fournisseurs_nom,produits_nom,produits_conditionnement,produits_udv,produits_prix_udv,commande_quantite, commande_ajustement, round(produits_prix_udv*commande_quantite,2) as total_produit from produits,fournisseurs,commande,membres where (produits_fournisseur=fournisseurs_ID and produits_ID=commande_produit and commande_membre=ID and produits_commission=$commission and produits_actif=1) order by nom_complet,fournisseurs_nom,produits_nom;");


// Initialisations
$pdf=new FPDF();
$pdf->SetAutoPageBreak(True);

$membre_old='';


// Si la requete est non vide
if (mysqli_num_rows($requete)!=0)
{
	while(($resultat = mysqli_fetch_array($requete)))
	{
		if ($membre_old=='')
		{
			$membre_old=$resultat['ID'];
			// premier membre
			ecrit_header($pdf,$resultat['nom_complet']);
			// Initialisation fournisseur
			$fournisseur_old=$resultat['fournisseurs_nom'];
			ecrit_nouveau_fournisseur($pdf,$resultat['fournisseurs_nom']);
			
			// Premier produit
			$total=0;
			$total_fournisseur=0;
			ecrit_produit($pdf,$resultat,$total,$total_fournisseur);
		}
		elseif ($resultat['ID']!==$membre_old)
		{
			// On cloture le tableau précédent
			ecrit_cloture_fournisseur($pdf,$total_fournisseur,$fournisseur_old);
			if ($contrib_caisse != 0)
			{
				ecriture_contrib_caisse($pdf,$contrib_caisse);
    				$total +=$contrib_caisse;
			}
			ecrit_cloture_membre($pdf,$total);
			
			// Nouveau membre
			$membre_old=$resultat['ID'];
			ecrit_header($pdf,$resultat['nom_complet']);
			
			// Initialisation fournisseur
			$fournisseur_old=$resultat['fournisseurs_nom'];
			ecrit_nouveau_fournisseur($pdf,$resultat['fournisseurs_nom']);
			
			// Premier produit
			$total=0;
			$total_fournisseur=0;
			ecrit_produit($pdf,$resultat,$total,$total_fournisseur);
		}
		else
		{
			if ($fournisseur_old==$resultat['fournisseurs_nom'])
			{
				// Nouvelle ligne produit
				ecrit_produit($pdf,$resultat,$total,$total_fournisseur);
			}
			else
			{
				// total fournisseur précédent
				ecrit_cloture_fournisseur($pdf,$total_fournisseur,$fournisseur_old);
				
				// Initialisation fournisseur
				$fournisseur_old=$resultat['fournisseurs_nom'];
				ecrit_nouveau_fournisseur($pdf,$resultat['fournisseurs_nom']);
				
				// Premier produit du nouveau fournisseur
				$total_fournisseur=0;
				ecrit_produit($pdf,$resultat,$total,$total_fournisseur);
			}
		}
	}
// On cloture le tableau précédent
ecrit_cloture_fournisseur($pdf,$total_fournisseur,$fournisseur_old);
if ($contrib_caisse != 0)
{
	ecriture_contrib_caisse($pdf,$contrib_caisse); 
	$total +=$contrib_caisse;
}
ecrit_cloture_membre($pdf,$total);
}
$pdf->Output("Commande par famille.pdf","I");
?>
