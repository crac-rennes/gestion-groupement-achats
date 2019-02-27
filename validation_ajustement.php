<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);

$tab_choix=unserialize(rawurldecode($_POST['choisis']));
//$tab_choix=unserialize($_POST['choisis']);
$nb_tab_choix=$_POST['nb_choisis'];
?>

<html>
<head>
<link rel="stylesheet" href="style.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>
<body>
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">


<H4><font color='red'>
Vous allez remettre à 0 tous les ajustements de la commande et effectuer les modifications suivantes :
</font></H4>

<table>
<thead>
	<th> Membre concerné </th>
	<th> Ajouté à la quantité commandée </th>
</thead>
</tbody>
<?php
for ($i=1;$i<=$nb_tab_choix;$i++)
{
	echo "<tr>\n";
	echo "<td align='left'>";
	echo $tab_choix[$i]['membre_nom'];
	echo "</td>\n";
	echo "<td align='right'>";
	echo $tab_choix[$i]['ajustement_retenu'];
	echo "</td>\n";
	echo "</tr>\n";
}
?>
</tbody>
</table>

<H4><font color='red'>
Etes-vous sur ?
</font></H4>

<form action="bon_commande_produit.php?commission=<?php echo $commission;?>" method="post">
<input type=hidden name='produits_ID' value='<?php echo $_POST['produits_ID'];?>' >
<input type=hidden name='choisis' value='<?php echo $_POST['choisis'];?>' >
<input type=hidden name='nb_choisis' value='<?php echo $nb_tab_choix;?>' >
<input class='bouton' type='submit' name='valid_ajust' value='Oui'>
<input class='bouton' type='submit' name='valid_ajust' value='Non'>
</form>

</div>
</body>
 </html>
