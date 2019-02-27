<?php
session_start();
require("fonctions.php");
verification_identification();

if (isset($_POST['modif_infos']))
{
	$ID=$_POST['ID'];
	$nom_complet=$_POST['nom_complet'];
	$email=$_POST['email'];
	$mail_second=$_POST['mail_second'];
	$adresse=$_POST['adresse'];
	$telephone=$_POST['telephone'];
 	if (isset($_POST['util_adresse_extra']))
  		$util_adresse_extra=1;
    	else
     		$util_adresse_extra=0;
 
	if (empty($nom_complet) or empty($email))
	{
		echo "<font color='red'> Tous les champs doivent être remplis !</font>\n";
	}
	else
	{
		mysqli_query($link, "update membres set nom_complet='".gestion_apostrophe($nom_complet)."', email='".gestion_apostrophe($email)."', mail_second='".gestion_apostrophe($mail_second)."', adresse='".gestion_apostrophe($adresse)."', telephone='$telephone',util_adresse_info_extra_groupement=$util_adresse_extra  where ID=$ID;");
		//header("Location: $BASE_URL/infos.php");
	}
}
elseif (isset($_POST['modif_mdp']))
{
	$ID=$_POST['ID'];
	$nom_complet=$_POST['nom_complet'];
	$email=$_POST['email'];
	$mail_second=$_POST['mail_second'];
	$statut=$_POST['statut'];
	$motdepasse=$_POST['motdepasse'];
	$adresse=$_POST['adresse'];
	$telephone=$_POST['telephone'];
	if (isset($_POST['util_adresse_extra']))
  		$util_adresse_extra=1;
    	else
     		$util_adresse_extra=0;
 
	if (empty($motdepasse))
	{
		echo "<font color='red'> Le mot de passe ne doit pas être vide !</font>\n";
	}
	else
	{
		mysqli_query($link, "update membres set motdepasse=md5('".($motdepasse)."') where ID=$ID;");
		echo "<font color=green> Modification effectuee. </font>\n<p><p>\n\n";
		//header("Location: $BASE_URL/infos.php");
	}
}
else
{
	$ID = $_POST['ID'];
	$requete = mysqli_query($link, "select * from membres where ID=$ID;");
	$membre = mysqli_fetch_array($requete);
	$nom_complet=$membre['nom_complet'];
	$email=$membre['email'];
	$mail_second=$membre['mail_second'];
	$motdepasse=$membre['motdepasse'];
	$statut=$membre['statut'];
	$adresse=$membre['adresse'];
	$telephone=$membre['telephone'];
 	$util_adresse_extra=$membre['util_adresse_info_extra_groupement'];

}
?>

<html> 
<head> 
<title>Modification des informations personnelles
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<H2>Informations personnelles
</H2>

<form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>' method='post'>
<input type='hidden' name='modif_infos' value='1'>
<input type='hidden' name='ID' value='<?php echo $ID; ?>'>
<input type='hidden' name='statut' value='<?php echo $statut; ?>'>
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
				<input type='text' name='nom_complet' maxlength='100' size='30' value="<?php echo $nom_complet; ?>">
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='email' maxlength='100' size='30' value="<?php echo "$email"; ?>">
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
		</tr>
	</thead>
	<tbody valign="middle">
		<tr valign="middle">
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='adresse' maxlength='300' size='75' value="<?php echo $adresse; ?>">
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='telephone' maxlength='30' size='15' value="<?php echo $telephone; ?>">
			</td>	
		</tr>
	</tbody>
</table>
J'accepte que mon adresse soit utilisée pour des informations extérieure au groupement <input type='checkbox' name='util_adresse_extra' value=1 <?php if ($util_adresse_extra) echo 'checked';?>><p>
<input class='bouton' type='submit' value='Valider'>
</form>

<H3> Mot de passe </H3>
<form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>' method='post'>
<input type='hidden' name='modif_mdp' value='1'>
<input type='hidden' name='ID' value='<?php echo $ID; ?>'>
<input type='hidden' name='nom_complet' value='<?php echo $nom_complet; ?>'>
<input type='hidden' name='email' value='<?php echo $email; ?>'>
<input type='hidden' name='statut' value='<?php echo $statut; ?>'>
Entrer un nouveau mot de passe :
<input type='password' name='motdepasse' maxlength='200' size='25'>
<input class='bouton' type='submit' value='Valider'>
</form>

</div>
</body>
</html>	
