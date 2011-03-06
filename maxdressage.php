<?php
/* 
 * Vérification du max en dressage de chaque joueur
 */

if (file_exists('root.php'))
  include_once('root.php');

/**
*
* Permet l'affichage des informations d'une case en fonction du joueur.
*
*/
include_once(root.'inc/fp.php');

$requete = "SELECT id FROM perso ORDER BY id ASC";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$perso = new perso($row['id']);
	echo $perso->get_dressage();
	$requete = "SELECT * FROM classe_permet WHERE id_classe = ".$joueur->get_classe_id()." AND competence = 'dressage'";
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	if($db->num_rows > 0)
	{
		$max = $row['permet'];
	}
	else
	{
		$max = $Tmaxcomp[$competence];
	}
	echo $max.'<br />';
}

?>
