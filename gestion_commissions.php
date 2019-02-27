<?php
session_start();
require("fonctions.php");
verification_identification();
verification_admin();


if (isset($_POST['ajout_commission']))
	{
	$index=$_POST['index'];
	// Test pour savoir si l'index choisi est valide
	if (($index<1) or ($index>9))
		{
		echo "<font color='red'><b>Erreur l'index doit être compris entre 1 et 9 !!</b></font>";
		}
	else
		{
		$requete = mysqli_query($link, "select commissions_nom from commissions where commissions_ID=$index;");
		if (mysqli_num_rows($requete))
			{
			$resultat=mysqli_fetch_array($requete);
			echo "<font color='red'><b>Erreur : l'index $index est déjà utilisé par la commission ". $resultat['commissions_nom'].". </b></font>";
			}
		else
			{
			if (mysqli_query($link, "insert into commissions (commissions_ID,commissions_nom,commissions_contrib_caisse) values ($index,'".$_POST['commission_nom']."',0);"))
				{
				echo "<font color='green'>Ajout effectué</font>\n";
				//echo "<font color='red'><b>Merci d'actualiser/raffraichir votre affichage pour que la modification soit appliquée dans le cadre du menu (cadre de gauche).</b></font>";
				}
			else
				{
				echo "<font color='red'>Problème lors de l'ajout de la commission !</font>";
				}
			} 
		}
	}

// Deplacement des produits contenus dans une rubrique vers une commission donnée
if (isset($_POST['deplacement_produits']))
{
	if (mysqli_query($link, "update produits set produits_commission=".$_POST['commissions_ID']." where produits_rubrique=".$_POST['rubriques_ID'].";"))
		echo "<font color='green'>Déplacement effectué</font>\n";
	else
		echo "<font color='red'>Problème lors du déplacement</font>\n";
}

// Modification d'une commission (depuis modif_commission.php)
if (isset($_POST['modif_commission']))
	{
	if ($_POST['modif_commission']=='Valider')
		{
		$index=$_POST['index'];
		$old_index=$_POST['old_index'];
		$nom=$_POST['commission_nom'];
		$old_nom=$_POST['old_commission_nom'];
  		$contrib_caisse=$_POST['contrib_caisse'];
		
		//echo $index;
		//echo $old_index;
		//echo $nom;
		//echo $old_nom;
		
		if ($index!=$old_index)
			{
			// Modification de l'index dans la table commission
			if ($requete=mysqli_query($link, "update commissions set commissions_ID=$index, commissions_contrib_caisse=$contrib_caisse where commissions_ID=$old_index;"))
				{
				echo "<font color='green'>Index modifié</font>\n";
				//echo "<font color='red'>Il est nécessaire de rafraichir la page pour que la mise à jour s'applique également aux menus !</font>\n";
				}
			else
				echo "<font color='red'>Problème lors de la modification des index.</font>\n";
			// Modification de l'index des produits concernés
			if ($requete=mysqli_query($link, "update produits set produits_commission=$index where produits_commission=$old_index;"))
				echo "<font color='green'>Mise à jour des produits effecutée.</font>\n";
			else
				echo "<font color='red'>Problème lors de la mise à jour des produits.</font>\n";
			}
			
		if ($nom!=$old_nom)
			{
			if ($requete=mysqli_query($link, "update commissions set commissions_nom='$nom', commissions_contrib_caisse=$contrib_caisse where commissions_ID=$index;"))
				echo "<font color='green'>Mise à jour effecutée.</font>\n";
			}
   		else
     			{
        			// On modifie juste la contribution à la caisse
        			if ($requete=mysqli_query($link, "update commissions set commissions_contrib_caisse=$contrib_caisse where commissions_ID=$index;"))
				echo "<font color='green'>Mise à jour effecutée.</font>\n";
        			}
		}
		
	}

// Suppression d'une commission (depuis suppression_commission.php
if (isset($_POST['suppression_commission']))
	{
	if ($_POST['suppression_commission']=='Oui')
		{
		$commission_ID=$_POST['commission_ID'];
		
		if (mysqli_query($link, "delete from commissions where commissions_ID=$commission_ID;"))
			echo  "<font color='green'>Commission supprimmée.</font><p>\n";
		if (mysqli_query($link, "delete from produits where produits_commission=$commission_ID;"))
			echo  "<font color='green'>Produits associés supprimmés.</font><p>\n";
		//echo "<font color='red'><b>Merci d'actualiser/raffraichir votre affichage pour que la modification soit appliquée dans le cadre du menu (cadre de gauche).</b></font>";

		}
	if ($_POST['suppression_commission']=='Non')
		{
		echo  "<font color='green'>Suppression annulée..</font><p>\n";
		}
	}

?>

<html> 
<head> 
<title>Gestion des commissions
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
<SCRIPT LANGUAGE="Javascript"> 
function rafraichir_menu() 
 	{ 
	parent.menu.location.reload(true);
	} 

</SCRIPT>

</head>  

<body onload="rafraichir_menu()"> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">


<H2>Gestion des commissions</H2>

<H4>Ajouter une commission </H4>

<?php
$requete=mysqli_query($link, "select count(*) from commissions;");
$resultat=mysqli_fetch_array($requete);
if ($resultat['count(*)']<9)
	{?>

	<form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>' method='post'>
	<table  class="inline_tab">
		<thead valign="middle">
			<tr>
				<th>Index</th>
				<th>Nom</th>
			</tr>
		</thead>
		<tbody valign="middle">
			<tr valign="middle">
				<td rowspan="1" align="left">
					<select name='index'>
					<?php
						$requete=mysqli_query($link, "select commissions_ID from commissions 	order by commissions_ID;");	
					$i=1;	
						while($resultat=mysqli_fetch_array($requete))
							{
							$index=$resultat['commissions_ID'];
							while($i!=$index)
								{
								echo "<option value=$i>$i</option>\n";
								$i++;
								}
							$i++;
							}
						while ($i<10)
							{
							echo "<option value=$i>$i</option>\n";
							$i++;
							}
					?>
					</select>
				</td>
				<td rowspan="1" align="left">
					<input type='text' name='commission_nom' maxlength='100'>
				</td>
			</tr>
		</tbody>
	</table>
	<input class='bouton' type='submit' name='ajout_commission' value='Valider'>
	</form>
<?php
	}
else
	echo "Le nombre maximum de commission est atteint !<p>\n";
?>
<H4>Modifier ou supprimer une commission </H4>

<table  class="inline_tab">
	<thead valign="middle">
		<tr>
			<th>Index</th>
			<th>Nom</th>
   			<th cosspan="1">Contribution à la caisse</th>
			<th colspan="2">Modifier / Supprimer</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<?php
			// Remplissage du tableau
			$requete = mysqli_query($link, "select * from commissions order by commissions_ID;");
			while ($resultat = mysqli_fetch_array($requete))
				{
				$commission_ID=$resultat["commissions_ID"];
				echo '<tr valign="middle">';
				// Index
				echo '<td rowspan="1" align="left">';
				echo $commission_ID;
				echo '</td>';
				// Nom
				echo '<td rowspan="1" align="left">';
				echo $resultat["commissions_nom"];
				echo '</td>';
    				// Caisse
				echo '<td rowspan="1" align="right">';
				if ($resultat["commissions_contrib_caisse"]!=0)
    					{echo $resultat["commissions_contrib_caisse"]."€";}
         				else
             				{echo "Pas de contribution dans cette commission";}
				echo '</td>';
				//  Modifier
				echo '<td rowspan="1" align="center" border-width=0>';
				echo "<form action='modif_commission.php' method='post'>\n";
				echo "<input type=hidden name='commission_ID' value=$commission_ID >\n";
				//echo "<input class='bouton' type='submit' name='submit' value='Modifier' />\n";
				echo "<input type='image' name='submit value='Valider' src='9070.ico'>";
				echo "</form>  \n";
				echo '</td>';
				//  Supprimer
				echo '<td rowspan="1" align="center">';
				echo "<form action='suppression_commission.php' method='post'>\n";
				echo "<input type=hidden name='commission_ID' value=$commission_ID >\n";
				//echo "<input class='bouton' type='submit' name='submit' value='Supprimer' />\n";
				echo "<input type='image' name='submit value='Valider' src='picto_poubelle_big.gif'>";
				echo "</form>  \n";
				echo '</td>';
				echo '</tr>';
				}
		?>
	</tbody>
</table>

<H4>Changer une rubrique de commission</H4>


<form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>' method='post'>
<table class="inline_tab">
	<thead valign="middle">
		<tr>
			<th>Rubrique</th>
			<th>Commission de destination</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<tr valign="middle">
			<td align="left">
				<select name='rubriques_ID'>
				<?php
				$requete = mysqli_query($link, "select rubriques_ID,rubriques_nom from rubriques order by rubriques_nom;");
				while (($resultat = mysqli_fetch_array($requete)))
				{
					echo "<option value=".$resultat['rubriques_ID'].">".$resultat['rubriques_nom']."</option>";
				}
				?>
				</select>
			</td>
			<td align="left">
				<select name='commissions_ID'>
				<?php
				$requete = mysqli_query($link, "select commissions_ID,commissions_nom from commissions order by commissions_nom;");
				while (($resultat = mysqli_fetch_array($requete)))
				{
					echo "<option value=".$resultat['commissions_ID'].">".$resultat['commissions_nom']."</option>";
				}
				?>
				</select>
			</td>
		</tr>
	</tbody>
</table>
<input class='bouton' type='submit' name='deplacement_produits' value='Valider'>
</form>

</div>
</body>
</html>	
