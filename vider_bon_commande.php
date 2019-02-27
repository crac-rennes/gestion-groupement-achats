<?php
session_start();
require("fonctions.php");
verification_identification();
$commission=$_GET['commission'];
verification_reponsable($commission);

if (isset($_POST['oui']))
{
	$requete = mysqli_query($link, "delete from commande where commande_commission=$commission;");
	header("Location: $BASE_URL/gestion_commande.php?commission=$commission");
}
elseif (isset($_POST['non']))
{
	header("Location: $BASE_URL/gestion_commande.php?commission=$commission");
}
?>

<html> 
<head> 
	<title>Vider le bon de commande
	</title> 
	<link rel="stylesheet" href="style.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head> 
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<font color='red'> 
Vous allez vider le bon de commande des <?php echo $nom_commission[$commission]; ?><p>
Les commandes de tous les membres pour cette commission seront perdues ! <p>
Etes-vous sur de vouloir continuer ? <p>
</font>
 
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?commission=".$_GET['commission']; ?>" method="post">
	<fieldset>       
		<input class="bouton" type="submit" name="oui" value="Oui" />        
		<input class="bouton" type="submit" name="non" value="Non" />        
	</fieldset>    
</form>  


</div>
</body>
</html>
