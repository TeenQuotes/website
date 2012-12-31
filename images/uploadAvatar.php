<?php 
require "../kernel/config.php";
echo "Upload:";
//Upload
$uploaddir = './avatar/';
$file = basename($_FILES['userfile']['name']);
$uploadfile = $uploaddir . $file;
echo $uploadfile."<br/>";
$resultat = move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);
if ($resultat) 
	echo "Transfert réussi";
else
	echo "Transfert raté";

echo "<br/>";

//Modification de l'image dans la bdd
$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($user, $db)  or die('Erreur de selection '.mysql_error()); 

$liste = explode(".", $file);
$identifiant = intval($liste[0]);

$requete = "UPDATE teen_quotes_account SET avatar = \"".htmlspecialchars(mysql_real_escape_string($file))."\" WHERE id= ".$identifiant;
echo "<br/>".$requete."<br/>";
$update = mysql_query($requete);

if($update)
	echo "Update réussi";
else
	echo "Update raté";