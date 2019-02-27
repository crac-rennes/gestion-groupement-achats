<?php
session_start();
require("fonctions.php");
verification_identification();
verification_admin();


if (isset($_POST['oui']))
{
	mysqli_query($link, "delete from membres where ID=".$_POST['ID'].";");
	header("Location: $BASE_URL/gestion_adherent.php");
}
elseif (isset($_POST['non']))
	header("Location: $BASE_URL/gestion_adherent.php");

?>

<html> 
<head> 
<title>Suppression d'un membre
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<font color='red'> Vous allez supprimer le membre : </font><p>

<?php
$requete = mysqli_query($link, "select nom_complet from membres where ID=".$_POST['ID'].";");
$resultat = mysqli_fetch_array($requete);
echo $resultat["nom_complet"];
?>
<p>
<font color='red'> Souhaitez vous poursuivre ? </font><p>

<form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>' method="post">
	<fieldset> 	
		<input type="hidden" name="ID" value="<?php echo $_POST['ID']; ?>">
		<input class="bouton" type="submit" name="oui" value="Oui" />        
		<input class="bouton" type="submit" name="non" value="Non" />        
	</fieldset>    
</form>  


</div>
</body>
</html>
