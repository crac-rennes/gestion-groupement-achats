<?php
session_start();
require("fonctions.php");
verification_identification();
verification_admin();


if (isset($_POST['commission_ID']))
	{
	$commission_ID=$_POST['commission_ID'];
	$requete=mysqli_query($link, "select commissions_nom,commissions_contrib_caisse from commissions where commissions_ID=$commission_ID;");
	$resultat=mysqli_fetch_array($requete);
	$commission_nom=$resultat['commissions_nom'];
 	$commissions_contrib_caisse=$resultat['commissions_contrib_caisse'];
	}
?>

<html> 
<head> 
<title>Modification d'une commission
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<H2>Modification d'une commission
</H2>

<form action="gestion_commissions.php" method="post">
<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Index</th>
			<th colspan="1">Nom</th>
			<th colspan="1">Contribution caisse</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<tr valign="middle">
			<td colspan="1" rowspan="1" align="left">
				<select name='index'>
				<?php
					echo "<option value=$commission_ID>$commission_ID</option>\n";
					$requete=mysqli_query($link, "select commissions_ID from commissions order by commissions_ID;");
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
			<td colspan="1" rowspan="1" align="left">
				<input type='hidden' name='old_commission_nom' value='<?php echo $commission_nom;?>'>
				<input type='text' name='commission_nom' maxlength='100' value='<?php echo $commission_nom;?>'>
			</td>
			<td colspan="1" rowspan="1" align="right">
				<input type='text' name='contrib_caisse' maxlength='5' value='<?php echo $commissions_contrib_caisse;?>'>
			</td>
		</tr>
	</tbody>
</table>
<input type='hidden' name='old_index' value=<?php echo $commission_ID;?>>
<input class='bouton' type='submit' name='modif_commission' value='Valider'>
<input class='bouton' type='submit' name='modif_commission' value='Annuler'>
</form>
			
</div>
</body>
</html>
