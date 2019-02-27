<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];

// La commande est elle verrouillée ?
$requete=mysqli_query($link, "select commissions_statut_commande,commissions_contrib_caisse from commissions where commissions_ID=$commission;");
$resultat = mysqli_fetch_array($requete);

if ($resultat['commissions_statut_commande']==0)
	{
	header("Location: $BASE_URL/contenu_commande_bloquee.php?commission=".$commission);  
	exit();
	}
 
$contrib_caisse=$resultat['commissions_contrib_caisse'];
	
$liste_choix_ajustement=array(-2,-1,0,0.5,1,2,3,4);
?>


<html> 
<head> 
<title>Edition du bon de commande de 
<?php
echo $nom_commission[$commission];
?>
</title> 

<SCRIPT LANGUAGE="Javascript"> 
function recalcul_article(num_article,nb_article) 
{ 
nom = "quantite_"+num_article;
quantite = document.getElementById(nom);
nom = "prix_udv_"+num_article;
prix_udv = document.getElementById(nom);
nom = "total_article_"+num_article;
total_article = document.getElementById(nom);
total_article.value=eval(quantite.value*prix_udv.value);
total_article.value=Math.round(total_article.value*100)/100;
if(total_article.value=="NaN" )
{ 
	total_article.value=0;
	quantite.value=0;
	alert("Vous devez saisir un nombre\n Attention : utiliser le point \".\" et pas la virgule \",\".\nExemple : taper 1.5 et pas 1,5");
}
calcul_total(nb_article);

// Forçage de l'ajustement à 0.5
ajustement=document.getElementById("ajust_"+num_article);
//alert("ajust_"+num_article);

if (quantite.value!=='0')
{
	ajustement.selectedIndex=3;
//	alert(ajustement.selectedIndex);
}
else
{
	ajustement.selectedIndex=2;	
}

} 

function calcul_total(nb_article)
{ 
tot=0;
for (i=1;i<=nb_article;i++)
{
	nom = "total_article_"+i;
	total_article = document.getElementById(nom);
	
	tot=eval(tot+"+"+total_article.value);
	
}
contrib=document.getElementById("contrib_caisse");
tot = eval(tot+"+"+contrib.value);
total.value=Math.round(tot*100)/100;
} 

function newWindow(newContent) 
  { 
winContent = window.open(newContent, 'Fiche','toolbar=no,status=no,width=300 ,height=300,scrollbars=yes,location=no,resize=yes,menubar=no,top=200,left=400') 
 winContent.focus() 
  } 

</SCRIPT>

<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">


<H2>
Bon de commande des <?php echo $nom_commission[$commission];?>
<font size=2 type='normal'>
<a href="pdf_liste_produits.php?commission=<?php echo $commission; ?>" target="_
blank">Liste des produits au format PDF</a></font>
</H2>

<?php
print("<form name='saisie_commande' action='validation_commande.php?commission=$commission' method='post'>\n");
?>

<table>
	<thead valign="middle">
		<tr>
			<th colspan="1">Nom du produit</th>
			<th colspan="1">Fournisseur</th>
			<th colspan="1">Conditionnement</th>
			<th colspan="1">Unité De Vente</th>
			<th colspan="1">Prix de l'U.D.V (en €)</th>
			<th colspan="1">Pour comparer</th>
			<th colspan="1">Quantité</th>
			<th colspan="1">Ajustement</th>
			<th colspan="1">Total</th>
			
		</tr>
	</thead>
	<tbody valign="middle">
		<?php
			// Remplissage des données communes et initialisation des quantités à 0
			$requete = mysqli_query($link, "select * from (produits left join fournisseurs on produits.produits_fournisseur=fournisseurs.fournisseurs_ID left join rubriques on produits.produits_rubrique=rubriques.rubriques_ID)  where (produits_commission=$commission and produits_actif=1) order by rubriques_nom,fournisseurs_nom,produits_nom;");
			$liste_produit = array();
			$commande_membre=array();
			$index=array();
			$nombre_produits=0;
			
			while(($resultat = mysqli_fetch_array($requete)))
			{
				$nombre_produits++;
				
				$produits_ID=$resultat['produits_ID'];
				
				// Une colonne pour pouvoir indexer le tableau de 1 à nombre_produits
				$index[$nombre_produits]=$produits_ID;
				
				$liste_produit[$produits_ID]['nom']=$resultat['produits_nom'];
				$liste_produit[$produits_ID]['rubrique']=$resultat['rubriques_nom'];
				$liste_produit[$produits_ID]['fournisseur']=$resultat['fournisseurs_nom'];
				$liste_produit[$produits_ID]['conditionnement']=$resultat['produits_conditionnement'];
				$liste_produit[$produits_ID]['udv']=$resultat['produits_udv'];
				$liste_produit[$produits_ID]['prix_comparable']=$resultat['produits_prix_comparable'];
				$liste_produit[$produits_ID]['prix_udv']=$resultat['produits_prix_udv'];
				$liste_produit[$produits_ID]['vrac']=$resultat['produits_vrac'];
				if ($resultat['produits_description']=='')
					$liste_produit[$produits_ID]['description_dispo']=0;
				else
					$liste_produit[$produits_ID]['description_dispo']=1;
				
				// le tableau commande est rendu monodimensionnel
				$commande[2*$produits_ID]="0";		// stocke la quantité
				$commande[2*$produits_ID+1]="";		// stocke l'ajustement
				
			}
			
			// Modification des lignes pour lesquelles il existe une entrée dans la table commande
			$membre = $_SESSION['gpt_ID'];
			$requete = mysqli_query($link, "select commande_produit, commande_quantite, commande_ajustement from commande where (commande_membre=$membre and commande_commission=$commission);");
			
			if ($requete)
			{
				while(($resultat = mysqli_fetch_array($requete)))
				{
					$produits_ID=$resultat['commande_produit'];
					$quantite=$resultat['commande_quantite'];
					$ajustement=$resultat['commande_ajustement'];
					$commande[2*$produits_ID]=$quantite;
					$commande[2*$produits_ID+1]=$ajustement;
				}
			}
			
			// Sauvegarde du nombre de produits dans la table transmise en POST
			print ("<input type='hidden' name='nombre_produit' value='$nombre_produits'>");
			
			// Initialisation pour le calcul du total
			$total=0;
			$old_rubrique='';
			for ($i=1;$i<=$nombre_produits;$i++)
			{
				$produits_ID=$index[$i];
				
				// sauvegarde de l'ID du produit dans la table transmis en POST
				print ("<input type='hidden' name='commande[]' value='$produits_ID' size=3 >");
				// Affichage de la rubrique au besoin
				if ($liste_produit[$produits_ID]['rubrique']!=$old_rubrique)
				{
					$old_rubrique=$liste_produit[$produits_ID]['rubrique'];
					echo "<tr valign='middle'>\n";
					echo '<td colspan="9" rowspan="1" align="center">';
					echo "<strong><font color='blue'>$old_rubrique</font></strong>";
					echo "</td>\n";
					echo "</tr>\n";
				}
				echo '<tr valign="middle">';
				// Nom
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $liste_produit[$produits_ID]['nom'];
								
$POPUP = <<<BIDON
<a href="javascript:void newWindow('popup_info_produit.php?produits_ID=$produits_ID')"><img src='infos.gif'></a>\n 
BIDON;

				if ($liste_produit[$produits_ID]['description_dispo']==1)
					echo "\t".$POPUP;
				print("</td>\n");
				// Fournisseur
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $liste_produit[$produits_ID]['fournisseur'];
				print("</td>\n");
				//  Conditionnement
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $liste_produit[$produits_ID]['conditionnement'];
				print("</td>\n");
				//  UDV
				echo '<td colspan="1" rowspan="1" align="left">';
				echo $liste_produit[$produits_ID]['udv'];
				print("</td>\n");
				//  Prix de l'UDV
				echo '<td colspan="1" rowspan="1" align="center">';
				$prix_udv=$liste_produit[$produits_ID]['prix_udv'];
				$id='prix_udv_'.$i;
				print("<input class='prix_udv' type='text' id='$id' name='prix_udv' value='".number_format($prix_udv,2,'.','')."' readonly size=5>");
				//echo $liste_produit[$produits_ID]['prix_udv'];
				print("</td>\n");
				//  Prix au kg
				echo '<td colspan="1" rowspan="1" align="left">';
				echo "soit ".$liste_produit[$produits_ID]['prix_comparable'];
				print("</td>\n");
				//  Quantité
				echo '<td colspan="1" rowspan="1" align="center">';
				$temp=2*$produits_ID;
				$id='quantite_'.$i;
				print ("<input style='text-align:center;' type='text' id='$id' name='commande[]' value='$commande[$temp]' size=2 onKeyUp=recalcul_article($i,$nombre_produits); >");
				print("</td>\n");
				//  Ajustement
				echo '<td colspan="1" rowspan="1" align="center">';
				$temp=2*$produits_ID+1;
				//print ("<input type='text' name='commande[]' value='$commande[$temp]' size=8 >");
				if ($liste_produit[$produits_ID]['vrac']!=0)
				{
					$id='ajust_'.$i;
					echo "<select name='commande[]' id='$id'>\n";
					for ($k=0;$k<=7;$k++)
					{
						if ( ($liste_produit[$produits_ID]['vrac']>0) || ( $liste_choix_ajustement[$k] != 0.5 ))
						{
							// On élimine le choix 0.5 dans le cas des lots
							if ($liste_choix_ajustement[$k]==$commande[$temp])
							{
								if ($liste_choix_ajustement[$k]>0)
									echo "<option value=$liste_choix_ajustement[$k] selected>+$liste_choix_ajustement[$k]</option>";
								else
									echo "<option value=$liste_choix_ajustement[$k] selected>$liste_choix_ajustement[$k]</option>";
							}
							else
							{
								if ($liste_choix_ajustement[$k]>0)
									echo "<option value=$liste_choix_ajustement[$k]>+$liste_choix_ajustement[$k]</option>";
								else
									echo "<option value=$liste_choix_ajustement[$k]>$liste_choix_ajustement[$k]</option>";
							}
						}
					}
				}
				else
				{
					echo "<input type='text' value=0 size=1 disabled>";
					echo "<input type='hidden' name='commande[]' value=0>";
				}
				echo "</select>\n";
				print("</td>\n");
				//  Total
				echo '<td colspan="1" rowspan="1" align="center">';
				$valeur_initiale=$commande[2*$produits_ID]*$liste_produit[$produits_ID]['prix_udv'];
				$total +=$valeur_initiale;
				$id='total_article_'.$i;
				print("<input style='text-align:center;' type='text' id='$id' name='total_article' value=$valeur_initiale size=5 readonly>");
				print("</td>\n");
				echo "</tr>";
			}
		// Si c'est le cas : ajout de la "contribution caisse"
		
		if ($contrib_caisse != 0)
  			{
     			$total += $contrib_caisse;
		?>
		<tr>
		<td colspan="8" rowspan="1" align="right">Contribution caisse : </td>
		<td colspan="1" rowspan="1" align="right"><input type='text' style='text-align:center;' id='contrib_caisse' align='center' size=5 value='<?php echo $contrib_caisse;?>' readonly> </td>
  		</tr>
    		<?php	}
	else
			echo "<input type='hidden' name='contrib' id='contrib_caisse' value=$contrib_caisse>";
      		?>
		<tr>
		<td colspan="8" rowspan="1" align="right">Total : </td>
		<td colspan="1" rowspan="1" align="right"><?php
print("<input type='text' style='text-align:center;' id='total'  align='center' size=5 value='$total' readonly> ");
?></td>
		</tr>
	</tbody>
</table>


<input class="bouton" type="submit" name="submit" value="Valider">

</form>  

</div>
</body>
</html>
