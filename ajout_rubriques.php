<?php
session_start();
require("fonctions.php");
verification_identification();
verification_simple_membre();
	
if (isset($_POST['rubriques_nom']))
{
	mysqli_query($link, "insert into rubriques (rubriques_nom) values ('".gestion_apostrophe($_POST['rubriques_nom'])."');");
	header("Location: $BASE_URL/gestion_rubriques.php");
}
?>

<html> 
<head> 
<title>Ajout d'une rubrique
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  

<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<H2>Ajout d'une rubrique</H2>

<form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>' method='post'>
<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Nom de la rubrique</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<tr valign="middle">
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='rubriques_nom' maxlength='100'>
			</td>
		</tr>
	</tbody>
</table>
<input class='bouton' type='submit' value='Valider'>
</form>

</div>
</body>
</html>	
