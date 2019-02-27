<?php  
// Démarrage ou restauration de la session  
session_start();  
// Réinitialisation du tableau de session  
// On le vide intégralement  
$_SESSION = array();  
// Si vous voulez détruire complètement la session, effacez également
// le cookie de session.
// Note : cela détruira la session et pas seulement les données de session !
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}
// Destruction de la session  
session_destroy();  
// Destruction du tableau de session  
unset($_SESSION);
// Redirection vers la page d'accueil
header("Location: $BASE_URL/index.php");        
exit();
?>
