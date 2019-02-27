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
<H3>Responsable de commission : comment faire ??</H3>
Votre menu (à gauche) contient plus d'options que celui d'un membre "classique" :
<ul>
	<li><a href="#gestion_produits" title="Gestion des produits">Gestion des produits</a></li>
	<li><a href="#gestion_commande" title="Gestion de la commande">Gestion de la commande</a></li>
	<li><a href="#gestion_fournisseurs" title="Gestion des fournisseurs">Gestion des fournisseurs</a></li>
	<li><a href="#gestion_rubriques" title="Gestion des rubriques">Gestion des rubriques</a></li>

</ul>
</div>

<div id="gestion_produits">
<H3> Gestion des produits</H3>
Cette page permet d'ajouter, supprimer, modifier les produits proposés par la commission.<p>
Description des différentes éléments de description du produit :
<ul>
	<li><b>Nom</b> : identifiant du produit (le plus explicite possible, sans être trop long)</li>
	<li><b>Fournisseur</b> : à choisir parmi les fournisseurs déjà renseignés (voir <a href="#gestion_fournisseurs">Gestion des fournisseurs</a>)</li>
	<li><b>Rubriques</b> : à choisir parmi les rubriques déjà renseignées (voir <a href="#gestion_rubriques">Gestion des fournisseurs</a>)</li>
	<li><b>Vrac ou lot</b> : indique si le produit est acheté en lot ou en vrac et réparti entre les membres. Dans ce cas, le produit est soumis aux ajustements. Il faudra alors indiquer lors de l'ajout ou de la modification du produit, le nombre d'UDV dans le conditionnement (ex : pour un sac de farine de 25 kg, si l'UDV est le kg, on indiquera 25 UDV/conditionnement)</li>
	<li><b>Conditionnement</b> : texte indiquant sous quelle forme est livré le produit</li>
	<li><b>Unité de vente (UDV)</b> : forme sous laquelle le produit est proposé aux membres</li>
	<li><b>Prix comparable</b> : prix au litre, au kg, ... pour comparer. A calculer "à la main"</li>
	<li><b>Prix de l'UDV</b> : pas besoin d'expliquer, non ?</li>
	<li><b>Actif</b> : il est possible desactiver des produits pour qu'il n'apparaissent pas dans le bon de commande. Utile en cas de rupture chez le fournisseur pour ne pas avoir à entrer le produit une nouvelle fois.</li>
</ul>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>
</div>

<div id="gestion_commande">
<H3> Gestion de la commande</H3>
Cette page propose différents outils pour simplifier le travail de préparation de la commande : 
<ul>
	<li><a href="#figer_vider" title="Bloquer/Débloquer le bon de commande et le vider">Bloquer/Débloquer le bon de commande et le vider</a></li>
	<li><a href="#fournisseur" title="Coté fournisseur">Gestion de la commande du coté fournisseurs</a></li>
	<li><a href="#ajustements" title="Gestion des ajustements">Gestion des ajustements</a></li>
	<li><a href="#preparation_livraison" title="Préparation de la livraison">Préparation de la livraison</a></li>
	<li><a href="#infos_commission" title="Informations de la commission">Informations de la commission</a></li>
</ul>
</div>

<div id="figer_vider">
	<H4>Bloquer/Débloquer le bon de commande et le vider</H4>
	Le bouton de gauche permet de bloquer/débloquer le bon de commande. Lorsque le bon de commande est bloqué, les membres ne peuvent que consulter leur commande, mais ne 	peuvent plus la modifier.<p>
	Le bouton de droite permet de vider la base de donnée de toutes les commandes passées. A n'utiliser qu'une fois la livraison au famille effectuée.
</div>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>

<div id="fournisseur">
	<H4>Gestion de la commande du coté fournisseurs</H4>
	Permet de sortir le <u>bon de commande par fournisseur</u>. Une version PDF dans laquelle le nom de la personne gérant la commande peut être intégré est également disponible.<p>
	Le <u>bilan de la commande</u> par fournisseur donne le montant par fournisseur et le montant total des commandes (TTC).
</div>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>

<div id="ajustements">
	<H4>Gestion des ajustements</H4>
	La première ligne permet de voir les commandes concernant un produit particulier. (voir <a href="#bon_commande_produit">ci-dessous</a>)<p>
	La deuxième ligne propose la même chose mais la liste ne contient que des produits pour lesquels l'ajustement n'a pas été fait. <p>
	La dernière ligne permet de lancer un ajustement automatique (voir <a href="#bon_commande_produit">ci-dessous</a>) sur tous les produits pour lesquels il est possible d'ajuster automatiquement.<p>
</div>

<div id="bon_commande_produit">
	<b>Bon de commande produit</b><p>
	Le bon de commande produit détaille les quantités commandées. C'est notamment utile pour préparer la livraison dans le cas de produits en vrac (mais une procédure automatique existe, voir <a href="#preparation_livraison" title="Préparation de la livraison">Préparation de la livraison</a>)<p>
	Si le produit est ajustable (vrac ou lot), il est précisé si compte-tenu des quantités commandées et des ajustements, il est possible d'obtenir un nombre entier de conditionnements. Selon le cas, un bouton "ajustement automatique" est présent ou non. <p>
	L'<u>ajustement automatique</u> réparti les ajustements entre les différents candidats, au hasard. <p>
	L'<u>ajustement manuel</u> permet de modifier les quantités commandés, ou d'ajouter de nouvelles commande pour le produit en question, de façon à permettre malgré tout de commander ce produit. <p>
	Le <u>basculement</u> permet de transférer les commandes depuis un produit vers un autre produit. En fonction des UDV, il peut être nécessaire de multiplier ou diviser les quantités commandées (ex : basculement de commande de sac de 5kg de farine vers du vrac en 25kg, il faut multiplier les quantités commandées (en sac) par 5).
	Enfin, il est possible de sortir chaque bon de commande produit en version <u>PDF</u>.
</div>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>

<div id="preparation_livraison">
	<H4>Préparation de la livraison aux familles</H4>
	<u>Détail des commandes pour tous les produits en vrac :</u> sort les quantités commandés par les différents membres pour tous les produits en vrac (pour la préparation des paquets). <p>
	<u>Bon de commande par famille :</u> récapitule les commandes de chaque famille. Disponible en PDF pour impression en vue de la préparation de la commande propre à chaque famille.<p>
	<u>Montant de la commande par famille :</u> juste la somme à payer par famille, pour faire les comptes. <p>
	<u>Estimation du travail de préparation :</u> estimation du temps nécessaire à la préparation de la commande et des besoins en sacs. <p>
</div>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>

<div id="infos_commission">
	<H4>Informations de la commission</H4>
	Il s'agit du message qui s'affiche sur la page d'accueil du site et propre à chaque commission.
</div>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>

<div id="gestion_fournisseurs">
	<H3> Gestion des fournisseurs</H3>
	Permet l'ajout, la suppression, la modification des fournisseurs du groupement. <p>
	<b> N.B.</b> : cette page est commune à toutes les commissions.<p>
	On renseigne également un certain nombre d'information utiles pour contacter nos fournisseurs.
</div>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>


<div id="gestion_rubriques">
	<H3> Gestion des rubriques</H3>
	Permet l'ajout, la suppression, la modification des rubriques. <p>
	Les rubriques permettent une subdivision de la commande suivant le type de produits. Exemple : farine, huile, légumes secs, ...
</div>
<a href="#sommaire" title="Retour en haut de la page"><small>Retour en haut de la page</small></a>

</body> 
</html>
