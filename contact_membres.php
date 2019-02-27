<?php
session_start();
require("fonctions.php");
verification_identification();

// Récupération de la liste des adresses
?>

<html> 
<head> 
<title>Contacter les membres du groupement
</title> 
<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >

<SCRIPT LANGUAGE="Javascript"> 

function getXhr()
	{
	var xhr = null;
	if(window.XMLHttpRequest) // Firefox et autres
		{
		xhr = new XMLHttpRequest();
		//alert("Firefox");
		}
	else if(window.ActiveXObject)
		{ // Internet Explorer
		try 
			{
			xhr = new ActiveXObject("Msxml2.XMLHTTP");
			} 
		catch (e) 
			{
			xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
		}
	else 
		{ // XMLHttpRequest non supporté par le navigateur
		alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
		xhr = false;
		}
	return xhr;
	}

// Node cleaner
function go(c)
{
	if(!c.data.replace(/\s/g,''))
	c.parentNode.removeChild(c);
}

function clean(d)
{
	var bal=d.getElementsByTagName('*');
	for(i=0;i<bal.length;i++)
		{
		a=bal[i].previousSibling;
		if(a && a.nodeType==3)
			go(a);
		b=bal[i].nextSibling;
		if(b && b.nodeType==3)
			go(b);
		}
	return d;
}

/// Méthode appliquée pour le choix des destinataire pour les "simples membres"


function selection_destinataire() 
{ 
//alert("toto");

var xhr = getXhr();

xhr.onreadystatechange = function()
	{
	// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
	if(xhr.readyState == 4 && xhr.status == 200)
		{		
		//leselect = xhr.responseText;
		//alert(leselect);

		xmlDoc = clean(xhr.responseXML.documentElement);
		var commission=xmlDoc.getElementsByTagName('commission')[0].firstChild.nodeValue;
		if (commission==0)
			{
			var liste_ID=xmlDoc.getElementsByTagName('id');
			var nb_membre=liste_ID.length;
			var ii;
			for (ii=0;ii<nb_membre;ii++)
				{
				//alert(liste_ID[ii].firstChild.nodeValue);
				checkbox = document.getElementById(liste_ID[ii].firstChild.nodeValue);
				checkbox.checked=false;
				}
			//alert("coucou");
			}
		else
			{
			var liste_ID=xmlDoc.getElementsByTagName('id');
			var nb_membre=liste_ID.length;
			var ii;
			for (ii=0;ii<nb_membre;ii++)
				{
				checkbox = document.getElementById(liste_ID[ii].firstChild.nodeValue);
				checkbox.checked=true;
				}
			}
		}
	else
		{
		return;
		}
	}

xhr.open("POST","recup_liste_membre_commission.php",true);
xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
selection = document.getElementById('choix_destin');
xhr.send("commission="+selection.value);

} 




// Méthode qui sera appelée sur le click du bouton


function selection_fournisseur(num_comm)
{
var xhr = getXhr();
xhr.onreadystatechange = function()
	{
	// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
	if(xhr.readyState == 4 && xhr.status == 200)
		{		
		
		leselect = xhr.responseText;
		//alert(leselect);

		xmlDoc = clean(xhr.responseXML.documentElement);
		//xmlDoc=xhr.responseXML;

		fourn=xmlDoc.getElementsByTagName("fournisseur");

		if (fourn[0].firstChild.nodeValue==0)
			{
			insertion=" ";
			}
		else
			{
			var id=xmlDoc.getElementsByTagName("id");
			if (id.length>0)
			{
				var nom=xmlDoc.getElementsByTagName("nom");
				var cond=xmlDoc.getElementsByTagName("cond");
				//alert(id.length);
		
				insertion="Produit : <select id='prod_"+fourn[0].firstChild.nodeValue+"' onChange=selection_produit("+fourn[0].firstChild.nodeValue+","+num_comm+") >";
				insertion += "<option value='0'>Sélectionner un produit</option>\n";
				insertion += "<option value='-1'>Tous</option>\n";
				for (ii=0;ii<id.length;ii++)
					insertion += "<option value='"+id[ii].firstChild.nodeValue+"'>"+nom[ii].firstChild.nodeValue+" ("+cond[ii].firstChild.nodeValue+")</option>\n";
				insertion += "</select>";
				//alert(insertion);
			}
			else
			{
					insertion="Aucune commande chez ce fournisseur.";
			}
		}
		document.getElementById('div_produit_'+num_comm).innerHTML = insertion;
		//alert(message[0].childNodes[1].nodeValue);
		
		}
	else
		{
		return;
		}
	}
sel = document.getElementById('com_'+num_comm);
idfournisseur = sel.options[sel.selectedIndex].value;
var params = "commission="+num_comm+"&idfourn="+idfournisseur;
//alert(params);
xhr.open("POST","recup_liste_produits.php",true);
xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
//http.setRequestHeader("Content-length", params.length);
//http.setRequestHeader("Connection", "close");
xhr.send(params);
} 


// Fonction pour la sélection des membres ayant commandé un produit particulier

function selection_produit(id_fourn,comm) 
{ 
var xhr = getXhr();
//alert(id_fourn);
selection = document.getElementById('prod_'+id_fourn).value;
//alert(selection);

xhr.onreadystatechange = function()
	{
	// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
	if(xhr.readyState == 4 && xhr.status == 200)
		{		
		//leselect = xhr.responseText;
		//alert(leselect);
		
		xmlDoc = clean(xhr.responseXML.documentElement);
		var liste_ID=xmlDoc.getElementsByTagName('id');
		var nb_membre=liste_ID.length;
		//alert(nb_membre);
		var ii;
		for (ii=0;ii<nb_membre;ii++)
			{
			checkbox = document.getElementById(liste_ID[ii].firstChild.nodeValue);
			//checkbox = document.getElementById(1);
			checkbox.checked=true;
			//alert(liste_ID[ii].firstChild.nodeValue);
			}
		}
	else
		{
		//alert(xhr.readyState+" "+xhr.status);
		return;
		}

	}


var params = "produit="+selection+"&fournisseur="+id_fourn+"&commission="+comm;
//alert("params="+params);
xhr.open("POST","recup_commande_produits.php",true);
xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
//http.setRequestHeader("Content-length", params.length);
//http.setRequestHeader("Connection", "close");
xhr.send(params);
} 

function validateEmail(elementValue){
   var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
   return emailPattern.test(elementValue);
 }


function go_mail(cache)
{
	var xhr3 = getXhr();


	xhr3.onreadystatechange = function()
		{
		// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
		if(xhr3.readyState == 4 && xhr3.status == 200)
			{

//leselect = xhr3.responseText;
//alert(leselect);
//alert(this.getResponseHeader("Content-Type"));
			
			xmlDoc = clean(xhr3.responseXML.documentElement);
//alert(xhr3.responseXML.xml);
			var liste_ID=xmlDoc.getElementsByTagName('id');
////alert(liste_ID);
			var liste_mail=xmlDoc.getElementsByTagName('mail');
//alert(liste_mail);
			var nb_membre=liste_ID.length;
//alert(nb_membre);
//alert(liste_mail[0].firstChild.nodeValue);
			var nb_emails_coches=0;
			if (nb_membre != 0)
			{
				destinataire="";
				var flag=0;
				
				// Boucle sur tous les membres
				for (ii=0;ii<nb_membre;ii++)
				{
					checkbox = document.getElementById(liste_ID[ii].firstChild.nodeValue);
					adresses_electroniques=liste_mail[ii].firstChild.nodeValue; // adresses_electroniques contient les toutes les adresses de la famille
//alert(adresses_electroniques);
					if ( checkbox.checked==true )
					{
						tab=adresses_electroniques.split(",");	// Séparation des différentes adresses
//alert(tab);						
						for (jj=0;jj<tab.length;jj++)		// boucle pour ajouter chacune des adresses
						{
//alert(tab.length+" "+tab[jj]);
							
							if (validateEmail(tab[jj]))		// email valide ??
							{
								nb_emails_coches++;
								if (flag)
									destinataire += ", ";
								else
									flag=1;
								destinataire += tab[jj];
							}
							else
							{
								alert("Adresse non valide :"+tab[jj]);
							}
						}
					}
					
				}
//alert(destinataire);
				if (nb_emails_coches>0)
				{
					if (cache==1)
						window.open('mailto:?subject=[Groupement d\'achats]&bcc='+destinataire+'');
					else
					{
						if (cache==0)
							window.open('mailto:'+destinataire+'?subject=[Groupement d\'achats]');
						if (cache==2)
							document.getElementById('liste_email').innerHTML="<table><tr><td width='90%' >Vous pouvez copier-coller la liste suivante dans les destinataires de votre courriel :<br><br>"+destinataire+"</td></tr></table>";
							//alert("Vous pouvez copier-coller la liste suivante dans les destinataires de votre courriel :\n\n"+destinataire);
					}
				}	
				else
				{
					alert("Sélectionner les adresses à utiliser (manuellement ou en fonction des fournisseurs et produits)");
					document.getElementById('liste_email').innerHTML="";
				}
			}
		}
		else
		{
			return;
		}
	}


//liste_test=document.getElementByTagName("input");
//var nb_test=liste_test.length;
//alert(nb_test);

	xhr3.open("GET","recup_adresse_pour_envoi_mail.php",true);
	xhr3.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	xhr3.send(null);
}

</SCRIPT>

<link rel="icon" type="image/png" href="logo.png" />

</head>  

<body>

<div id="menu">
	<?php include("menu.php");?>
</div>
<div id="main">
		
	<H2>Contacter les membres du groupement</H2>

	<form>
	<table style="border:0px">
	<tr><td style="border:0px">
	<select name="choix_destin" id="choix_destin" onChange=selection_destinataire()>
		<option selected value=0>Aucun</option>
		<option value=11>Tous</option>
		<option value=12>Tous (pour une information extérieure au groupement)</option>
		<?php
			//for ($num_comm=1;$num_comm<=$nb_commissions;$num_comm++)
				//{
				//echo "<option value=$num_comm>Commission $nom_commission[$num_comm]</option>";
				//}
			for ($i=1;$i<=$nb_commissions;$i++)
			{
				$id_commission=$tab_commission[$i];
				echo "<option value=$id_commission>Commission $nom_commission[$id_commission]</option>";
			}
		?>
	</select>
	</td>
	<td width='200' style="border:0px"></td><td style="border:0px;" width="100">
	<A href="javascript: go_mail(0)">Envoyer</A>
	</td><td style="border:0px">
	<A href="javascript: go_mail(1)">Envoyer (copie cachée)</A>
	</td><td style="border:0px">
	<A href="javascript: go_mail(2)">Afficher les adresses</A>
	</td></tr></table>
	<p>
	<div id="liste_email"></div>


	<?php
	// Pour les responsables de commission
	for ($num_comm=1;$num_comm<=$nb_commissions;$num_comm++)
		{
		if ( (is_resp_comm($num_comm,$_SESSION['statut'])) or (is_admin($_SESSION['statut'])))
			{
			echo "<H4>Commission $nom_commission[$num_comm]</H4><p>";
			echo "Sélectionner les membres ayant commandé<p>Fournisseur : ";
			echo "<select name='choix_destin_comm_$num_comm' id='com_$num_comm' onChange=selection_fournisseur($num_comm)>\n";
			//echo "<select name='choix_destin_comm_$num_comm' id='com_$num_comm' onChange=selection_destinataire()>\n";
			echo "<option selected value=0>Aucun</option>";
			$requete = mysqli_query($link, "select fournisseurs_ID,fournisseurs_nom from fournisseurs,produits where fournisseurs_ID=produits_fournisseur and produits_commission=$num_comm group by fournisseurs_ID order by fournisseurs_nom;");
			while(($resultat = mysqli_fetch_array($requete)))
				{
				$fournisseurs_ID=$resultat['fournisseurs_ID'];
				$fournisseurs_nom=$resultat['fournisseurs_nom'];
				echo "<option value=$fournisseurs_ID>$fournisseurs_nom</option>\n";
				}
			echo "</select>\n";

			echo "<div id='div_produit_$num_comm'></div>";
			}
		}
	?>

	<table border="2">
		<thead valign="middle">
			<tr>
				<th colspan="1"> </th>
				<th colspan="1">Nom</th>
				<th colspan="1">Statut</th>
				<th colspan="1">Email</th>
				<th colspan="1">Adresse</th>
				<th colspan="1">Téléphone</th>
			</tr>
		</thead>
		<tbody valign="middle">
			<?php
				// Remplissage du tableau
				$requete = mysqli_query($link, "select * from membres order by nom_complet;");
				while(($resultat = mysqli_fetch_array($requete)))
				{
					$nom_checkbox=$resultat['ID'];
					echo '<tr valign="middle">';
					echo '<td colspan="1" rowspan="1" align="center">';
					echo "<input type='checkbox' name='$nom_checkbox' id='$nom_checkbox' value=1>";
					echo '</td>';

					// Nom
					echo '<td colspan="1" rowspan="1" align="left">';
					echo $resultat["nom_complet"];
					echo '</td>';
					// Statut
					echo '<td colspan="1" rowspan="1" align="left">';
					if ($resultat["statut"]==0)
						echo "Membre";
					else if ( is_admin($resultat["statut"]) )
						echo "Administrateur";
						else
						{
							for ($i=1;$i<=$nb_commissions;$i++)
							{
								//echo $resultat['statut'];
								if ( is_resp_comm($i, $resultat['statut']) )
								{
									echo "Resp. $nom_commission[$i]<p>";
									}
								}
						}
					echo '</td>';
					//  Email
					echo '<td colspan="1" rowspan="1" align="left">';
					echo $resultat["email"];
					echo '</td>';
					//  Adresse
					echo '<td colspan="1" rowspan="1" align="left">';
					echo $resultat["adresse"];
					echo '</td>';
					//  Telephone
					echo '<td colspan="1" rowspan="1" align="left">';
					echo $resultat["telephone"];
					echo '</td>';
					echo "</tr>\n";
				}?>
		</tbody>
	</table>
	</form>
</div>
</body>
</html>

