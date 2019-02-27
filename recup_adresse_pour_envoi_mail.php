<?php
session_start();
require("fonctions.php");
//verification_identification();

$buffer = '<?xml version="1.0"?>';

$buffer .= "<reponse>";

$res = mysqli_query($link, "SELECT ID,email,mail_second FROM membres;");

while($row = mysqli_fetch_assoc($res))
		{
		$ID=$row['ID'];
		$buffer.= "<id>".$ID."</id>";
		if ($row['mail_second']!='')
			$buffer .= "<mail>".$row['email'].",".$row['mail_second']."</mail>";
		else
			$buffer .= "<mail>".$row['email']."</mail>";
		}

$buffer .= "</reponse>";

header('Content-Type: text/xml');
echo $buffer;

?>
