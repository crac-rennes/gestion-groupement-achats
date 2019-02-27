<?php
session_start();
require("fonctions.php");
verification_identification();
verification_admin();

if ((isset($_POST['Valider'])) && $_POST['requetes_sql']!="")
	{
	$nb_requetes=0;
	$ligne=0;
	$requete=strtok($_POST['requetes_sql'],"\n");
//	$retour=mysqli_query($requete);
//	$nb_requetes++;
	$retour=1;
	while (($requete !== false) && ($retour))
		{
		//echo $requete."**".strlen($requete);
		if (strlen($requete)>1)
			{
			$retour=mysqli_query(stripslashes($requete));
//			echo stripslashes($requete);
			$nb_requetes++;
			}
		$requete = strtok("\n");
		$ligne++;
		}
	if ($retour)
		{
		echo "<font color='green'> $nb_requetes requetes effectuées avec succés. </font><p>\n";
		}
	else
		{
		echo "Erreur à la ligne $ligne.<p><p>\n\n";
		echo $retour;
		echo mysqli_errno();
		}
	}
	?>

<html>
<head> 
	<title>Restauration de tout ou partie de la base (interpreteur MySQL)
	</title> 
	<link rel="stylesheet" href="style.css">

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  <body>
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">


Insérer ici les commandes SQL.

<form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>' method='post'>
	<textarea rows='20' cols='120' name='requetes_sql'></textarea>
	<p>
	<input name='Valider' type='submit' value='Valider'>
</form>


</div>
</body>
</html>
