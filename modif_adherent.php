<?php
session_start();
require("fonctions.php");
verification_identification();
verification_admin();

if (isset($_POST['modif_infos']))
{
	$identifiant=$_POST['ID'];
	$full_name=$_POST['nom_complet'];
	$courriel=$_POST['email'];
	$mail_second=$_POST['mail_second'];
	# Drapeau pour les responsables de commission
	$privileges=0;
 	foreach ($nom_commission as $id => $nom_com)
	{
		if ($_POST["commission_".$id])
			$privileges += pow(2,$id-1);
	}
	# Cas de l'administrateur du site
	if ($_POST['admin'])
		$privileges = pow(2,10);
	# Cas du membre associé (ne commande pas)
	if ($_POST['associe'])
		$privileges = pow(2,12);
	$adresse=$_POST['adresse'];
	$telephone=$_POST['telephone'];
	if (empty($full_name) or empty($courriel))
	{
		echo "<font color='red'> Tous les champs doivent être remplis !</font>\n";
	}
	else
	{
		mysqli_query($link, "update membres set nom_complet='".gestion_apostrophe($full_name)."', email='".gestion_apostrophe($courriel)."', mail_second='".gestion_apostrophe($mail_second)."', statut='$privileges', adresse='".gestion_apostrophe($adresse)."', telephone='$telephone' where ID=$identifiant;");
		header("Location: $BASE_URL/gestion_adherent.php");
	}
}
elseif (isset($_POST['modif_mdp']))
{
	$identifiant=$_POST['ID'];
	$mot_de_passe=$_POST['motdepasse'];
	if (empty($mot_de_passe))
	{
		echo "<font color='red'> Le mot de passe ne doit pas être vide !</font>\n";
		$requete = mysqli_query($link, "select * from membres where ID=$identifiant;");
		$membre = mysqli_fetch_array($requete);
		$full_name=$membre['nom_complet'];
		$courriel=$membre['email'];
		$mail_second=$membre['mail_second'];
		$mot_de_passe='';
		$privileges=$membre['statut'];
		$telephone=$membre['telephone'];
		$adresse=$membre['adresse'];
	}
	else
	{
		mysqli_query($link, "update membres set motdepasse=md5('".($mot_de_passe)."') where ID=$identifiant;");
		header("Location: $BASE_URL/gestion_adherent.php");
	}
}
else
{
	// Première fois sur la page :
	// Récupération des informations sur le membre
	$identifiant = $_POST['ID'];
	$requete = mysqli_query($link, "select * from membres where ID=$identifiant;");
	$membre = mysqli_fetch_array($requete);
	$full_name=$membre['nom_complet'];
	$courriel=$membre['email'];
	$mail_second=$membre['mail_second'];
	$mot_de_passe='';
	$privileges=$membre['statut'];
	$telephone=$membre['telephone'];
	$adresse=$membre['adresse'];
}
?>

<html> 
<head> 
<title>Modification d'un membre
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  
	
<body> 
<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">

<H2>Modification d'un membre
</H2>

<H3> Modifier les informations relatives à ce membre </H3>

<form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>' method='post'>
<input type='hidden' name='modif_infos' value='1'>
<input type='hidden' name='ID' value='<?php echo $identifiant; ?>'>
<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Nom</th>
			<th colspan="1">Statut</th>
		</tr>
	</thead>
	<tbody valign="middle">
		<tr valign="middle">
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='nom_complet' maxlength='100' size='50' value="<?php echo $full_name; ?>">
			</td>
			<td colspan="1" rowspan="1" align="left">
				<?php
                 
 				foreach ($nom_commission as $id => $nom_com)
     				{
					if (is_resp_comm($id,$privileges))
      					echo "<input type='checkbox' name='commission_".$id."' value=1 checked>Resp commission ".$nom_com."<p>";
            		else
                		echo "<input type='checkbox' name='commission_".$id."' value=1>Resp commission ".$nom_com."<p>";
                    }
                        		
				if ( is_admin($privileges) ) 
    				echo "<input type='checkbox' name='admin' value=1 checked>Administrateur<p>";
         		else
             		echo "<input type='checkbox' name='admin' value=1>Administrateur<p>";
				
				if ( is_associe($privileges) ) 
    				echo "<input type='checkbox' name='associe' value=1 checked>Membre associé (annule toute sélection ci-dessus)<p>";
         		else
             		echo "<input type='checkbox' name='associe' value=1>Membre associé (annule toute sélection ci-dessus)<p>";
				?>
			</td>
	</tbody>
</table>

<table border="2">
	<thead valign="middle">
		<tr>
			<th colspan="1">Email</th>
			<th colspan="1">Mails secondaires</th>
			<th colspan="1">Adresse</th>
			<th colspan="1">Téléphone</th>
		</tr>
		</tr>
	</thead>
	<tbody valign="middle">
		<tr valign="middle">
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='email' maxlength='100' size='25' value="<?php echo $courriel; ?>">
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='mail_second' maxlength='200' size='25' value="<?php echo $mail_second; ?>">
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='adresse' maxlength='300' size='70' value="<?php echo $adresse; ?>">
			</td>
			<td colspan="1" rowspan="1" align="left">
				<input type='text' name='telephone' maxlength='30' size='15' value="<?php echo $telephone; ?>">
			</td>
		</tr>
	</tbody>
</table>


<input class='bouton' type='submit' value='Valider'>
</form>

<H3> Modifier son mot de passe </H3>
<form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>' method='post'>
<input type='hidden' name='modif_mdp' value='1'>
<input type='hidden' name='ID' value='<?php echo $identifiant; ?>'>
Entrer un nouveau mot de passe :
<input type='password' name='motdepasse' maxlength='200' size='25' value=''>
<input class='bouton' type='submit' value='Valider'>
</form>

</div>
</body>
</html>	
