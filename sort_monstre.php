<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
$tab_sort_jeu = explode(';', $joueur->get_sort_jeu());
$W_case = $_GET['poscase'];
$coord = convert_in_coord($W_case);
$W_distance = detection_distance($W_case, $_SESSION["position"]);
?>
<fieldset>
	<legend>Livre de Sorts</legend>
<?php
if (isset($_GET['ID']))
{
	$requete = "SELECT * FROM sort_jeu WHERE id = ".sSQL($_GET['ID']);
	$req = $db->query($requete);
	$row = $db->read_array($req);
	
	if($W_distance > $row['portee'])
	{
		echo 'Vous êtes trop loin pour lancer ce sort !';
	}
	else
	{
		$sortpa_base = $row['pa'];
		$sortmp_base = $row['mp'];
		$sortpa = round($row['pa'] * $joueur->get_facteur_magie());
		$sortmp = round($row['mp'] * (1 - (($Trace[$joueur->get_race()]['affinite_'.$row['comp_assoc']] - 5) / 10)));
		//Réduction du cout par concentration
		if($joueur->is_buff('buff_concentration')) $sortmp = ceil($sortmp * (1 - ($joueur->get_buff('buff_concentration','effet') / 100)));
		if($joueur->get_pa() < $sortpa)
		{
			echo 'Pas assez de PA';
		}
		elseif($joueur->get_mp() < $sortmp)
		{
			echo 'Pas assez de mana';
		}
		elseif($joueur->get_hp() <= 0)
		{
			echo 'Vous êtes mort';
		}
		else
		{
			switch($row['type'])
			{
				case 'debuff_aveuglement' : case 'debuff_desespoir' : case 'debuff_enracinement' : case 'debuff_desespoir' : case 'debuff_ralentissement' :
					$cible = recupmonstre($_GET['id_monstre']);
					//Test d'esquive du sort
					$attaque = rand(0, ($joueur->get_volonte() * ($joueur[$row['comp_assoc']] + $joueur->get_incantation())));
					$defense = rand(0, ($cible['volonte'] * $cible->get_pm() / 3));
					$joueur->set_pa($joueur->get_pa() - $sortpa);
					$joueur->set_mp($joueur->get_mp() - $sortmp);
					if ($attaque > $defense)
					{
						//Mis en place du debuff
						if(lance_buff($row['type'], $_GET['id_monstre'], $row['effet'], $row['effet2'], ($row['duree'] * 4), $row['nom'], description($row['description'], $row), 'monstre', 1, 0, 0))
						{
							echo 'Le sort '.$row['nom'].' a été lancé avec succès sur '.$cible->get_nom().'<br />';
						}
						else
						{
							echo $cible->get_nom().' bénéficit d\'un débuff plus puissant<br />';
						}
					}
					else
					{
						echo $cible->get_nom().' resiste a votre sort !<br />';
				 	}
					//Augmentation des compétences
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur->set_incantation($augmentation[0]);
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_incantation().' en incantation</span><br />';
					}
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, $row['comp_assoc'], $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence($row['comp_assoc'], $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur[$row['comp_assoc']] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur[$row['comp_assoc']].' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
					}
					//Mis à jour du joueur
					sauve_sans_bonus_ignorables($joueur, array('mp', 'pa', 'incantation', $row['comp_assoc']));
				break;
				case 'maladie_amorphe' : case 'maladie_degenerescence' : case 'maladie_mollesse' :
					//On selectionne tous les monstres de la case
					$requete = "SELECT id FROM map_monstre WHERE x = ".$coord['x']." AND y = ".$coord['y'];
					$req_monstre = $db->query($requete);
					while($row_monstre = $db->read_assoc($req_monstre))
					{
						$cible = recupmonstre($row_monstre['id']);
						//Test d'esquive du sort
						$protection = $cible['volonte'] * $cible->get_pm() / 3;
						if(array_key_exists('bulle_sanctuaire', $cible->get_buff())) $protection *= $cible->get_buff('bulle_sanctuaire','effet');
						if(array_key_exists('bulle_dephasante', $cible->get_buff())) $protection *= $cible->get_buff('bulle_dephasante','effet');
						$attaque = rand(0, ($joueur->get_volonte() * $joueur[$row['comp_assoc']]));
						$defense = rand(0, $protection);
						if ($attaque > $defense)
						{
							$duree = $row['duree'];
							if(array_key_exists('souffrance_extenuante', $joueur->get_buff())) $duree = $duree * $joueur->get_buff('souffrance_extenuante','effet');
							//Mis en place du debuff
							if(lance_buff($row['type'], $cible->get_id(), $row['effet'], $row['effet2'], ($duree * 4), $row['nom'], description($row['description'], $row), 'monstre', 1, 0, 0))
							{
								echo 'Le sort '.$row['nom'].' a été lancé avec succès sur '.$cible->get_nom().'<br />';
							}
							else
							{
								echo $cible->get_nom().' bénéficit d\'un debuff plus puissant<br />';
							}
						}
						else
						{
							echo $cible->get_nom().' resiste a votre sort !<br />';
			 			}
			 		}
					$joueur->set_pa($joueur->get_pa() - $sortpa);
					$joueur->set_mp($joueur->get_mp() - $sortmp);
					//Augmentation des compétences
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur->get_incantation() = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_incantation().' en incantation</span><br />';
					}
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, $row['comp_assoc'], $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence($row['comp_assoc'], $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur[$row['comp_assoc']] = $augmentation[0];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur[$row['comp_assoc']].' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
					}
					//Mis à jour du joueur
					sauve_sans_bonus_ignorables($joueur, array('mp', 'pa', 'incantation', $row['comp_assoc']));
				break;
			}
		}
	}
	echo '<br /><a href="sort_monstre.php?poscase='.$W_case.'&amp;id_monstre='.$_GET['id_monstre'].'" onclick="return envoiInfo(this.href, \'information\');">Revenir au livre de sort</a>';
}
else
{
	if(array_key_exists('action', $_GET))
	{
		switch($_GET['action'])
		{
			case 'favoris' :
				$requete = "INSERT INTO sort_favoris VALUES('', ".sSQL($_GET['id']).", ".$joueur->get_id().")";
				$db->query($requete);
			break;
			case 'delfavoris' :
				$requete = "DELETE FROM sort_favoris WHERE id_sort =  ".sSQL($_GET['id'])." AND id_perso = ".$joueur->get_id();
				$db->query($requete);
			break;
		}
	}
	$i = 0;
	$type = '';
	$magies = array('favoris');
	$magie = '';
	$requete = "SELECT * FROM sort_jeu GROUP BY comp_assoc";
	$req = $db->query($requete);
	while($row = $db->read_array($req))
	{
		if($magie != $row['comp_assoc'])
		{
			$magie = $row['comp_assoc'];
			$magies[] = $row['comp_assoc'];
		}
	}
	foreach($magies as $magie)
	{
		echo '<a href="sort_monstre.php?poscase='.$W_case.'&amp;tri='.$magie.'&amp;id_monstre='.$_GET['id_monstre'].'" onclick="return envoiInfo(this.href, \'information\');"><img src="image/icone_'.$magie.'.png" alt="'.$Gtrad[$magie].'" title="'.$Gtrad[$magie].'" /></a> ';
	}
	if(array_key_exists('tri', $_GET)) $where = 'WHERE comp_assoc = \''.sSQL($_GET['tri']).'\''; else $_GET['tri'] = 'favoris';
	if($_GET['tri'] == 'favoris')
	{
		$requete = "SELECT * FROM sort_jeu WHERE id IN (SELECT id_sort FROM sort_favoris WHERE id_perso = ".$joueur->get_id().") AND cible = 4";
	}
	else
	{
		$requete = "SELECT * FROM sort_jeu ".$where." AND cible = 4 ORDER BY comp_assoc ASC, type ASC";
	}
	$req = $db->query($requete);
	$magie = '';
	while($row = $db->read_array($req))
	{
		$sortmp = round($row['mp'] * (1 - (($Trace[$joueur->get_race()]['affinite_'.$row['comp_assoc']] - 5) / 10)));
		$sortpa = round($row['pa'] * $joueur->get_facteur_magie());
		//Réduction du cout par concentration
		if(array_key_exists('buff_concentration', $joueur->get_buff())) $sortmp = ceil($sortmp * (1 - ($joueur->get_buff('buff_concentration','effet') / 100)));
		if($magie != $row['comp_assoc'])
		{
			$magie = $row['comp_assoc'];
			echo '<h2>'.$Gtrad[$magie].'</h2>';
		}
		if(in_array($row['id'], $tab_sort_jeu))
		{
			if($Gtrad[$row['type']] != $Gtrad[$type])
			{
				$type = $row['type'];
				echo '<h3>'.$Gtrad[$row['type']].'</h3>';
			}
			$image = image_sort($row['type']);
			?>
			<div style="z-index: 3;">
			<tr>
			<?php
			//On ne peut uniquement faire que les sorts qui nous target ou target tous le groupe
			if($row['cible'] == 4)
			{
				$href = 'envoiInfo(\'sort_monstre.php?poscase='.$W_case.'&amp;ID='.$row['id'].'&amp;id_monstre='.$_GET['id_monstre'].'\', \'information\')';
				$color = 'blue';
				$cursor = 'cursor : pointer;';
			}
			else
			{
				$href = '';
				$cursor = '';
				$color = 'black';
			}
			?>
			<td style="width : 36px;">
				<?php echo $image; ?>
			</td>
			<td>
				<span style="<?php echo $cursor; ?>text-decoration : none; color : <?php echo $color; ?>;" onclick="<?php echo $href; ?>" onmousemove="afficheInfo('info_<?php echo $i; ?>', 'block', event, 'centre');" onmouseout="afficheInfo('info_<?php echo $i; ?>', 'none', event );"><?php echo $row['nom']; ?></span>
				<div style="display: none; z-index: 2; position : absolute; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 200px; padding: 5px;" id="info_<?php echo $i; ?>">
				<?php
				echo description($row['description'], $row).'<br /><span class="xmall">Incantation : '.$incanta.'</span>';
				?>
				</div>
			</td>
			<?php
			echo '
			<td>
				<span class="xsmall"> '.$sortpa.' PA 
			</td>
			<td>
				'.$sortmp.' MP
			</td> 
			<td>';
			?>
			</div>
			<div style="display: none; z-index: 2; position : absolute; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 200px; padding: 5px;" id="info_<?php echo $i; ?>">
			<?php
			echo description($row['description'], $row);
			if($_GET['tri'] == 'favoris') echo ' <td><a href="sort_monstre.php?action=delfavoris&amp;id='.$row['id'].'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/croix_quitte.png" alt="Supprimer des favoris" title="Supprimer des favoris" /></a></td>';
			else echo ' <td><a href="sort_monstre.php?action=favoris&amp;id='.$row['id'].'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/favoris.png" alt="Favoris" title="Ajouter aux sorts favoris" /></a></td>';
			echo '</tr>';
			?>
			</div>
			<?php
			$i++;
		}
	}
}

?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
</fieldset>