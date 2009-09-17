<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
check_undead_players();

//Si le joueur a assez de PA
if($joueur->get_pa() >= 30)
{
	//On recherche les informations sur ce placement
	$requete = 'SELECT x, y, fin_placement, debut_placement FROM placement WHERE id = '.sSQL($_GET['id_construction']);
	$req = $db->query($requete);
	$row = $db->read_assoc($req);

	if ($row['fin_placement'] < time())
	{
		security_block(BAD_ENTRY, "Construction déjà finie !");
	}
	
	//Calcul de la distance entre le joueur et le placement
	$distance = calcul_distance(convert_in_pos($joueur->get_x(), $joueur->get_y()), convert_in_pos($row['x'], $row['y']));
	//Si il est sur la case
	if($distance == 0)
	{
		if ($row['debut_placement'] == 0)
			security_block(BAD_ENTRY, "Erreur de paramètre");

		//Seconde supprimées du décompte
		$secondes_max = floor(($row['fin_placement'] - $row['debut_placement']) * (sqrt($joueur->get_architecture()) / 100));

		// Gemme de fabrique : augmente de effet % le max possible
		if ($joueur->is_enchantement('forge'))
		{
			$secondes_max += floor($joueur->get_enchantement('forge', 'effet') / 100 * $secondes_max);
		}
		$secondes_min = round($secondes_max / 2);
		$secondes = round(rand($secondes_min, $secondes_max));
		//On met à jour le placement
		$requete = "UPDATE placement SET fin_placement = fin_placement - ".$secondes." WHERE id = ".sSQL($_GET['id_construction']);
		if($db->query($requete))
		{
			//On supprime les PA du joueurs
			$joueur->set_pa($joueur->get_pa() - 30);
			//Augmentation de la compétence d'architecture
			$augmentation = augmentation_competence('architecture', $joueur, 1);
			if ($augmentation[1] == 1)
			{
				$joueur->set_architecture($augmentation[0]);
			}
			echo '<h6>La construction a été accélérée de '.transform_sec_temp($secondes).'</h6>';
			echo '<a href="archi_accelere_construction.php?id_construction='.$_GET['id_construction'].'" onclick="return envoiInfo(this.href, \'information\');">Accélérer de nouveau</a>';
			$joueur->sauver();
		}
	}
}
else
{
	echo '<h5>Vous n\'avez pas assez de PA</h5>';
}
?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
</div>
