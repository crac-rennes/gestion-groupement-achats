<?php
session_start();
require("fonctions.php");
//verification_identification();

$buffer = '<?xml version="1.0"?>';

$com=$_POST["commission"];
$fourn=$_POST["idfourn"];

$buffer .= "<reponse>";

$buffer .= "<fournisseur>".$fourn."</fournisseur>";
$requete = "select produits_ID,produits_nom, produits_conditionnement from produits where ( produits_fournisseur=$fourn and produits_actif=1 and produits_commission=$com) order by produits_nom;";

$res = mysqli_query($requete);

//$buffer .= $requete;

if (mysqli_num_rows($res)>0)
	{
	while($row = mysqli_fetch_assoc($res))
			{
			$buffer .= "<nom>".$row['produits_nom']."</nom>";
			$buffer .= "<id>".$row['produits_ID']."</id>";
			$buffer .= "<cond> ".$row['produits_conditionnement']."</cond>";
			}
	}

$buffer .= "</reponse>";

header('Content-Type: text/xml');
echo $buffer;

/*echo "<select name='livre'>";
if(isset($_POST["idAuteur"])){
mysql_connect("localhost","root","root");
mysqli_select_db("test");
$res = mysqli_query($link, "SELECT id,titre FROM livre
WHERE idAuteur=".$_POST["idAuteur"]." ORDER BY titre");
while($row = mysqli_fetch_assoc($res)){
echo "<option value='".$row["id"]."'>".$row["titre"]."</option>";
}
}
echo "</select>";
*/

?>
