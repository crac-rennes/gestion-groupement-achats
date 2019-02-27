<?php
session_start();
require("fonctions.php");
verification_identification();

$requete=mysqli_query($link,"select commissions_infos,commissions_nom from commissions where commissions_infos!='';");
?>

<html> 
<head> 
<title>Informations
</title> 
<link rel="stylesheet" href="style.css">
<script type="text/javascript" language="javascript" src="fonction.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  

<body>
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">
<?php 
if (mysqli_num_rows($requete)!=0)
{
	echo "<H2>Infos commande</H2>\n";
	
	while(($resultat = mysqli_fetch_array($requete)))
	{
		if (($resultat['commissions_infos']!=NULL) and ($resultat['commissions_infos']!=''))
		{
			echo "<H3> Commission ".$resultat['commissions_nom']."</H3>\n\n";
			echo "<p><p>\n";
			echo str_replace("\n","<p>",$resultat['commissions_infos']);
			echo "\n<p><p>";
		}
	}
}

?>

</div>
</body>
</html>	
