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

<H3>Un coup de main pour passer votre commande</H3>
Vous n'avez qu'à remplir la colonne quantité et la colonne ajustement, si elle est disponible.
<b> N'oubliez pas</b>, ensuite de valider avec le bouton situé en bas de la page.
<p>Si tout s'est bien passé, vous devrier obtenir un récapitulatif de votre commande.
<H4>Ajustements</H4>
Une des raisons d'être du groupement est l'achat en gros. 
Pour que cela puisse fonctionner, il est nécessaire de pouvoir ajuster les commandes de chacuns 
pour pouvoir tomber sur un conditionnement complet. C'est pour cette raison que sur les produits
en lot ou en vrac, il est possible de renseigner une colonne ajustement.<p>
<b>Comment ça marche ?</b><p>
Vous indiquez dans cette colonne, jusqu'à quel point vous êtes prêt à accepter d'avoir plus ou moins
du produit en question.

</body> 
</html>
