<?php
if (file_exists('root.php'))
  include_once('root.php');

include ('livre.php');
if(array_key_exists('tri', $_GET)) $tris = $_GET['tri']; else $tris = 'favoris';
?>
<hr>
<?php
if($joueur->get_groupe() != 0) $groupe_joueur = new groupe($joueur->get_groupe());
if (isset($_GET['ID']))
{
	$sort = new sort_jeu($_GET['ID']);

	if(array_key_exists('groupe', $_GET) AND $_GET['groupe'] == 'yes') $groupe = true; else $groupe = false;
	$sortpa_base = $sort->get_pa();
	$sortmp_base = $sort->get_mp();
	$sortpa = round($sort->get_pa() * $joueur->get_facteur_magie());
	$sortmp = round($sort->get_mp() * (1 - (($Trace[$joueur->get_race()]['affinite_'.$sort->get_comp_assoc()] - 5) / 10)));
	//Réduction du cout par concentration
	if($joueur->is_buff('buff_concentration', true)) $sortmp = ceil($sortmp * (1 - ($joueur->get_buff('buff_concentration','effet') / 100)));
	//Coût en MP * 1.5 si sort de groupe
	if($groupe) $sortmp = ceil($sortmp * 1.5);
	$action = false;
	if(isset($groupe_joueur) AND $groupe)
	{
		$cibles = array();
		foreach($groupe_joueur->get_membre_joueur() as $membre)
		{
			//On peut agir avec les membres du groupe si ils sont a 7 ou moins de distance
			if($joueur->get_distance_pytagore($membre) <= 7) $cibles[] = $membre;
		}
	}
	else
		$cibles = array($joueur);

	if($joueur->get_pa() < $sortpa)
		echo '<h5>Pas assez de PA</h5>';
	elseif($joueur->get_mp() < $sortmp)
		echo '<h5>Pas assez de mana</h5>';
	elseif($joueur->get_hp() <= 0)
		echo '<h5>Vous êtes mort</h5>';
	else
	{
		switch($sort->get_type())
		{
			case 'vie' :
				$soin_total = 0;
				foreach($cibles as $cible)
				{
					if($cible->get_hp() > 0)
					{
						if($cible->get_hp() < floor($cible->get_hp_max()))
						{
							$action = true;
							$de_degat_sort = de_soin($joueur->get_comp($sort->get_carac_assoc()), $sort->get_effet());
							$i = 0;
							$de_degat_sort2 = array();
							while($i < count($de_degat_sort))
							{
								$de_degat_sort2[$de_degat_sort[$i]] += 1;
								$i++;
							}
							$i = 0;
							$keys = array_keys($de_degat_sort2);
							while($i < count($de_degat_sort2))
							{
								if ($i > 0) echo ' + ';
								echo $de_degat_sort2[$keys[$i]].'D'.$keys[$i];
								$i++;
							}
							echo '<br />';
							$soin = 0;
							$i = 0;
							while($i < count($de_degat_sort))
							{
								$soin += rand(1, $de_degat_sort[$i]);
								$i++;
							}
							if($soin > (floor($cible->get_hp_max()) - $cible->get_hp())) $soin = floor($cible->get_hp_max()) - $cible->get_hp();
							echo 'Vous soignez '.$cible->get_nom().' de '.$soin.' HP<br />';
							$soin_total += $soin;
							$cible->set_hp($cible->get_hp() + $soin);
							$cible->sauver();
							if($groupe)
							{
								//Insertion du soin de groupe dans le journal de la cible
								$requete = "INSERT INTO journal VALUES('', ".$cible->get_id().", 'rgsoin', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), ".$soin.", 0, ".$joueur->get_x().", ".$joueur->get_y().")";
								$db->query($requete);
							}
						}
						else
						{
							echo 'La cible a toute sa vie<br />';
						}
					}
					else
					{
						echo $cible->get_nom().' est mort.<br />';
					}
				}
				$joueur->set_pa($joueur->get_pa() - $sortpa);
				$joueur->set_mp($joueur->get_mp() - $sortmp);
				if($action)
				{
					$difficulte_sort = diff_sort($sort->get_difficulte(), $joueur, 'incantation', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur->set_incantation($augmentation[0]);
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_incantation().' en incantation</span><br />';
					}
					$difficulte_sort = diff_sort($sort->get_difficulte(), $joueur, 'sort_vie', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('sort_vie', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur->set_sort_vie($augmentation[0]);
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_sort_vie().' en '.$Gtrad['sort_vie'].'</span><br />';
					}
					//sauve_sans_bonus_ignorables($joueur, array('mp', 'pa', 'incantation', 'sort_vie'));
					$joueur->sauver();
					//Insertion du soin de groupe dans le journal du lanceur
					if($groupe)
					{
						$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$joueur->get_id().", 'gsoin', '".$joueur->get_nom()."', 'groupe', NOW(), ".$soin_total.", 0, ".$joueur->get_x().", ".$joueur->get_y().")";
					}
					else
					{
						$requete = "INSERT INTO journal(id_perso, action, actif, passif, time, valeur, valeur2, x, y) VALUES(".$joueur->get_id().", 'soin', '".$joueur->get_nom()."', '".$joueur->get_nom()."', NOW(), ".$soin_total.", 0, ".$joueur->get_x().", ".$joueur->get_y().")";
					}
					$db->query($requete);
				}
				if($groupe) $groupe_href = '&amp;groupe=yes'; else $groupe_href = '';
				echo '<a href="sort.php?ID='.$_GET['ID'].$groupe_href.'" onclick="return envoiInfo(this.href, \'information\')">Utilisez a nouveau ce sort</a>';
			break;
			case 'body_to_mind' :
				if($joueur->get_hp() > $row['effet'])
				{
					$sorthp = $row['effet'];
					$sortmp = $row['effet2'];
					if($sortmp > (floor($joueur['mp_max']) - $joueur->get_mp())) $sortmp = floor($joueur['mp_max']) - $joueur->get_mp();
					echo 'Vous utilisez '.$sorthp.' HP pour convertir en '.$sortmp.' MP<br />';
					$joueur->set_pa($joueur->get_pa() - $sortpa);
					$joueur->set_mp($joueur->get_mp() + $sortmp);
					$joueur->set_hp($joueur->get_hp() - $sorthp);
					if($sortmp > 0)
					{
						$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sorthp);
						$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
						if ($augmentation[1] == 1)
						{
							$joueur->set_incantation($augmentation[0]);
							echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_incantation().' en incantation</span><br />';
						}
						$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'sort_mort', $sortpa_base, $sorthp);
						$augmentation = augmentation_competence('sort_mort', $joueur, $difficulte_sort);
						if ($augmentation[1] == 1)
						{
							$joueur->set_sort_mort($augmentation[0]);
							echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_sort_mort().' en '.$Gtrad['sort_mort'].'</span><br />';
						}
						sauve_sans_bonus_ignorables($joueur, array('mp', 'pa', 'incantation', 'sort_mort'));
					}
					echo '<a href="sort.php?ID='.$_GET['ID'].'" onclick="return envoiInfo(this.href, \'information\')">Utilisez a nouveau ce sort</a>';
				}
				else
				{
					echo 'Vous n\'avez pas assez de points de vie.';
				}
			break;
			case 'teleport' :
				if($joueur->get_hp() > 0)
				{
					$joueur->set_x($Trace[$joueur->get_race()]['spawn_x']);
					$joueur->set_y($Trace[$joueur->get_race()]['spawn_y']);
					$joueur->set_mp($joueur->get_mp() - $sortmp);
					$joueur->set_pa($joueur->get_pa() - $sortpa);
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur->set_incantation($augmentation[0]);
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_incantation().' en incantation</span><br />';
					}
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'sort_element', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('sort_element', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur->set_sort_element($augmentation[0]);
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_sort_element().' en '.$Gtrad['sort_element'].'</span><br />';
					}
					sauve_sans_bonus_ignorables($joueur, array('mp', 'pa', 'incantation', 'sort_element', 'x', 'y'));
					if($db->query($requete)) echo 'Vous vous êtes téléporté dans votre capitale
					<img src="image/pixel.gif" onLoad="envoiInfo(\'deplacement.php\', \'centre\');" />';
				}
				else
				{
					echo 'Le joueur est mort.';
				}
			break;
			case 'repos_sage' :
				//On vérifie qu'il a pas déjà le debuff
				if(!$joueur->is_debuff('repos_sage', true))
				{
					//Mis en place du debuff
					lance_buff($row['type'], $joueur->get_id(), 1, 0, $row['duree'], $row['nom'], 'Vous ne pouvez plus attaquer ni lancer le sort repos du sage', 'perso', 1, 0, 0, 0);
					$joueur->set_pa($joueur->get_pa() - $sortpa);
					$joueur->set_mp($joueur->get_mp() + $row['effet']);
					if($joueur->get_mp() > $joueur->get_mp_max()) $joueur->set_mp($joueur->get_mp_max());
					//Augmentation des compétences
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, 'incantation', $sortpa_base, 1);
					$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur->set_incantation($augmentation[0]);
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_incantation().' en incantation</span><br />';
					}
					$difficulte_sort = diff_sort($row['difficulte'], $joueur, $row['comp_assoc'], $sortpa_base, 1);
					$augmentation = augmentation_competence($row['comp_assoc'], $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$comp_assoc = "set_".$row['comp_assoc'];
						$joueur->$comp_assoc($augmentation[0]);
						$comp_assoc = "get_".$row['comp_assoc'];
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->$comp_assoc().' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
					}
					//Mis à jour du joueur
					sauve_sans_bonus_ignorables($joueur, array('mp', 'pa', 'incantation', $row['comp_assoc']));
				}
				else
				{
					echo 'Vous êtes déjà reposé';
				}
			break;
			case 'buff_critique' : case 'buff_evasion' : case 'buff_bouclier' : case 'buff_sacrifice' : case 'buff_inspiration' : case 'buff_force' : case 'buff_armure_glace' : case 'buff_barriere' : case 'buff_bouclier_sacre' : case 'buff_colere' : case 'buff_epine' : case 'buff_meditation' : case 'buff_rage_vampirique' : case 'buff_rapidite' : case 'buff_concentration' : case 'buff_furie_magique' : case 'buff_surpuissance' : case 'bouclier_feu' : case 'bouclier_terre' : case 'bouclier_eau' : case 'souffrance_extenuante' : case 'bulle_sanctuaire' : case 'bulle_dephasante' :
				foreach($cibles as $cible)
				{
					//Mis en place du buff
					if(lance_buff($sort->get_type(), $cible->get_id(), $sort->get_effet(), $sort->get_effet2(), $sort->get_duree(), $sort->get_nom(), description($sort->get_description(), $sort), 'perso', 0, count($cible->get_buff()), $cible->get_rang_royaume()))
					{
						$action = true;
						echo $cible->get_nom().' a bien reçu le buff<br />';
						//Insertion du buff dans le journal de la cible
						if($groupe)
						{
							$requete = "INSERT INTO journal VALUES('', ".$cible->get_id().", 'rgbuff', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), '".$sort->get_nom()."', 0, 0, 0)";
							$db->query($requete);
						}
					}
					else
					{
						if($G_erreur == 'puissant') echo $cible->get_nom().' bénéficie d\'un buff plus puissant<br />';
						else echo $cible->get_nom().' a trop de buffs.<br />';
					}
				}
				if($action)
				{
					$joueur->set_pa($joueur->get_pa() - $sortpa);
					$joueur->set_mp($joueur->get_mp() - $sortmp);
					//Augmentation des compétences
					$difficulte_sort = diff_sort($sort->get_difficulte(), $joueur, 'incantation', $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur->set_incantation($augmentation[0]);
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_incantation().' en incantation</span><br />';
					}
					$difficulte_sort = diff_sort($sort->get_difficulte(), $joueur, $sort->get_comp_assoc(), $sortpa_base, $sortmp_base);
					$augmentation = augmentation_competence($sort->get_comp_assoc(), $joueur, $difficulte_sort);
					if ($augmentation[1] == 1)
					{
						$joueur->set_comp($sort->get_comp_assoc(), $augmentation[0]);
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->$comp_assoc().' en '.$Gtrad[$sort->get_comp_assoc()].'</span><br />';
					}
					//Mis à jour du joueur
					//sauve_sans_bonus_ignorables($joueur, array('mp', 'pa', 'incantation', $row['comp_assoc']));
					$joueur->sauver();
					if($groupe)
					{
						$requete = "INSERT INTO journal VALUES('', ".$joueur->get_id().", 'gbuff', '".$joueur->get_nom()."', 'groupe', NOW(), '".$sort->get_nom()."', 0, 0, 0)";
						$db->query($requete);
					}
					else
					{
						$requete = "INSERT INTO journal VALUES('', ".$joueur->get_id().", 'buff', '".$joueur->get_nom()."', '".$joueur->get_nom()."', NOW(), '".$sort->get_nom()."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
					}
				}
			break;
			case 'engloutissement' : case 'deluge' : case 'blizzard' : case 'orage_magnetique' :
				//Récupération de tous les joueurs de la case
				/*$requete = "SELECT ID FROM perso WHERE x = ".$joueur->get_x()." AND y = ".$joueur->get_y()." AND statut = 'actif'";
				$req_j = $db->query($requete);
				$cibles = array();*/
				$cibles = perso::create(array('x', 'y'), array($joueur->get_x(), $joueur->get_y()), 'id ASC', false, 'statut = \'actif\'');
				foreach($cibles as $cible)
				{
					//Si c'est pas le joueur
					if($cible->get_id() != $joueur->get_id())
					{
						//Test d'esquive du sort
						$protecion = $cible->get_volonte() * $cible->get_pm() / 3;
						if($cible->is_buff('bulle_sanctuaire', true)) $protection *= $cible->get_buff('bulle_sanctuaire','effet');
						if($cible->is_buff('bulle_dephasante', true)) $protection *= $cible->get_buff('bulle_dephasante','effet');
						$comp_assoc = 'get_'.$row['comp_assoc'];
						$attaque = rand(0, ($joueur->get_volonte() * $joueur->$com_assoc()));
						$defense = rand(0, $protection);
						if ($attaque > $defense)
						{
							//Mis en place du debuff pour tous
							if(lance_buff($row['type'], $cible->get_id(), $row['effet'], $row['effet2'], $row['duree'], $row['nom'], description($row['description'], $row), 'perso', 1, 0, 0))
							{
								echo 'Le sort '.$row['nom'].' a été lancé avec succès sur '.$cible->get_nom().'<br />';
								//Insertion du debuff dans les journaux des 2 joueurs
								$requete = "INSERT INTO journal VALUES('', ".$joueur->get_id().", 'debuff', '".$joueur->get_nom()."', '".$cible->get_nom()."', NOW(), '".$row['nom']."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
								$db->query($requete);
								$requete = "INSERT INTO journal VALUES('', ".$cible->get_id().", 'rdebuff', '".$cible->get_nom()."', '".$joueur->get_nom()."', NOW(), '".$row['nom']."', 0, ".$joueur->get_x().", ".$joueur->get_y().")";
								$db->query($requete);
							}
							else
							{
								echo 'Il bénéficit d\'un debuff plus puissant<br />';
							}
							//Suppression de MP pour orage magnétique
							if($row['type'] == 'orage_magnetique')
							{
								if($cible->is_debuff('orage_magnetique'))
								{
									echo $cible->get_nom().' Est déjà sous cet effet<br />';
								}
								else
								{
									//Réduction des mp de la cible
									$cible->set_mp($cible->get_mp() - ($cible->get_mp_max() * $row['effet'] / 100));
									if($cible->get_mp() < 0) $cible->set_mp(0);
									$cible->sauver();
								}
							}
						}
						else
						{
							echo $cible->get_nom().' resiste a votre sort !<br />';
						}
					}
				}
				$joueur->set_pa($joueur->get_pa() - $sortpa);
				$joueur->set_mp($joueur->get_mp() - $sortmp);
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
					$joueur->set_comp_assoc($row['comp_assoc'], $augmentation[0]);
					echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_comp($row['comp_assoc']).' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
				}
				//Mis à jour du joueur
				$joueur->sauver();
			break;
			case "guerison" :
				$check = false;
				//-- Suppression d'un debuff au hasard
				foreach($cibles as $cible)
				{
					$cible_s = new perso($cible);
					$debuff_tab = array();
					foreach($cible_s->get_debuff() as $debuff)
					{
						if($debuff->is_supprimable())
						{
							$debuff_tab[] = $debuff->get_id();
						};
					}
					if(count($debuff_tab) > 0)
					{
						$requete = "DELETE FROM buff WHERE id=".$debuff_tab[rand(0, count($debuff_tab)-1)].";";
						$db->query($requete);
						$check = true;
					}
					else
					{
						echo "Impossible de lancer de lancer le sort. ".addslashes($cible_s->get_nom())." n&apos;a aucun debuff.<br/>";
					};
				}
				if($check)
				{
					$joueur->set_pa($joueur->get_pa() - $sortpa);
					$joueur->set_mp($joueur->get_mp() - $sortmp);

					//-- Augmentation des compétences
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
						$joueur->set_comp_assoc($row['comp_assoc'], $augmentation[0]);
						echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_comp($row['comp_assoc']).' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
					}
					//-- Mis à jour du joueur
					$joueur->sauver();
				}

				echo "<a href=\"\" onclick=\"return envoiInfo('sort.php?ID=".$_GET["ID"]."', 'information')\">Utilisez a nouveau cette compétence</a>";
			break;
			case "esprit_sacrifie" :	 //-- Esprit Sacrifié
				//-- Suppression d'un debuff au hasard
				if($joueur->is_buff())
				{
					$debuff_tab = array();
					foreach($joueur->get_debuff() as $debuff)
					{
						if($debuff->is_supprimable()) { $debuff_tab[count($debuff_tab)] = $debuff->get_id(); };
					}
					if(count($debuff_tab) > 0)
					{	
						$joueur->set_pa($joueur->get_pa() - $sortpa);
						$joueur->set_mp($joueur->get_mp() - $sortmp);
						
						$buff_tab = array();
						foreach($joueur->get_buff() as $buff)
						{
							$buff_tab[count($buff_tab)] = $buff->get_id();
						}
						
						$db->query("DELETE FROM buff WHERE id=".$buff_tab[rand(0, count($buff_tab)-1)].";");
						$db->query("DELETE FROM buff WHERE id=".$debuff_tab[rand(0, count($debuff_tab)-1)].";");
						{//-- Augmentation des compétences
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
								$joueur->set_comp_assoc($row['comp_assoc'],$augmentation[0]);
								echo '&nbsp;&nbsp;<span class="augcomp">Vous êtes maintenant à '.$joueur->get_comp($row['comp_assoc']).' en '.$Gtrad[$row['comp_assoc']].'</span><br />';
							}
						}
						//-- Mis à jour du joueur
						$joueur->sauver();
					}
					else { echo "Impossible de lancer de lancer le sort. Vous n&apos;a aucun debuff.<br/>"; };
				}
				else { echo "Impossible de lancer de lancer le sort. Vous n&apos;a aucun buff.<br/>"; };
					
				echo "<a href=\"\" onclick=\"return envoiInfo('sort.php?ID=".$_GET["ID"]."', 'information')\">Utilisez a nouveau cette compétence</a>";
			
			break;
		}
	}
	echo '<br /><a href="sort.php" onclick="return envoiInfo(this.href, \'information\');">Revenir au livre de sort</a>';
}
else
{
	if(array_key_exists('action', $_GET))
	{
		switch($_GET['action'])
		{
			case 'favoris' :
				$requete = "INSERT INTO sort_favoris(id_sort, id_perso) VALUES(".sSQL($_GET['id']).", ".$joueur->get_id().")";
				echo $requete;
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
		echo '<a href="sort.php?tri='.$magie.'" onclick="return envoiInfo(this.href, \'information\');"><img src="image/icone_'.$magie.'.png" alt="'.$Gtrad[$magie].'" title="'.$Gtrad[$magie].'" /></a> ';
	}
	$where = '';
	if(array_key_exists('tri', $_GET)) $where = 'AND comp_assoc = \''.$_GET['tri'].'\''; else $_GET['tri'] = 'favoris';
	if($_GET['tri'] == 'favoris')
	{
		$where = ' AND id IN (SELECT id_sort FROM sort_favoris WHERE id_perso = \''.$joueur->get_id().'\')';
		$requete = "SELECT * FROM sort WHERE type != 'rez' AND id IN (SELECT id_sort FROM sort_favoris WHERE id_perso = ".$joueur->get_id().") ORDER BY comp_assoc ASC, type ASC";
	}
	else
	{
		$requete = "SELECT * FROM sort WHERE type != 'rez' ".$where." ORDER BY comp_assoc ASC, type ASC";
	}
	$test = false;
	$sorts = sort_jeu::create('', '', 'comp_assoc ASC, type ASC', false, 'type != \'rez\''.$where);
	//$req = $db->query($requete);
	$magie = '';
	echo '<table width="97%" class="information_case">';
	foreach($sorts as $sort)
	{	
		$sortpa = round($sort->get_pa() * $joueur->get_facteur_magie());
		$sortmp = round($sort->get_mp() * (1 - (($Trace[$joueur->get_race()]['affinite_'.$sort->get_comp_assoc()] - 5) / 10)));
		//Réduction du cout par concentration
		if($joueur->is_buff('buff_concentration', true)) $sortmp = ceil($sortmp * (1 - ($joueur->get_buff('buff_concentration','effet') / 100)));
		if($magie != $sort->get_comp_assoc())
		{
			$magie = $sort->get_comp_assoc();
			$type = '';
			echo '<tr><td colspan="6"><h3>'.$Gtrad[$magie].'</h3></td></tr>';
		}
		if(in_array($sort->get_id(), explode(';',$joueur->get_sort_jeu())))
		{
			$image = image_sort($sort->get_type());
			$incanta = $sort->get_incantation();
			echo '
			<div style="z-index: 3;">
				<tr>';
			//On ne peut uniquement faire que les sorts qui nous target ou target tous le groupe
			if($sort->get_cible() == 1 OR $sort->get_cible() == 8 OR $sort->get_cible() == 2)
			{
				$href = 'envoiInfo(\'sort.php?ID='.$sort->get_id().'\', \'information\')';
				$href2 = 'envoiInfo(\'sort.php?ID='.$sort->get_id().'&amp;groupe=yes\', \'information\')';
				$color = '#444';
				$cursor = 'cursor : pointer;';
			}
			else
			{
				$href = '';
				$href2 = '';
				$cursor = '';
				$color = 'black';
			}

			?>
			<td style="width : 36px;">
				<?php echo $image; ?>
			</td>
			<td>
				<span style="<?php echo $cursor; ?>; text-decoration : none; color : <?php echo $color; ?>;" onclick="<?php echo $href; ?>; return nd();" onmouseover="return <?php echo make_overlib(description($sort->get_description(), $sort).'<br/><span class=&quot;xmall&quot;>Incantation : '.$incanta.'</span>'); ?>" onmouseout="return nd();"> <strong><?php echo $sort->get_nom(); ?></strong></span>
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
			if($row['cible'] == 2)
			{
				if($joueur->is_comp_perso('sort_groupe') || $joueur->is_comp_perso('sort_groupe_'.$sort->get_comp())) echo ' <span style="'.$cursor.'text-decoration : none; color : '.$color.';" onclick="'.$href2.'">(groupe - '.ceil($sortmp * 1.5).' MP)</span>';
			}
			if($_GET['tri'] == 'favoris') echo ' <td><a href="sort.php?action=delfavoris&amp;id='.$sort->get_id().'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/croix_quitte.png" alt="Supprimer des favoris" title="Supprimer des favoris" /></a></td>';
			else echo ' <td><a href="sort.php?action=favoris&amp;id='.$sort->get_id().'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/favoris.png" alt="Favoris" title="Ajouter aux sorts favoris" /></a></td>';
			echo '</tr>';
			?>
			</div>
			<?php
			$i++;
		}

	}
	echo '</table>';
}

?>
<img src="image/pixel.gif" onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
