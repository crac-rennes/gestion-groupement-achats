<?php
session_start();
require("fonctions.php");
verification_identification();
verification_admin();
?>

<html> 
<head> 
<title>Suppression d'une commission
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<font color='red'> Attention : cette opération supprimera la commission 

<?php
$requete = mysqli_query($link, "select commissions_nom from commissions where commissions_ID=".$_POST['commission_ID'].";");
$resultat = mysqli_fetch_array($requete);
echo $resultat['commissions_nom'];
?>
 et les produits associés.
</font><p>

<font color='red'> Souhaitez vous poursuivre ? </font><p>

<form action='gestion_commissions.php' method="post">
	<fieldset> 	
		<input type="hidden" name="commission_ID" value="<?php echo $_POST['commission_ID']; ?>">
		<input class="bouton" type="submit" name="suppression_commission" value="Oui" />        
		<input class="bouton" type="submit" name="suppression_commission" value="Non" />        
	</fieldset>    
</form>  


</div>
</body>
</html>
