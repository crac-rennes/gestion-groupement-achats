<?php
session_start();
require("fonctions.php");
verification_identification();
?>

<html> 
<head> 
<title>Aide à la commande</title> 

<link rel="stylesheet" href="style.css">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head> 

<body>

<div id="sommaire">
<H3>Aide pour les principales tâches du responsable de commission</H3>
<ul>
	<li><a href="#liste_produits">Obtenir la liste des produits d'un fournisseur</a></li>
	<li><a href="#prix_produit">Mettre à jour le prix d'un produit</a></li>
	<li><a href="#activer">Activer/désactiver un produit</a></li>
	<li><a href="#liste_personne_produit">Liste des personnes ayant commandé un produit particulier</a></li>
	<li><a href="#bon_fournisseur">Obtenir le bon de commande d'un fournisseur</a></li>
	<li><a href="#bilan_famille">Montant + total des commandes de toutes les familles</a></li>
	<li><a href="#bilan_fournisseur">Montant + total des commandes pour tous les fournisseurs</a></li>
	<li><a href="#historique">Consultation de l’historique des commandes</a></li>

</ul>
</div>

<div id="liste_produits">
<H3>Obtenir la liste des produits d'un fournisseur</H3>
<table>
	<tr>
		<td align='center'><b>Rubrique<b></td>
		<td align='center'>Action</td>
	</tr>	
	<tr>
		<td>Gestion des produits</td>
		<td></td>
	</tr>
	<tr>
		<td>N'afficher que les produits d'un fournisseur</td>
		<td>Filtrer + choix du fournisseur</td>
	</tr>
</table>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>
</div>

<div id="prix_produit">
<H3>Mettre à jour le prix d'un produit</H3>
<table>
	<tr>
		<td align='center'><b>Rubrique<b></td>
		<td align='center'>Action</td>
	</tr>	
	<tr>
		<td>Gestion des produits</td>
		<td></td>
	</tr>
	<tr>
		<td>N'afficher que les produits d'un fournisseur</td>
		<td>Filtrer + choix du fournisseur</td>
	</tr>
	<tr>
		<td>Colonne modifier</td>
		<td>Mettre à jour : "prix" + "pour comparer". Valider</td>
	</tr>
</table>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>
</div>

<div id="activer">
<H3>Activer/désactiver un produit</H3>
<table>
	<tr>
		<td align='center'><b>Rubrique<b></td>
		<td align='center'>Action</td>
	</tr>	
	<tr>
		<td>Gestion des produits</td>
		<td></td>
	</tr>
	<tr>
		<td>N'afficher que les produits d'un fournisseur</td>
		<td>Filtrer + choix du fournisseur</td>
	</tr>
	<tr>
		<td>Colonne modifier</td>
		<td>Mettre à jour : "actif ?". Valider</td>
	</tr>
</table>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>
</div>


<div id="liste_personne_produit">
<H3>Liste des personnes ayant commandé un produit particulier</H3>
<table>
	<tr>
		<td align='center'><b>Rubrique<b></td>
		<td align='center'>Action</td>
	</tr>	
	<tr>
		<td>Gestion de la commande</td>
		<td></td>
	</tr>
	<tr>
		<td>Voir les commandes par produits / Gérer l'ajustement :<p>Choix du produit</td>
		<td>Choix produit</td>
	</tr>
	<tr>
		<td>Bon de commande produit</td>
		<td>Imprimer </td>
	</tr>
</table>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>
</div>


<div id="bon_fournisseur">
<H3>Obtenir le bon de commande d'un fournisseur</H3>
<table>
	<tr>
		<td align='center'><b>Rubrique<b></td>
		<td align='center'>Action</td>
	</tr>	
	<tr>
		<td>Gestion de la commande</td>
		<td></td>
	</tr>
	<tr>
		<td>Sortir le bon de commande pour un fournisseur: <p>Choix du fournisseur</td>
		<td>Choix fournisseur</td>
	</tr>
	<tr>
		<td>Bon de commande Fournisseur</td>
		<td>OK + Enregistrer une copie (dans le doc .pdf) </td>
	</tr>
</table>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>
</div>

<div id="bilan_famille">
<H3>Montant + total des commandes de toutes les familles</H3>
<table>
	<tr>
		<td align='center'><b>Rubrique<b></td>
		<td align='center'>Action</td>
	</tr>	
	<tr>
		<td>Gestion de la commande</td>
		<td></td>
	</tr>
	<tr>
		<td>Préparer la livraison aux familles <p>Montant total de la commande par famille </td>
		<td>Voir</td>
	</tr>
</table>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>
</div>

<div id="bilan_fournisseur">
<H3>Montant + total des commandes pour tous les fournisseurs</H3>
<table>
	<tr>
		<td align='center'><b>Rubrique<b></td>
		<td align='center'>Action</td>
	</tr>	
	<tr>
		<td>Gestion de la commande</td>
		<td></td>
	</tr>
	<tr>
		<td>Préparer la livraison aux familles <p>Bilan de la commande par fournisseur </td>
		<td>Voir</td>
	</tr>
</table>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>
</div>

<div id="historique">
<H3>Consultation de l’historique des commandes</H3>
<table>
	<tr>
		<td align='center'><b>Rubrique<b></td>
		<td align='center'>Action</td>
	</tr>	
	<tr>
		<td>Gestion de la commande</td>
		<td></td>
	</tr>
	<tr>
		<td>Historique les commandes</td>
		<td>Choix de la sauvegarde par date</td>
	</tr>
</table>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>
</div>

</body> 
</html>
