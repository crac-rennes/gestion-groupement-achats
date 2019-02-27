<?php
session_start();
require("fonctions.php");
verification_identification();
verification_admin();


if (isset($_POST['ajout_valide']))
{
	$full_name=$_POST['nom_complet'];
	$courriel=$_POST['email'];
	$mail_second=$_POST['mail_second'];
	$mot_de_passe=$_POST['motdepasse'];
	if (empty($full_name) or empty($courriel) or empty($mot_de_passe))
	{
		echo "<font color='red'> Les champs nom, email et mot de passe doivent être remplis !</font>\n";
	}
	else
	{
		mysqli_query($link, "insert into membres (nom_complet,email,mail_second,motdepasse,statut,adresse,telephone,util_adresse_info_extra_groupement) values ('".gestion_apostrophe($_POST['nom_complet'])."','".gestion_apostrophe($_POST['email'])."','".gestion_apostrophe($_POST['mail_second'])."','".md5($_POST['motdepasse'])."','0','".gestion_apostrophe($_POST['adresse'])."','".$_POST['telephone']."',1);");
		header("Location: $BASE_URL/gestion_adherent.php");
	}

}
else
{
	$full_name='';
	$courriel='';
	$mail_second='';
	$mot_de_passe='';
	$privileges=0;
	$telephone='';
	$adresse='';
}
?>

<html> 
<head> 
<title>Ajout d'un membre
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  

<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<H2>Ajout d'un membre</H2>

<form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>' method='post'>

<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Nom</th>
			<th colspan="1">Email</th>
			<th colspan="1">Mails secondaires</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<tr valign="middle">
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='nom_complet' maxlength='100' size='25' value="<?php echo $full_name; ?>">
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='email' maxlength='100' size='25' value="<?php echo $courriel; ?>">
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='mail_second' maxlength='200' size='25' value="<?php echo $mail_second; ?>">
			</td>
		</tr>
	</tbody>
</table>

<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Adresse</th>
			<th colspan="1">Téléphone</th>
			<th colspan="1">Mot de passe</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<tr valign="middle">
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='adresse' maxlength='300' size='70' value="<?php echo $adresse; ?>">
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='telephone' maxlength='30' size='15' value=" ">
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='password' name='motdepasse' maxlength='200' value='<?php echo $mot_de_passe; ?>'>
			</td>
		</tr>
	</tbody>
</table>

<input class='bouton' type='submit' name='ajout_valide' value='Valider'>
</form>

</div>
</body>
</html>	
