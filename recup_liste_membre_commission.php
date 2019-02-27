<?php
session_start();
require("fonctions.php");
//verification_identification();

$buffer = '<?xml version="1.0"?>';

$com=$_POST["commission"];

$buffer .= "<reponse>";


// Pour le XML
$buffer .= "<commission>".$com."</commission>";

if ( ($com==0) or ($com==11) )
	{
	$res = mysqli_query($link, "SELECT ID FROM membres;");
	}
else
	{
	if ($com==12)
		{
		$res = mysqli_query($link, "SELECT ID FROM membres where util_adresse_info_extra_groupement=1;");
		}
	else
		{
		$res = mysqli_query($link, "select ID from membres where (pow(2,$com-1)&statut=pow(2,$com-1));");
		}
	}

while($row = mysqli_fetch_assoc($res))
		{
		$ID=$row['ID'];
		$buffer.= "<id>".$ID."</id>";
		}

$buffer .= "</reponse>";

header('Content-Type: text/xml');
echo $buffer;

?>
