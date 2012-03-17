<?php //	 -*- tab-width:	2; mode: php -*-
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
check_undead_players();

if ($joueur->is_buff('debuff_rvr'))
{
	echo '<h5>RvR impossible pendant la trêve</h5>';
}
//Si le joueur a assez de PA
elseif($joueur->get_pa() >= 30)
{
	$ID_CONSTRUCTION = sSQL($_GET['id_construction'], SSQL_INTEGER);

	//On recherche les informations sur ce placement
	$requete = 'SELECT x, y, construction.hp as hp_c, batiment.hp as hp_b FROM construction LEFT JOIN batiment ON batiment.id = construction.id_batiment WHERE construction.id = \''.$ID_CONSTRUCTION.'\'';
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	
	//Calcul de la distance entre le joueur et le placement
	$distance = calcul_distance(convert_in_pos($joueur->get_x(), $joueur->get_y()), convert_in_pos($row['x'], $row['y']));
	//Si il est sur la case
	if($distance == 0)
	{

		$buffs = construction::get_construction_buff($ID_CONSTRUCTION);
		foreach ($buffs as $b) {
			if ($b->type == 'sabotage') {
				echo '<h5>Ce bâtiment est saboté</h5></div>';
				print_onload_infoperso();
				exit(0);
			}
		}

		//HP redonnés
		$hp_repare_max = ceil(1000 * (1 - 50/($joueur->get_architecture()+50)));
		$hp_repare_min = $joueur->get_architecture();
		$hp_repare = rand($hp_repare_min, $hp_repare_max);
		// Gemme de fabrique : augmente de effet % l'accélération
		if ($joueur->is_enchantement('forge'))
		{
			$hp_repare += floor($joueur->get_enchantement('forge', 'effet') / 100 * $hp_repare);
		}
		$hp = $row['hp_c'] + $hp_repare;
		if($hp > $row['hp_b'])
		{
			$hp = $row['hp_b'];
			$hp_repare = $row['hp_b'] - $row['hp_c'];
		}
		//On vérifie qu'il y a bien eu réparation du batiment
		if($hp_repare > 0)
		{
			//On met à jour le placement
			$requete = "UPDATE construction SET hp = ".$hp." WHERE id = ".sSQL($_GET['id_construction']);
			if($db->query($requete))
			{
				//On supprime les PA du joueurs
				$joueur->set_pa($joueur->get_pa() - 30);
				//Augmentation de la compétence d'architecture
				$augmentation = augmentation_competence('architecture', $joueur, 1);
				if ($augmentation[1] == 1)
				{
					$joueur->set_architecture($augmentation[0]);
					$joueur->sauver();
				}
				
				// Augmentation du compteur de l'achievement
				$achiev = $joueur->get_compteur('structure_hp');
				$achiev->set_compteur($achiev->get_compteur() + $hp_repare);
				$achiev->sauver();
				
				echo '<h6>La construction a été réparée de '.$hp_repare.' HP</h6>';
				$joueur->sauver();
			}
		}
		else echo '<h5>La construction est déjà totalement réparée</h5>';
	}
}
?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
</div>
