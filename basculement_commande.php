<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);

// lorsqu'on vient de la même page après validation
if (isset($_POST['basculement_commande']))
	{
// - tester si on ne rafraichit pas la page
// ??
	// Initialisations
	$nb_membre=$_POST['nb_membre'];
	$a_deplacer=$_POST['a_basculer'];
	$produit_origine=$_POST['produits_ID'];
	$produit_destination=$_POST['produit_destination'];
	$conserver_ajustements=$_POST['conserver_ajustements'];
	if ($_POST['operateur']=='multiplier')	$operateur='*';
	elseif ($_POST['operateur']=='diviser')	$operateur='/';
 	$coefficient=$_POST['coefficient'];
	
// - vérifier que la quantité déplacée est compatible avec la quantite commandée initialement
	$OK=1;
	for ($i=0;$i<$nb_membre;$i++)
		if ($a_deplacer[4*$i+1]<$a_deplacer[4*$i+3])
			$OK=0;

	if ($OK)
		{
		for ($i=0;$i<$nb_membre;$i++)
			{
			$membre=$a_deplacer[4*$i];
			$quant_init=$a_deplacer[4*$i+1];
			$ajust=$a_deplacer[4*$i+2];
			$quant_dep=$a_deplacer[4*$i+3];

			// Si la quantité déplacée est non nulle
			if ($quant_dep<>0)
				{			
// - diminuer les quantités du produit intiale des quantités basculées (éventuellement effacer les lignes vides)
				mysqli_query($link, "update commande set commande_quantite=commande_quantite-$quant_dep where (commande_produit=$produit_origine and commande_membre=$membre and commande_commission=$commission);");

// - créer ou augmenter les quantités commandées dans le produit destination
				if (mysqli_num_rows(mysqli_query($link, "select * from commande where (commande_membre=$membre and commande_produit=$produit_destination);")))
					{
					if ($conserver_ajustements)
						$suffixe = ",commande_ajustement=commande_ajustement+".$ajust.$operateur.$coefficient;
					else
						$suffixe="";	
					mysqli_query($link, "update commande set commande_quantite=commande_quantite+".$quant_dep.$operateur.$coefficient.$suffixe." where (commande_membre=$membre and commande_produit=$produit_destination and commande_commission=$commission);");
					}
				else
					{
					if (!$conserver_ajustements)
						$ajust=0; // on force à zero dans ce cas
					mysqli_query($link, "insert into commande values ($membre,$commission,$produit_destination,".$quant_dep.$operateur.$coefficient.",".$ajust.$operateur.$coefficient.");");
//					echo "insert into commande values ($membre,$commission,$produit_destination,".$quant_dep.$operateur.$coefficient.",'$ajust');";
					}
				}
			}
		// Effacement des commandes vides
		mysqli_query($link, "delete from commande where (commande_quantite=0 and commande_commission=$commission and commande_produit=$produit_origine);");
		
		echo "<font style='color:green'>Déplacement effectué !</font>";
		$requete=mysqli_query($link, "select produits_actif from produits where produits_ID=$produit_destination;");
		$resultat = mysqli_fetch_array($requete);
		if ($resultat['produits_actif']==0)
			echo "<font style='color:red'>Pensez à activer le produit vers lequel les déplacements ont été effectués !</font>";
		}
	else
		{
		echo "Les quantités déplacées sont supérieures aux quantités commandées !";
		}

	}
?>

<html>
<head>
<link rel="stylesheet" href="style.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
<SCRIPT>
function mise_a_jour_total(nb_ligne)
{ 
tot=0;
for (i=1;i<=nb_ligne;i++)
{
	quantite = document.getElementById(i);
	tot=eval(tot+"+"+quantite.value);
	
}
total = document.getElementById('nouveau_total');
total.value=tot;
} 
</SCRIPT>
</head>
<body>
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">


<?php
$produits_ID=$_POST['produits_ID'];
$requete=mysqli_query($link, "select produits_udv,produits_nom,produits_conditionnement,produits_prix_udv,produits_vrac from produits where produits_ID=$produits_ID;");
$resultat=mysqli_fetch_array($requete);
$produits_nom=$resultat['produits_nom'];
$produits_conditionnement=$resultat['produits_conditionnement'];
$produits_prix_udv=$resultat['produits_prix_udv'];
$produits_udv=$resultat['produits_udv'];
$produits_vrac=abs($resultat['produits_vrac']);
echo "<H3>Basculement de tout ou partie des commandes de $produits_nom en $produits_conditionnement.</H3><p>";

echo "UDV : $produits_udv <p>";
$requete=mysqli_query($link, "select commande_membre,commande_quantite, commande_ajustement, nom_complet from commande,membres where (commande_membre=ID and commande_produit=$produits_ID) order by nom_complet;");

if ($produits_vrac!=0)
{
	$ajustement_plus=0;
	$ajustement_moins=0;
}
?>

<form name='basculement_commande' action=<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?commission=".$commission; ?> method='post'>
<input type='hidden' name='produits_ID' value='<?php echo $produits_ID;?>'>
<input type='hidden' name='nb_membre' value='<?php $nb_ligne=mysqli_num_rows($requete);echo $nb_ligne;?>'>

<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Membre</th>
			<th colspan="1">Quantite <?php echo $produits_udv; ?></th>
			<?php
			if ($produits_vrac!=0)
			{
				echo '<th colspan="1">Ajustement</th>';
			}?>
			<th colspan="1">A déplacer</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<?php
		$total_quantite=0;
		$ii=0;
		while (($resultat = mysqli_fetch_array($requete)))
		{
			$ii++;
			echo '<tr valign="middle">';
			// Nom
			echo '<td colspan="1" rowspan="1" align="left">';
			echo $resultat['nom_complet'];
			echo "<input type='hidden' name='a_basculer[]' value='".$resultat['commande_membre']."'>";
			print("</td>\n");
			// quantite
			$total_quantite +=$resultat['commande_quantite'];
			echo '<td colspan="1" rowspan="1" align="right">';
			echo $resultat['commande_quantite'];
			echo "<input type='hidden' name='a_basculer[]' value='".$resultat['commande_quantite']."'>";
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
				echo "<input type='hidden' name='a_basculer[]' value='".$resultat['commande_ajustement']."'>";
				echo "</td>\n";
				}
			else
				echo "<input type='hidden' name='a_basculer[]' value='O'>";
			echo '<td colspan="1" rowspan="1" align="right">';
			echo "<input style='text-align:center;' type='text' name='a_basculer[]' id='$ii' value='".$resultat['commande_quantite']."' size=2 onChange=mise_a_jour_total($nb_ligne)>";
			print("</td>\n");
		}
		?>
		<tr valign="middle">
		<td colspan="1" rowspan="1" align="left">Total : </td>
		<td colspan="1" rowspan="1" align="right"><?php echo $total_quantite; ?></td>
		<?php 
		if ($produits_vrac!=0)
			echo "<td colspan='1' rowspan='1' align='right'>\n+$ajustement_plus/-$ajustement_moins\n</td>"; ?>
		<td colspan="1" rowspan="1" align="right"><input size=2  id='nouveau_total' readonly style='text-align:center;' value='<?php echo $total_quantite; ?>'></td>
	</tr>
	</tbody>
</table>

<p>
Basculer vers : 
<select name='produit_destination'>
<?php
$requete=mysqli_query($link, "select produits_ID,produits_nom,produits_conditionnement, produits_actif, produits_UDV from produits where (produits_commission=$commission and produits_ID<>$produits_ID) order by produits_nom;");
while ($resultat = mysqli_fetch_array($requete))
{
	if ($resultat['produits_actif'])
	{
		if ($resultat['produits_conditionnement'] != "")
		{
			echo "<option value='".$resultat['produits_ID']."'>".$resultat['produits_nom']." en ".$resultat['produits_conditionnement']." (UDV : ".$resultat['produits_UDV'].") </option>\n";
		}
		else
		{
			echo "<option value='".$resultat['produits_ID']."'>".$resultat['produits_nom']." (UDV : ".$resultat['produits_UDV'].") </option>\n";
		}
	}
	else
	{
		if ($resultat['produits_conditionnement'] != "")
		{
			echo "<option style='color:#FF0000;' value='".$resultat['produits_ID']."'><font style='color:red;'>".$resultat['produits_nom']." en ".$resultat['produits_conditionnement']." (UDV : ".$resultat['produits_UDV'].") </font></option>\n";
		}
		else
		{
			echo "<option style='color:#FF0000;' value='".$resultat['produits_ID']."'><font style='color:red;'>".$resultat['produits_nom']." (UDV : ".$resultat['produits_UDV'].") </font></option>\n";
		}
	}
}
?>
</select>
<p>
<font color='red'> Les produits en rouge sont désactivés. Penser à les réactiver pour que la commande ne soit pas "perdue". </font>
<p>
Appliquer aux quantité basculées :
<select name='operateur'>
	<option value="multiplier">multiplier</option>
	<option value="diviser">diviser</option>
</select>
 par<input type='text' name='coefficient' value='1' size="2">

<P>
Conserver les ajustements
<input type='checkbox' name='conserver_ajustements'>
<small>(ils sont affectés de la même opération de multiplication/division et éventuellement ajoutés à ceux existant)</small>
<p>
<input class='bouton' type='submit' name='basculement_commande' value='Basculer'>
</form>

<div align="center"><a href="gestion_commande.php?commission=<?php echo $commission; ?>">Retour à la gestion de commande</a></div>

</div>
</body>
</html>
