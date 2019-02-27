<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);

$produits_ID=$_POST['produits_ID'];

// Dans le cas d'une modif de l'existant et d'un ajout
if ((isset($_POST['ajust_manuel_modif_quantite'])) or (isset($_POST['ajust_manuel_ajout_membre'])))
	{
	// On commance par supprimer toutes les commandes
	mysqli_query($link, "delete from commande where commande_produit=$produits_ID;");
	$nombre_commande=$_POST['nombre_commande'];
	$modif=$_POST['modif'];
	$i=0;
	while ($i<3*$nombre_commande)
		{
		$ajustement=$modif[$i++];
		$ID=$modif[$i++];
		$quantite=$modif[$i++];
		if ($quantite>0)
			$requete = mysqli_query($link, "insert into commande values ($ID,$commission,$produits_ID,$quantite,'$ajustement');");
		}
	}

// Dans le cas d'un ajout
if (isset($_POST['ajust_manuel_ajout_membre']))
	{
	if ($_POST['ajust_manuel_ajout_membre_quantite']>0)
		{
		$quantite_ajoutee=$_POST['ajust_manuel_ajout_membre_quantite'];
		$membre_ajoute=$_POST['membre_ajoute'];
		if (mysqli_num_rows(mysqli_query($link, "select commande_produit from commande where (commande_membre=$membre_ajoute and commande_produit=$produits_ID);"))==0)
			{
			$requete = mysqli_query($link, "insert into commande values ($membre_ajoute,$commission,$produits_ID,$quantite_ajoutee,'0');");
			}
		}
	}




// Suppression des commandes pour lesquelles la quantité est nulle :
// mysqli_query($link, "delete from commande where (commande_quantite=0 and commande_commission=$commission);");

?>


<html>
<head>
<link rel="stylesheet" href="style.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
<script LANGUAGE="Javascript"> 
function calcul_total(ligne,nb_membres)
{ 
variable = "quant_modif"+ligne;
quantite_modifiee= document.getElementById(variable);

/*test=quantite_modifiee.value;
test=Math.round(test*100)/100;
alert(test);	//ligne 8
if(test=="NaN")
{ 
	alert("toto");
	alert("toto");
	alert("toto");
	variable="quant_init"+ligne;
	quantite_init= document.getElementById(variable);
	quantite_modifie.value=eval(quantite_init.value);
	alert("Vous devez saisir un nombre\n Attention : utiliser le point \".\" et pas la virgule \",\".\nExemple : taper 1.5 et pas 1,5");
}
*/
total_quantite_modifiee=0;
for (i=1;i<=nb_membres;i++)
{
	variable = "quant_modif"+i;
	quantite_modifiee= document.getElementById(variable);
	
	total_quantite_modifiee=eval(total_quantite_modifiee+"+"+quantite_modifiee.value);
	
}
total = document.getElementById('total_quantite_modifiee');
total.value=Math.round(total_quantite_modifiee*100)/100;
} 
</script>
</head>
<body>
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">


<?php
$requete=mysqli_query($link, "select produits_udv,produits_nom,produits_conditionnement,produits_prix_udv,produits_vrac from produits where produits_ID=$produits_ID;");
$resultat=mysqli_fetch_array($requete);
$produits_nom=$resultat['produits_nom'];
$produits_conditionnement=$resultat['produits_conditionnement'];
$produits_prix_udv=$resultat['produits_prix_udv'];
$produits_udv=$resultat['produits_udv'];
$produits_vrac=abs($resultat['produits_vrac']);
echo "<H3>Modification manuelle de la commande de $produits_nom en $produits_conditionnement.</H3><p><H4>Pour supprimer une commande, forcer la quantité commandée à 0.</H4>\n";

$requete=mysqli_query($link, "select ID,commande_quantite, commande_ajustement, nom_complet from commande,membres where (commande_membre=ID and commande_produit=$produits_ID) order by nom_complet;");

// Initlalisation pour ajustement automatique
if ($produits_vrac!=0)
{
	$ajustement_plus=0;
	$ajustement_moins=0;
}

// Initialisation indexation pour le calcul automatique
$i=0;
?>

<form name='ajustement_manuel' action=<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?commission=".$commission; ?> method='post'>
<?php echo "<input type='hidden' name='produits_ID' value=$produits_ID>\n";?>

<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Membre</th>
			<th colspan="1">Quantite initiale <?php echo $produits_udv; ?></th>
			<?php
			if ($produits_vrac!=0)
			{
				echo '<th colspan="1">Ajustement</th>';
			}?>
			<th colspan="1">Quantité modifiée</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<?php
		$total_quantite=0;
		$total_quantite_modifiee=0;
		$nombre_membres=mysqli_num_rows($requete);
		while (($resultat = mysqli_fetch_array($requete)))
		{
			$i++;
			echo '<tr valign="middle">';
			// Nom
			echo '<td colspan="1" rowspan="1" align="left">';
			echo $resultat['nom_complet'];
			print("</td>\n");
			// quantite
			$total_quantite +=$resultat['commande_quantite'];
			$total_quantite_modifiee+=$resultat['commande_quantite'];
			echo '<td colspan="1" rowspan="1" align="right">';
			//echo $resultat['commande_quantite'];
			$id='quant_init'.$i;
			echo	"<input style='text-align:center;' type='text' id='$id'value='".$resultat['commande_quantite']."' size=1 readonly>";
			print("</td>\n");
			// Ajustement
			if ($produits_vrac!=0)
			{
				echo '<td colspan="1" rowspan="1" align="right">';
				// Ajustement automatique
				if ($resultat['commande_ajustement']>0)
				{
					$ajustement_plus += $resultat['commande_ajustement'];
					echo "+";
				}
				else
					$ajustement_moins -= $resultat['commande_ajustement'];
				echo $resultat['commande_ajustement'];
				echo "<input type='hidden' name='modif[]' value=".$resultat['commande_ajustement'].">";
				echo "</td>\n";
			}
			else echo "<input type='hidden' name='modif[]' value='0'>";
			// Prix
			echo '<td colspan="1" rowspan="1" align="right">';
			$id='quant_modif'.$i;
			echo "<input type='hidden' name='modif[]' value=".$resultat['ID'].">";
			echo	"<input style='text-align:center;' type='text' id='$id' name='modif[]' value='".$resultat['commande_quantite']."' size=1 onKeyUp=calcul_total($i,$nombre_membres);>";
			print("</td>\n");
			echo '</tr>';
		}
		
	?>
	<tr valign="middle">
		<td colspan="1" rowspan="1" align="left"><b>Total : </b></td>
		<td colspan="1" rowspan="1" align="right"><?php echo $total_quantite; ?></td>
		<?php 
		if ($produits_vrac!=0)
			echo "<td colspan='1' rowspan='1' align='right'>\n+$ajustement_plus/-$ajustement_moins\n</td>"; ?>
		<td colspan="1" rowspan="1" align="right"><?php print("<input type='text' style='text-align:center;' id='total_quantite_modifiee'  align='center' size=5 value='$total_quantite_modifiee' readonly> ");?></td>
	</tr>
	</tbody>
</table>

<input type='hidden' name='nombre_commande' value=<?php echo $nombre_membres;?>>
<input class='bouton' type='submit'  name='ajust_manuel_modif_quantite' value='Valider les modifications'>

<p><p>
<H4>Ajouter une commande à un membre n'ayant pas commandé :</H4>
<p><p>
Membre :
<?php $requete=mysqli_query($link, "select ID,nom_complet from membres where (ID not in (select commande_membre from commande where commande_produit=$produits_ID)) AND (statut!=".pow(2,12).") order by nom_complet;");
if  (mysqli_num_rows($requete)!=0)
	{
	echo "<select name='membre_ajoute'>\n";
	while (($resultat = mysqli_fetch_array($requete)))
		{
		echo "<option value=".$resultat['ID'].">".$resultat['nom_complet']."</option>";
		}
	echo "</select>";
	}
?>

Quantité :<input style='text-align:center;' name='ajust_manuel_ajout_membre_quantite' type='text' value='0' size=1>
<p><p>
	

<input class='bouton' type='submit' name='ajust_manuel_ajout_membre' value='Ajouter une commande et valider les modifications'>
</form>

<H4> Ajustement automatique ?? </H4>
<?php
// Ajustement automatique si possible
if ($produits_vrac!=0)
{
	$nb_conditionnement=floor($total_quantite/$produits_vrac);
	$reste_conditionnement=fmod($total_quantite,$produits_vrac);
	echo "Cela fait $nb_conditionnement conditionnement entier et il reste $reste_conditionnement.<p>\n";
	if ($reste_conditionnement==0)
	{
		if ( ($ajustement_plus==0) and ($ajustement_moins==0) )
		{
			// Commande déjà ajustée
			echo "Commande ajustée<p>\n";
		}
		else
		{
			echo "Pile-poil. Petit veinard !<p>\n";
			echo "<form action='".htmlspecialchars($_SERVER['PHP_SELF'])."?commission=$commission' method='post'>\n";
			echo "Remettre à 0 les ajustements ?\n";
			echo "<input type='hidden' name='produits_ID' value=$produits_ID>\n";
			echo "<input class='bouton' type='submit' name='nettoyer_ajustements' value='Valider'>\n";
			echo "</form>\n";
		}
	}
	elseif (($produits_vrac-$reste_conditionnement)<=$ajustement_plus)
	{
		// ajustement vers le haut
		$nb_conditionnement++;
		echo "Ajustement possible à la hausse avec $nb_conditionnement  conditionnement(s)<p>\n";
		// Formulaire
		echo "<form action='ajustement_automatique.php?commission=$commission' method='post'>\n";
		echo "<input type='hidden' name='produits_ID' value=$produits_ID>\n";
		echo "<input type='hidden' name='a_regler' value=".($produits_vrac-$reste_conditionnement).">\n";
		echo "<input class='bouton' type='submit' name='ajustement_haut' value='Ajustement automatique'>\n";
		echo "</form>\n";
	}
	elseif ($reste_conditionnement<=$ajustement_moins)
	{
		echo "Ajustement possible à la baisse avec $nb_conditionnement  conditionnement(s)<p>\n";
		// Formulaire
		echo "<form action='ajustement_automatique.php?commission=$commission' method='post'>\n";
		echo "<input type='hidden' name='produits_ID' value=$produits_ID>\n";
		echo "<input type='hidden' name='a_regler' value=$reste_conditionnement>\n";
		echo "<input class='bouton' type='submit' name='ajustement_bas' value='Ajustement automatique'>\n";
		echo "</form>\n";
	}
	else
		echo "Ajustement impossible :-( <p>\n";
}
?>

<div align="center"><a href="gestion_commande.php?commission=<?php echo $commission; ?>">Retour à la gestion de commande</a></div>

</div>
</body>
 </html>
