<?php  

require("fonctions.php");

// Test de l'envoi du formulaire  
if(!empty($_POST))   
{
	$errorMessage = '';   

	// Les identifiants sont transmis ?    
	if(!empty($_POST['login']) && !empty($_POST['password']))     
	{      
		$login=$_POST['login'];
		$requete = mysqli_query($link,"SELECT * FROM membres WHERE email='$login';");
		$resultat = mysqli_fetch_array($requete);
		$email = $resultat['email'];

		// Sont-ils les mêmes que les constantes ?      
		if(!$resultat)       
		{        
			//$errorMessage = 'Mauvais login ! ::'.$_POST['login']."  ".$_POST['password']."SELECT * FROM membres WHERE email='$login';";      
			$errorMessage = 'Mauvaise adresse !';
		}        
		else
		{
			
			$password=$resultat['motdepasse'];
			if(md5($_POST['password']) !== $password)       
			{          
				$errorMessage = 'Mauvais mot de passe !'; 
			} 
			else      
			{        
			//$errorMessage = $password;
				// On ouvre la session        
				session_start();
				
				// Problème avec le nom du cookie sur le serveur
				// Le même nom est conservé (pas aléatoire) : conflit quand plusieurs connexions simultanées ?
				// Ci-dessous : tests infructueux
				//session_name("sess_".random(10));
				//session_regenerate_id(TRUE);        
				// On enregistre le login en session        
				$_SESSION['email'] = $email;
				$_SESSION['nom_complet'] = $resultat['nom_complet'];
				$_SESSION['statut'] = $resultat['statut'];
				$_SESSION['gpt_ID']=$resultat['ID'];
				
				// On inscrit la date de derniere connexion dans la base
				mysqli_query($link, "update membres set derniere_connexion=concat(curdate(),' ',curtime()) where ID=".$resultat['ID'].";");
				
				header("Location: $BASE_URL/infos.php");        
				exit();
			}
		}    
	}      
	else    
	{      
		$errorMessage = 'Veuillez inscrire vos identifiants svp !';   
	}
}


?>


<html>  
<head>    
<title><?php echo $NOM_GROUPEMENT;?></title>
<link rel="stylesheet" href="style.css">
<script type="text/javascript" language="javascript" src="fonction.js"></script>
<link rel="icon" type="image/png" href="logo.png" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>  

<body onload="if (self != top) top.location = self.location">    
<center>
<p></p><p></p>
<H2>Site internet du <?php echo $NOM_GROUPEMENT; ?></H2><p></p>
<H4>Site réservé aux membres du groupement</H4><p></p>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
	<fieldset style="width: 400px;" align='center'>       
		<legend>Veuillez vous identifier</legend>
			<?php          
				// Rencontre-t-on une erreur ?          
				if(!empty($errorMessage))
				{            
					echo '<p>', htmlspecialchars($errorMessage) ,'</p>'; 
				}
			?>       
			<p>
			<label for="login">Adresse email :</label>        
				<input type="text" name="login" id="login" value="" /> 
			</p>        
			<p>          
			<label for="password">Mot de passe :</label>           
				<input type="password" name="password" id="password" value="" />        			</p>        
			<center>
				<input class="bouton" type="submit" name="submit" value="S'identifier" />        
			</center>
	</fieldset>    
</form>  
admin@gpt.com  // toto
<div style="width:350;align:center;">
<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/fr/"><img alt="Licence Creative Commons" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-sa/3.0/fr/88x31.png" /></a><br />Ce(tte) œuvre est mise à disposition selon les termes de la <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/fr/">Licence Creative Commons Attribution - Pas d’Utilisation Commerciale - Partage dans les Mêmes Conditions 3.0 France</a>.
<p>Site internet développé par Paul Leducq (paul point leducq arobase gmail point com)</p>
</div>

</center>
</body>
</html>

