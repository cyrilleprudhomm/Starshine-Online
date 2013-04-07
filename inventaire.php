<?php //	 -*- tab-width:	2; mode: php -*-
if (file_exists('root.php'))
  include_once('root.php');

//Affiche et gère l'inventaire du personnage

//Inclusion des fichiers indispensables
include_once(root.'inc/fp.php');

// Inclusion du gestionnaire de compétences
include_once(root.'fonction/competence.inc.php');

//Visu par un autre joueur
if(array_key_exists('id_perso', $_GET))
{
	$visu = true;
	$bonus = recup_bonus($_GET['id_perso']);
	if(array_key_exists(20, $bonus) AND check_affiche_bonus($bonus[20], $joueur, $perso))
	{
		$joueur_id = $_GET['id_perso'];
	}
	else exit();
}
else
{
	$visu = false;
	$joueur_id = $_SESSION['ID'];
}
$joueur = new perso($joueur_id);
//Filtre
if(array_key_exists('filtre', $_GET)) $filtre_url = '&amp;filtre='.$_GET['filtre'];
else $filtre_url = '';
$W_requete = 'SELECT royaume, type, info FROM map WHERE x ='.$joueur->get_x()
		 .' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
?>
<fieldset>
<legend>Inventaire du Personnage</legend>
<ul id="messagerie_onglet">
		<li><a href="inventaire.php"
			onclick="return envoiInfo(this.href, 'information');">Personnage</a>
		</li>
		<li><a href="inventaire_pet.php"
			onclick="return envoiInfo(this.href, 'information');">Créature</a></li>
</ul>
	<div class="spacer"></div>
<?php
//Switch des actions
if(!$visu AND isset($_GET['action']))
{
	switch($_GET['action'])
	{
		case 'desequip' :
			if($joueur->desequip($_GET['partie']))
			{
			}
			else
			{
				echo '<h5>'.$G_erreur.'</h5>';
			}
		break;
		case 'equip' :
			if($joueur->equip_objet($joueur->get_inventaire_slot_partie($_GET['key_slot'])))
			{
				//On supprime l'objet de l'inventaire
				$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot'], true), 1);
				$joueur->sauver();
			}
			else
			{
				echo '<h5>'.$G_erreur.'</h5>';
			}
		break;
		case 'utilise' :
				/*
				 * Pose d'un bâtiment ou ADS
				 */
			if($_GET['type'] == 'fort' OR $_GET['type'] == 'tour' OR $_GET['type'] == 'bourg' OR $_GET['type'] == 'mur' OR $_GET['type'] == 'arme_de_siege')
			{
				if ($joueur->is_buff('debuff_rvr'))
				{
					echo '<h5>RvR impossible pendant la trêve</h5>';
					break;
				}

					if ($W_row['type'] == 1)
				{
						echo '<h5>Vous ne pouvez pas poser de bâtiment sur une ville</h5>';
						break;
					}

					//Cherche infos sur l'objet
					$requete = "SELECT batiment.id AS batiment_id FROM objet_royaume RIGHT JOIN batiment ON batiment.id = objet_royaume.id_batiment WHERE objet_royaume.id = ".sSQL($_GET['id_objet']);
					$req = $db->query($requete);
					if ($db->num_rows($req) == 0)
					{
						die('<h5>Erreur SQL</h5>');
					}
					$row = $db->read_assoc($req);
					$batiment = new batiment($row['batiment_id']);
					if($R->get_diplo($joueur->get_race()) != 127 && $_GET['type'] != 'arme_de_siege' && $batiment->get_id() != 1) // id=1 : poste avancé
					{
						echo '<h5>Vous ne pouvez poser un bâtiment uniquement sur un territoire qui vous appartient</h5>';
						break;
					}

						//On vérifie si ya pas déjà un batiment en construction
						$requete = "SELECT id FROM placement WHERE x = ".$joueur->get_x()." AND y = ".$joueur->get_y();
						$req = $db->query($requete);
					if($db->num_rows > 0)
						{
						echo '<h5>Il y a déjà un bâtiment en construction sur cette case !</h5>';
						break;
					}

							//On vérifie si ya pas déjà un batiment
							$requete = "SELECT id FROM construction WHERE x = ".$joueur->get_x()." AND y = ".$joueur->get_y();
							$req = $db->query($requete);
					if($db->num_rows > 0)
							{
						echo '<h5>Il y a déjà un bâtiment sur cette case !</h5>';
						break;
					}

                $nbr_mur = 0;
                if( $_GET['type'] == 'mur' )
                {
        					//   Debut Evolution #581
        					// Pour pouvoir construire un mur, il faut ne pas avoir plus de 2 murs autour de celui que l'on construit.
        					// Attention, les tests pour voir s'il n'y a pas trop de murs autour seront uniquement
        					// fait sur les 4 cases juste au nord, a l'est, au sud et a l'ouest (avec la meme limite qu'actuellement).
        					// Il faut aussi tester pour chacune des ces cases ou il y a deja des murs si la limite ne sera pas depassee une fois le mur pose

        					// On commence par extraire la position des murs ou des constructions de murs a 2 cases de distance de la case a traiter
        					//   000000
        					//   000000
        					//   000000
        					$position_murs=array();
        					$position_murs[0]=array(0,0,0,0,0);
        					$position_murs[1]=array(0,0,0,0,0);
        					$position_murs[2]=array(0,0,0,0,0);
        					$position_murs[3]=array(0,0,0,0,0);
        					$position_murs[4]=array(0,0,0,0,0);
					
        					// Il y a donc 25 positions a recuperer
        					$requete  = 'SELECT x,y FROM construction WHERE ABS(CAST(x AS SIGNED) -'.$joueur->get_x().') <= 2 AND ABS(CAST(y AS SIGNED) - '.$joueur->get_y().') <= 2 AND type LIKE "mur"';
        					$requete  = 'SELECT id,x,y FROM construction WHERE ABS(CAST(x AS SIGNED) - '.$joueur->get_x().') <= 2 AND ABS(CAST(y AS SIGNED) - '.$joueur->get_y().') <= 2 AND type LIKE "mur" UNION SELECT id,x,y FROM placement WHERE ABS(CAST(x AS SIGNED) - '.$joueur->get_x().') <= 2 AND ABS(CAST(y AS SIGNED) - '.$joueur->get_y().') <= 2 AND type LIKE "mur"';
        					$req = $db->query($requete);

        					// Stockage des positions dans la matrice
        					while($row = $db->read_assoc($req))
        					{
        						$position_murs[$row[x]-$joueur->get_x()+2][$row[y]-$joueur->get_y()+2]=1;
        					}
        					// Rajout de la position du nouveau mur dans la matrice pour les tests (il est au milieu de la matrice).
        					$position_murs[2][2]=1;

        					// DEBUG message
        					/*echo '<h4> Matrice des murs deja poses ou en construction:</h4>';
        					for ($i=0;$i<5;$i++)
        					{
        						echo '<h4>';
        						for ($j=0;$j<5;$j++)
        						{
        							// Attention, les x sont verticaux la !!!
        							echo ' '.$position_murs[$j][$i];
        						}
        						echo '</h4>';
        					}*/

        					// Gestion des cardinalites (somme du nombre de murs adjacent au nord, ouest, est, sud de chaque position en comptant la position courante
        					// Cette matrice n'est pas utilisee directement, elle sert juste pour le debug (attention a la variable max_nb_murs si on la retire !)
        					$murs_cardinalite=array();
        					$murs_cardinalite[0]=array(0,0,0);
        					$murs_cardinalite[1]=array(0,0,0);
        					$murs_cardinalite[2]=array(0,0,0);
        					$max_nb_murs=0;
        					for ($x = 1; $x<=3 ; $x+=1)
        					{
        						for ($y = 1; $y<=3 ; $y+=1)
        						{
        							$murs_cardinalite[$x-1][$y-1]=$position_murs[$x-1][$y]+$position_murs[$x+1][$y]+$position_murs[$x][$y-1]+$position_murs[$x][$y+1]+ $position_murs[$x][$y];
        							$max_nb_murs=max($max_nb_murs,$murs_cardinalite[$x-1][$y-1]);
        						}
        					}

        					// DEBUG MESSAGE
        					/*
        					echo '<h4>Nombre de murs estimes (en incluant le futur mur) autour de la position ('.$row[x]-$joueur->get_x().','.$row[y]-$joueur->get_y().'):</h4>';
        					echo '<h4>'.$murs_cardinalite[0][0].'  '.$murs_cardinalite[1][0].' '.$murs_cardinalite[2][0].'</h4>';
        					echo '<h4>'.$murs_cardinalite[0][1].'  '.$murs_cardinalite[1][1].' '.$murs_cardinalite[2][1].'</h4>';
        					echo '<h4>'.$murs_cardinalite[0][2].'  '.$murs_cardinalite[1][2].' '.$murs_cardinalite[2][2].'</h4>';
        					echo '<h4>Le maximum est :'.$max_nb_murs.'</h4>';
        					*/
        					// Il reste maintenant a verifier que toutes les conditions sont réunies
        					// Si une des cases vaut 4 ou plus, alors erreur
        					$nbr_mur = $max_nb_murs;
        					//   Fin Evolution #581
                }
					if( $nbr_mur > 3 )
                {
						echo '<h5>Il y a déjà trop de murs autour !</h5>';
						break;
					}

					// Règles des distance entre bâtiments
					$isOk = true;
					if($_GET['type'] == 'bourg' || $_GET['type'] == 'fort'){
						// Distance d'une capitale
						$distanceMax = $_GET['type'] == 'bourg' ? 5 : 7;

						$requete = "SELECT 1 FROM map"
										." WHERE x >= ".max(($joueur->get_x() - $distanceMax), 1)
										." AND x <= ".min(($joueur->get_x() + $distanceMax), 190)
										." AND y >= ".max(($joueur->get_y() - $distanceMax), 1)
										." AND y <= ".min(($joueur->get_y() + $distanceMax), 190)
										." AND type = 1";
						$req = $db->query($requete);
						if($db->num_rows > 0)
                  {
							echo '<h5>Il y a une capitale à moins de '.$distanceMax.' cases !</h5>';
							$isOk = false;
						}

						$facteurEntretien = $R->get_facteur_entretien();
						
						// Distance entre Bourgs
						if($isOk && $_GET['type'] == 'bourg'){
							// dist entre 2 bourgs
							$distanceMax = 7 * $facteurEntretien;

							$requete = "SELECT 1 FROM construction"
								." WHERE x >= ".max(($joueur->get_x() - $distanceMax), 1)
								." AND x <= ".min(($joueur->get_x() + $distanceMax), 190)
								." AND y >= ".max(($joueur->get_y() - $distanceMax), 1)
								." AND y <= ".min(($joueur->get_y() + $distanceMax), 190)
								." AND type = 'bourg'";
							$req = $db->query($requete);
							if($db->num_rows > 0)
							{
								echo '<h5>Il y a un bourg à moins de '.$distanceMax.' cases !</h5>';
								$isOk = false;
							}

							// On vérifie aussi les chantiers
							if($isOk){
								$requete = "SELECT 1 FROM placement"
								." WHERE x >= ".max(($joueur->get_x() - $distanceMax), 1)
								." AND x <= ".min(($joueur->get_x() + $distanceMax), 190)
								." AND y >= ".max(($joueur->get_y() - $distanceMax), 1)
								." AND y <= ".min(($joueur->get_y() + $distanceMax), 190)
								." AND type = 'bourg'";
								$req = $db->query($requete);
								if($db->num_rows > 0)
								{
									echo '<h5>Il y a un bourg en construction à moins de '.$distanceMax.' cases !</h5>';
									$isOk = false;
								}
							}
						}

						// Distance entre forts
						else if($isOk && $_GET['type'] == 'fort'){
							// dist entre 2 forts du même royaume
							$distanceForts = 4;
							$distanceMax = $distanceForts * $facteurEntretien;

							$requete = "SELECT id FROM construction"
									." WHERE x >= ".max(($joueur->get_x() - $distanceMax), 1)
									." AND x <= ".min(($joueur->get_x() + $distanceMax), 190)
									." AND y >= ".max(($joueur->get_y() - $distanceMax), 1)
									." AND y <= ".min(($joueur->get_y() + $distanceMax), 190)
									." AND type = 'fort'"
									." AND royaume = ".$R->get_id()
									." UNION"
									." SELECT id FROM construction"
										." WHERE x >= ".max(($joueur->get_x() - $distanceForts), 1)
										." AND x <= ".min(($joueur->get_x() + $distanceForts), 190)
										." AND y >= ".max(($joueur->get_y() - $distanceForts), 1)
										." AND y <= ".min(($joueur->get_y() + $distanceForts), 190)
										." AND type = 'fort'"
										." AND royaume <> ".$R->get_id();
							$req = $db->query($requete);
							if($db->num_rows > 0)
							{
								echo '<h5>Il y a un fort à moins de '.$distanceMax.' cases !</h5>';
								$isOk = false;
							}

							// On vérifie aussi les chantiers
							if($isOk){
								$requete = "SELECT id FROM placement"
											." WHERE x >= ".max(($joueur->get_x() - $distanceMax), 1)
											." AND x <= ".min(($joueur->get_x() + $distanceMax), 190)
											." AND y >= ".max(($joueur->get_y() - $distanceMax), 1)
											." AND y <= ".min(($joueur->get_y() + $distanceMax), 190)
											." AND type = 'fort'"
									." AND royaume = ".$R->get_id()
									." UNION"
											." SELECT id FROM placement"
											." WHERE x >= ".max(($joueur->get_x() - $distanceForts), 1)
											." AND x <= ".min(($joueur->get_x() + $distanceForts), 190)
											." AND y >= ".max(($joueur->get_y() - $distanceForts), 1)
											." AND y <= ".min(($joueur->get_y() + $distanceForts), 190)
											." AND type = 'fort'"
												." AND royaume <> ".$R->get_id();
								$req = $db->query($requete);
								if($db->num_rows > 0)
								{
									echo '<h5>Il y a un fort en cosntruction à moins de '.$distanceMax.' cases !</h5>';
									$isOk = false;
								}
							}
						}
					}
					if(!$isOk){
						break;
					}


					if( $joueur->is_buff('convalescence') && $joueur->get_pa() < 10 )
					{
						echo '<h5>Vous n\'avez pas assez de PA !</h5>';
						break;
					}

    								//Positionnement de la construction
    								if($_GET['type'] == 'arme_de_siege')
    								{
                      $distance = 1;
                      $rez = $batiment->get_bonus('rez');
    								}//max($row['temps_construction'] * $distance, $row['temps_construction_min']);
    								else
                    {
    								  $distance = calcul_distance(convert_in_pos($Trace[$joueur->get_race()]['spawn_x'], $Trace[$joueur->get_race()]['spawn_y']), ($joueur->get_pos()));
    									$rez = 0;
                    }
                    $time = time() + max($batiment->get_temps_construction() * $distance, $batiment->get_temps_construction_min());

                    $requete = 'INSERT INTO placement (type, x, y, royaume, debut_placement, fin_placement, id_batiment, hp, nom, rez, point_victoire) VALUES (?,?,?,?,?,?,?,?,?,?,?)';
                    $types = 'siiiiiiisii';
                    $params = array($_GET['type'], $joueur->get_x(),
                                    $joueur->get_y(), 
                                    $Trace[$joueur->get_race()]['numrace'],
                                    time(), $time, $batiment->get_id(),
                                    $batiment->get_hp(), $batiment->get_nom(),
                                    $rez, $batiment->get_point_victoire());
                    $db->param_query($requete, $params, $types);
    								// Coût en PA si en convalescence
    								if( $joueur->is_buff('convalescence') )
    								{
    								  $joueur->set_pa( $joueur->get_pa() - 10 );
                    }
    								//On supprime l'objet de l'inventaire
    								$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']), 1);
    								$joueur->sauver();
    								echo '<h6>'.$batiment->get_nom().' posé avec succès</h6>';

    								if($_GET['type'] == 'mur')
    								{
    									// Augmentation du compteur de l'achievement
    									$achiev = $joueur->get_compteur('pose_murs');
    									$achiev->set_compteur($achiev->get_compteur() + 1);
    									$achiev->sauver();
    								}

                  }
			switch($_GET['type'])
			{
				case 'drapeau' :
				  if ($joueur->is_buff('debuff_rvr'))
					{
						echo '<h5>RvR impossible pendant la trêve</h5>';
						break;
					}
					if ($W_row['type'] != 1 && $W_row['type'] != 4)
					{
						//Cherche infos sur l'objet
						$requete = "SELECT *, batiment.id AS batiment_id  FROM objet_royaume RIGHT JOIN batiment ON batiment.id = objet_royaume.id_batiment WHERE objet_royaume.id = ".sSQL($_GET['id_objet']);
						$req = $db->query($requete);
						$row = $db->read_assoc($req);

						//Si terrain neutre ou pas a nous ET que c'est pas dans un donjon
						if((($R->get_diplo($joueur->get_race()) > 6 && $R->get_diplo($joueur->get_race()) != 127) OR $R->get_nom() == 'Neutre') AND !is_donjon($joueur->get_x(), $joueur->get_y()))
						{
							//Si c'est un petit drapeau, on vérifie qu'on est uniquement sur neutre
							if($row['nom'] == 'Petit Drapeau' && $R->get_nom() != 'Neutre')
							{
									echo '<h5>Vous ne pouvez pas poser de petit drapeau sur une case non neutre !</h5>';
							}
							else
							{
								//On vérifie si ya pas déjà un batiment en construction
								$requete = "SELECT id FROM placement WHERE x = ".$joueur->get_x()." AND y = ".$joueur->get_y();
								$req = $db->query($requete);
								if($db->num_rows <= 0)
								{
									//On vérifie si ya pas déjà un batiment
									$requete = "SELECT id FROM construction WHERE x = ".$joueur->get_x()." AND y = ".$joueur->get_y();
									$req = $db->query($requete);
									if($db->num_rows <= 0)
									{
                    if( !$joueur->is_buff('convalescence') OR $joueur->get_pa() >= 10 )
                    {
  										//Positionnement du drapeau
  										$distance = calcul_distance(convert_in_pos($Trace[$joueur->get_race()]['spawn_x'], $Trace[$joueur->get_race()]['spawn_y']), ($joueur->get_pos()));
  										$time = time() + max($row['temps_construction'] * $distance, $row['temps_construction_min']);
  										$requete = "INSERT INTO placement (id, type, x, y, royaume, debut_placement, fin_placement, id_batiment, hp, nom, rez) VALUES('', 'drapeau', '".$joueur->get_x()."', '".$joueur->get_y()."', '".$Trace[$joueur->get_race()]['numrace']."', ".time().", '".$time."', '".$row['batiment_id']."', '".$row['hp']."', 'drapeau', 0)";
  										$db->query($requete);
      								// Coût en PA si en convalescence
      								if( $joueur->is_buff('convalescence') )
      								{
      								  $joueur->set_pa( $joueur->get_pa() - 10 );
                      }
  										//On supprime l'objet de l'inventaire
  										$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot'], true), 1);
  										$joueur->sauver();
  										echo '<h6>Drapeau posé avec succès</h6>';

  										// Augmentation du compteur de l'achievement
  										$achiev = $joueur->get_compteur('pose_drapeaux');
  										$achiev->set_compteur($achiev->get_compteur() + 1);
  										$achiev->sauver();

  										if ($W_row['info'] == 3)
  										{
  											// Augmentation du compteur de l'achievement
  											$achiev = $joueur->get_compteur('pose_drapeaux_sable');
  											$achiev->set_compteur($achiev->get_compteur() + 1);
  											$achiev->sauver();
  										}
                    }
                    else
                    {
  								    echo '<h5>Vous n\'avez pas assez de PA !</h5>';
                    }
									}
									else
									{
										echo '<h5>Il y a déjà un batiment sur cette case !</h5>';
									}
								}
								else
								{
									echo '<h5>Il y a déjà un batiment en construction sur cette case !</h5>';
								}
							}
						}
						else
						{
							echo '<h5>Vous ne pouvez poser un drapeau uniquement sur les royaumes avec lesquels vous êtes en guerre</h5>';
						}
					}
					else
					{
						echo '<h5>Vous ne pouvez pas poser de drapeau sur ce type de terrain</h5>';
					}
				break;
				case 'identification' :
					$fin = false;
					$i = 0;
					$materiel = $joueur->recherche_objet('o2');
					//my_dump($materiel);
					if ($materiel == false) {
						echo '<h5>Vous n\'avez pas de materiel d\'identification</h5>';
						$fin = true;
					}
					elseif ($joueur->get_pa() < 10) {
						echo '<h5>Vous n\'avez pas assez de points d\'action</h5>';
						$fin = true;
					}
					else {
						$joueur->add_pa(-10); /* pas oublier que ca coute 10 PA */
					}
					$count = count($joueur->get_inventaire_slot_partie());
					while(!$fin AND $i < $count)
					{
						//echo $joueur->get_inventaire_slot()[$i];
						$stack = explode('x', $joueur->get_inventaire_slot_partie($i));
						$id_objet = $stack[0];
						if(mb_substr($joueur->get_inventaire_slot_partie($i), 0, 1) == 'h')
						{
							$augmentation = augmentation_competence('identification', $joueur, 3);
							if ($augmentation[1] == 1)
							{
								$joueur->set_comp('identification', $augmentation[0]);
							}
							//echo $id_objet;
							$requete = "SELECT * FROM gemme WHERE id = ".mb_substr($id_objet, 2);
							$req = $db->query($requete);
							$row = $db->read_assoc($req);
							$player = rand(0, $joueur->get_identification());
							$thing = rand(0, pow(10, $row['niveau']));
							//echo $joueur['identification'].' / '.pow(10, $row['niveau']).' ---- '.$player.' VS '.$thing;
							//Si l'identification réussie
							if($player > $thing)
							{
								//On remplace la gemme par celle identifiée
								$gemme = mb_substr($joueur->get_inventaire_slot_partie($i), 1);
								$joueur->set_inventaire_slot_partie($gemme, $i);
								$joueur->set_inventaire_slot(serialize($joueur->get_inventaire_slot_partie(false, true)));
								echo 'Identification réussie !<br />Votre gemme est une '.$row['nom'];
								$log_admin = new log_admin();
								$message = $joueur->get_nom().' a identifié '.$row['nom'];
								$log_admin->send($joueur->get_id(), 'identification', $message);
							}
							else
							{
								echo '<h5>L\'identification n\'a pas marché...</h5>';
							}
							//On supprime l'objet de l'inventaire
							$joueur->supprime_objet('o2', 1);
							$fin = true;
						}
						$i++;
					}
					$joueur->sauver(); /* On sauve a la fin pour les PA */
				break;
				case 'potion_vie' :
					if($joueur->get_hp() > 0)
					{
						$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
						if(check_utilisation_objet($joueur, $objet))
						{
							$requete = "SELECT effet, nom, pa, mp FROM objet WHERE id = ".$objet['id_objet'];
							$req = $db->query($requete);
							$row = $db->read_assoc($req);
               				echo 'Vous utilisez une '.$row['nom'].' elle vous redonne '.$row['effet'].' points de vie<br />';
							?>
	<img src="image/pixel.gif"
		onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
								<?php
                				$joueur->add_hp($row['effet']);
                				$joueur->add_mp(-$row['mp']);
                				$joueur->add_pa(-$row['pa']);
                				$joueur->sauver();
								
								// Augmentation du compteur de l'achievement
								$achiev = $joueur->get_compteur('use_potion');
								$achiev->set_compteur($achiev->get_compteur() + 1);
								$achiev->sauver();
						}
					}
					else echo 'Vous êtes mort !';
				break;
				case 'potion_guerison' :
					if($joueur->get_hp() > 0)
					{
						$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
						if(check_utilisation_objet($joueur, $objet))
						{
							foreach($joueur->get_buff() as $buff)
							{
								if($buff->get_debuff() == 1)
								{
					if($buff->get_supprimable() == 1) {
$buff_tab[count($buff_tab)] = $buff->get_id();
};
								}
							}
							if(count($buff_tab) > 0)
							{
								$db->query("DELETE FROM buff WHERE id=".$buff_tab[rand(0, count($buff_tab)-1)].";");
								echo '<h6>Une malédiction a été correctement supprimée</h6>';
							}
							else echo '<h5>Vous n\'avez pas de malédiction a supprimer</h5>';
						}
					}
					else echo 'Vous êtes mort !';
				break;
				case 'robustesse' :
				  die('code mort');
				break;
				case 'globe_pa' :
					$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
					if(check_utilisation_objet($joueur, $objet))
					{
						$requete = "SELECT effet, nom, pa, mp FROM objet WHERE id = ".$objet['id_objet'];
						$req = $db->query($requete);
						$row = $db->read_assoc($req);
						$joueur->set_pa($joueur->get_pa() + $row['effet']);
						if($joueur->get_pa() > floor($G_PA_max)) $joueur->set_pa(floor($G_PA_max));
						echo 'Vous utilisez un '.$row['nom'].'<br />';
						?>
	<img src="image/pixel.gif"
		onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
						<?php
						$joueur->set_mp(max(0, ($joueur->get_mp() - $row['mp'])));
						$joueur->sauver();
					}
				break;
				case 'parchemin_tp' :
					$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
					$requete = "SELECT effet, nom, pa, mp FROM objet WHERE id = ".$objet['id_objet'];
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					//Calcul de la distance entre le point où est le joueur et sa ville natale
					$distance = detection_distance($W_case, convert_in_pos($Trace[$joueur->get_race()]['spawn_x'], $Trace[$joueur->get_race()]['spawn_y']));
					if($row['effet'] >= $distance)
					{
						if(check_utilisation_objet($joueur, $objet))
						{
							//Téléportation du joueur
							echo 'Vous utilisez un '.$row['nom'].'<br />';
							?>
	<img src="image/pixel.gif"
		onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
							<?php
							$requete = "UPDATE perso SET x = ".$Trace[$joueur->get_race()]['spawn_x'].", y = ".$Trace[$joueur->get_race()]['spawn_y'].", pa = pa - ".$row['pa'].", mp = mp - ".$row['mp']." WHERE ID = ".$joueur->get_id();
							$db->query($requete);
						}
					}
					else
					{
						echo 'Vous êtes trop loin de la ville pour utiliser ce parchemin.';
					}
				break;
				case 'objet_quete' :
					$stack = explode('x', $joueur->get_inventaire_slot_partie($_GET['key_slot']));
					$id_objet = $stack[0];
					$id_objet_reel = mb_substr($id_objet, 1);
					if($id_objet_reel == 27)
					{
						?>
	Journal du mage Demtros - Unité aigle -<br /> < Le journal est en très
	mauvais état, certaines pages sont déchirées, ou rendues illisibles par
	l'humidité et des taches de sang ><br /> <br /> 12 Dulfandal : Nous
	approchons enfin de la cité de Myriandre. Je ne peux expliquer le
	trouble qui me saisi depuis plusieurs jours mais je sais que cela à un
	rapport avec les ruines de cette ville. Le malaise que je ressens est
	presque palpable, mais impossible d'en déterminer la cause. J'en ai
	fais part a Frankriss mais malgré toute la considération de ce dernier,
	je sens bien qu'ils me pense juste un peu nerveux...<br /> Je vais
	prendre deux tours de garde ce soir, de toute façon, je n'arriverais
	pas à dormir.<br /> <br /> 13 Dulfandal : Nous voici enfin arrivés,
	Achen et les autres sont partis en reconnaissance. Je scanne l'espace
	astral dans l'espoir de déterminer d'où peux provenir la perturbation
	que je ressens mais en vain.. Je sens malgré tout qu'il y a quelque
	chose d'anormal.<br /> <br /> 14 Dulfandal : Achen est mort, Dubs est
	l'agonie, dans son délire il est quand même parvenu à nous dire ce qui
	c'est passé, il semblerait qu'il ai été attaqués par des goules surgie
	de nulle part, Dubs a réussi à s'enfuir mais pas achen. il nous dis que
	les goules ont disparues d'un coup... frankriss est préoccupé, mais il
	gère la situation avec son sang froid coutumier. Nous allons former un
	groupe d'assaut pour aller récupérer Achen, ou ce qu'il en reste...
	L'unité aigle ne laisse jamais un compagnon derrière elle.<br /> Je ne
	sais pas ce qui se passe dans cette ville mais j'ai un mauvais
	pressentiment.<br /> 15 Dulfandal : le sort était camouflé, c'est pour
	ça que je l'ai pas perçu, celui qui l'a lancer doit être un mage
	exceptionnel pour arriver à camoufler une telle portion de terrain...
	nous nous déplaçons les épées sorties et près au combat, l'ennemi peut
	surgir de n'importe ou. Je n'arrive pas a démêler la trame du sort,
	trop puissant pour moi ... ( taches de sang )<br /> Il faut que
	j'écrive ( taches de sang ) sachent ce qui se passe ( taches de sang ),
	un nécromancien ( la suite est déchirée ).<br />
						<?php
					}
				break;
			case 'grimoire':
				$stack = explode('x', $joueur->get_inventaire_slot_partie($_GET['key_slot']));
				$id_objet = $stack[0];
				$id_objet_reel = mb_substr($id_objet, 1);
				$ok = utilise_grimoire($id_objet_reel, $joueur);
				if ($ok)
				{
					$joueur->supprime_objet($id_objet, 1);
				} else {
					echo "Vous ne pouvez pas lire ce grimoire<br />";
				}
				break;
			default:
				error_log('Utilisation d\'un objet invalide: '.$_GET['type']);
			}
		break;
		//Dépot de l'objet au dépot militaire
		case 'depot' :
			//On le dépose
			if ($joueur->is_buff('debuff_rvr'))
			{
				echo '<h5>RvR impossible pendant la trêve</h5>';
				break;
			}
			if ($R->get_race() != $joueur->get_race())
			{
				echo '<h5>Impossible de poser au dépot '.$R->get_race().'</h5>';
			}
			else
			{
				$objet = $joueur->get_inventaire_slot_partie($_GET['key_slot']);
				$id = mb_substr($objet, 1, strlen($objet));
				$requete = "INSERT INTO depot_royaume VALUES (NULL, ".$id.", ".$R->get_id().")";
				$db->query($requete);
				//On supprime l'objet de l'inventaire
				$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']), 1);
				echo '<h6>Objet posé avec succès</h6>';
			}
		break;
		case 'vente' :
			$id_objet = $_GET['id_objet'];
			$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
			$categorie = empty($objet['categorie'])?mb_substr($id_objet, 0, 1):$objet['categorie'];
			//Rétrocompatibilité avec anciens inventaires
			if($categorie != 'a' AND $categorie != 'p' AND $categorie != 'o' AND $categorie != 'g' AND $categorie != 'm' AND $categorie != 'l' AND $categorie != 'd')
			{
				$categorie = 'p';
			}
			else $id_objet = mb_substr($id_objet, 1);
			switch ($categorie)
			{
				//Si c'est une arme
				case 'a' :
					$requete = "SELECT * FROM arme WHERE ID = ".$id_objet;
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$partie = 'main_droite';
				break;
				//Si c'est une protection
				case 'p' :
					$requete = "SELECT * FROM armure WHERE ID = ".$id_objet;
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$partie = $row['type'];
				break;
				case 'd' :
					$requete = "SELECT * FROM objet_pet WHERE id = ".$id_objet;
					$req = $db->query($requete);
					$row = $db->read_assoc($req);
					$partie = $row['type'];
				break;
				case 'o' :
					$requete = "SELECT * FROM objet WHERE id = ".$id_objet;
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
				break;
				case 'g' :
					$requete = "SELECT * FROM gemme WHERE id = ".$id_objet;
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
					$row['prix'] = pow(15, $row['niveau']) * 10;
				break;
				case 'm' :
					$requete = "SELECT * FROM accessoire WHERE id = ".$id_objet;
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
				break;
				case 'l' :
					$requete = "SELECT * FROM grimoire WHERE id = ".$id_objet;
					//Récupération des infos de l'objet
					$req = $db->query($requete);
					$row = $db->read_array($req);
				break;
			}
			$modif_prix = 1;
				if($objet['slot'] > 0)
				{
					$modif_prix = 1 + ($objet['slot'] / 5);
				}
				if($objet['slot'] == '0')
				{
					$modif_prix = 0.9;
				}
				if($objet['enchantement'] > '0')
				{
					$requete = "SELECT * FROM gemme WHERE id = ".$objet['enchantement'];
					$req = $db->query($requete);
					$row_e = $db->read_assoc($req);
					$modif_prix = 1 + ($row_e['niveau'] / 2);
				}
			$prix = floor(($row['prix']) * $modif_prix / $G_taux_vente);
			$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']), 1);
			$requete = "UPDATE perso SET star = star + ".$prix." WHERE ID = ".$joueur->get_id();
			$req = $db->query($requete);
		break;
		case 'ventehotel' :
			//On vérifie qu'il a moins de 10 objets en vente actuellement
			$requete = "SELECT COUNT(*) FROM hotel WHERE id_vendeur = ".$joueur->get_id();
			$req = $db->query($requete);
			$row = $db->read_array($req);
			$objet_max = 10;
			$bonus_craft = ceil($joueur->get_artisanat() / 5);
			$objet_max += $bonus_craft;
			if($row[0] >= $objet_max)
			{
				echo 'Vous avez déjà '.$objet_max.' objets ou plus en vente.';
			}
			else
			{
				$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
				$categorie = $objet['categorie'];
				switch ($categorie)
				{
					//Si c'est une arme
					case 'a' :
						$requete = "SELECT * FROM arme WHERE ID = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = 'main_droite';
					break;
					//Si c'est une protection
					case 'p' :
						$requete = "SELECT * FROM armure WHERE ID = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$partie = $row['type'];
					break;
					case 'd' :
						$requete = "SELECT * FROM objet_pet WHERE id = ".$objet['id_objet'];
						$req = $db->query($requete);
						$row = $db->read_assoc($req);
						$partie = $row['type'];
					break;
					case 'o' :
						$requete = "SELECT * FROM objet WHERE id = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
					break;
					case 'g' :
						$requete = "SELECT * FROM gemme WHERE id = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
						$row['prix'] = pow(8, ($row['niveau'] + 1)) * 10;
					break;
					case 'm' :
						$requete = "SELECT * FROM accessoire WHERE id = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
					break;
					case 'l' :
						$requete = "SELECT * FROM grimoire WHERE id = ".$objet['id_objet'];
						//Récupération des infos de l'objet
						$req = $db->query($requete);
						$row = $db->read_array($req);
					break;
				}
				$modif_prix = 1;
				if($objet['slot'] > 0)
				{
					$modif_prix = 1 + ($objet['slot'] / 5);
				}
				if($objet['slot'] == '0')
				{
					$modif_prix = 0.9;
				}
				if($objet['enchantement'] > '0')
				{
					$requete = "SELECT * FROM gemme WHERE id = ".$objet['enchantement'];
					$req = $db->query($requete);
					$row_e = $db->read_assoc($req);
					$modif_prix = 1 + ($row_e['niveau'] / 2);
				}
				$prix = floor((2 * $row['prix']) * $modif_prix / $G_taux_vente);
				$prixmax = $prix * 10;
				echo '
				<h2>Inventaire</h2>
			<div style="font-size : 0.9em;">
				<form method="get" name="formulaire" action="javascript:envoiInfo(\'inventaire.php\', \'information\');">
					Mettre en vente à l\'hotel des ventes pour <input type="text" name="prix" value="'.$prix.'" onchange="formulaire.comm.value = formulaire.prix.value * '.($R->get_taxe_diplo($joueur->get_race()) / 100).';" onkeyup="formulaire.comm.value = formulaire.prix.value * '.($R->get_taxe_diplo($joueur->get_race()) / 100).';" /> Stars<br />
					Taxe : <input type="text" name="comm" value="'.($prix * $R->get_taxe_diplo($joueur->get_race()) / 100).'" disabled="true" /><br />
					Maximum = '.$prixmax.' stars.<br />
					<input type="hidden" name="action" value="ventehotel2" />
					<input type="button" name="btnSubmit" value="Mettre en vente" onclick="javascript:envoiInfo(\'inventaire.php?action=ventehotel2&amp;key_slot='.$_GET['key_slot'].'&amp;prix=\' + formulaire.prix.value + \'&amp;max='.$prixmax.'&amp;comm=\' + formulaire.comm.value, \'information\');" />
				</form>';
				exit();
			}
		break;
		case 'ventehotel2' :
			$comm = $_GET['comm'];
			if($_GET['prix'] > $_GET['max'])
			{
				echo 'Vous voulez vendre cet objet trop chère, le commissaire priseur n\'en veut pas !<br />';
			}
			else
			{
				if($_GET['prix'] > 0)
				{
					if($joueur->get_star() >= $comm)
					{
						$objet = $joueur->get_inventaire_slot_partie($_GET['key_slot']);
						$objet_d = decompose_objet($objet);
						switch ($objet_d['categorie'])
						{
							//Si c'est une arme
							case 'a' :
								$requete = "SELECT * FROM arme WHERE ID = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$partie = 'main_droite';
								$objet_id = $objet;
							break;
							//Si c'est une protection
							case 'p' :
								$requete = "SELECT * FROM armure WHERE ID = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$partie = $row['type'];
								$objet_id = $objet;
							break;
							case 'd' :
								$requete = "SELECT * FROM objet_pet WHERE id = ".$objet_d['id_objet'];
								$req = $db->query($requete);
								$row = $db->read_assoc($req);
								$partie = $row['type'];
								$objet_id = $objet;
							break;
							case 'o' :
								$requete = "SELECT * FROM objet WHERE id = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$objet_id = $objet_d['id'];
							break;
							case 'g' :
								$requete = "SELECT * FROM gemme WHERE id = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$row['prix'] = pow(15, $row['niveau']) * 10;
								$objet_id = $objet;
							break;
							case 'm' :
								$requete = "SELECT * FROM accessoire WHERE id = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$objet_id = $objet;
							break;
							case 'l' :
								$requete = "SELECT * FROM grimoire WHERE id = ".$objet_d['id_objet'];
								//Récupération des infos de l'objet
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$objet_id = $objet;
							break;
						}
						$prix = $_GET['prix'];
						if($objet_id != '')
						{
							$joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']), 1);
							$joueur->set_star($joueur->get_star() - $comm);
							$joueur->sauver();
							$requete = "INSERT INTO hotel VALUES (NULL, '".$objet_id."', ".$joueur->get_id().", ".sSQL($_GET['prix']).", 1, '".$R->get_race()."', ".time().")";
							$req = $db->query($requete);
							$R->set_star($R->get_star() + $comm);
							$R->sauver();
							$requete = "UPDATE argent_royaume SET hv = hv + ".$comm." WHERE race = '".$R->get_race()."'";
							$db->query($requete);
							$message_mail = $joueur->get_nom()." vend ".nom_objet($objet_id)." (".$objet_id.") pour ".$_GET['prix']." stars. Commission : ".$comm." stars";
							echo 'Vous mettez en vente '.nom_objet($objet_id).' pour '.$_GET['prix'].' stars. Commission : '.$comm.' stars<br />';
						}
						$log_admin = new log_admin();
						$log_admin->send($joueur->get_id(), 'mis en vente HV', $message_mail);
					}
					else
					{
						echo 'Vous n\'avez pas assez de stars pour payer la commission';
					}
				}
				else
				{
					echo 'Pas de prix négatif ou nul !';
				}
			}
		break;
		case 'slot' :
			$craft = $joueur->get_forge();
			if($joueur->get_race() == 'scavenger') $craft = round($craft * 1.45);
			if($joueur->get_accessoire() !== false)
			{
				$accessoire = $joueur->get_accessoire();
				if($accessoire->type == 'fabrication')
					$craft = round($craft * (1 + ($accessoire->effet / 100)));
			}
			
			// Gemme de fabrique : augmente de effet % le craft
			$joueur->get_armure();
			if ($joueur->get_enchantement()!== false &&
					$joueur->is_enchantement('forge')) {
				$craft += round($craft * ($joueur->get_enchantement('forge','effet') / 100));
			}

			$chance_reussite1 = pourcent_reussite($craft, 10);
			$chance_reussite2 = pourcent_reussite($craft, 30);
			$chance_reussite3 = pourcent_reussite($craft, 100);
			echo 'Quel niveau d\'enchâssement voulez vous ?
			<ul>
				<li><a href="inventaire.php?action=slot2&amp;key_slot='.$_GET['key_slot'].'&amp;niveau=1'.$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">Niveau 1</a> <span class="small">('.$chance_reussite1.'% de chances de réussite)</span></li>
				<li><a href="inventaire.php?action=slot2&amp;key_slot='.$_GET['key_slot'].'&amp;niveau=2'.$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">Niveau 2</a> <span class="small">('.$chance_reussite2.'% de chances de réussite)</span></li>
				<li><a href="inventaire.php?action=slot2&amp;key_slot='.$_GET['key_slot'].'&amp;niveau=3'.$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">Niveau 3</a> <span class="small">('.$chance_reussite3.'% de chances de réussite)</span></li>
			</ul>';
		break;
		case 'slot2' :
			if($joueur->get_pa() >= 10)
			{
				$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
				if(empty($objet['slot']))
				{
					switch($_GET['niveau'])
					{
						case '1' :
							$difficulte = 10;
						break;
						case '2' :
							$difficulte = 30;
						break;
						case '3' :
							$difficulte = 100;
						break;
					}
					$craft = $joueur->get_forge();
					if($joueur->get_race() == 'scavenger') $craft = round($craft * 1.45);
					if($joueur->get_accessoire() !== false)
					{
						$accessoire = $joueur->get_accessoire();
						if($accessoire->type == 'fabrication')
							$craft = round($craft * (1 + ($accessoire->effet / 100)));
					}

					// Gemme de fabrique : augmente de effet % le craft
					$joueur->get_armure();
					if ($joueur->get_enchantement()!== false &&
							$joueur->is_enchantement('forge')) {
						$craft += round($craft * ($joueur->get_enchantement('forge','effet') / 100));
					}

					$craftd = rand(0, $craft);
					$diff = rand(0, $difficulte);
					echo 'dé du joueur : '.$craft.' / dé difficulté : '.$difficulte.'<br />
					Résultat joueur : '.$craftd.' / Résultat difficulte : '.$diff.'<br />';
					if($craftd >= $diff)
					{
						//Craft réussi
						echo 'Réussite !<br />';
						$objet['slot'] = $_GET['niveau'];
						
						// Augmentation du compteur de l'achievement
						$achiev = $joueur->get_compteur('objets_slot');
						$achiev->set_compteur($achiev->get_compteur() + 1);
						$achiev->sauver();
					}
					else
					{
						//Craft échec
						echo 'Echec... L\'objet ne pourra plus être enchâssable<br />';
						$objet['slot'] = 0;
					}
					$augmentation = augmentation_competence('forge', $joueur, 2);
					if ($augmentation[1] == 1)
					{
						$joueur->set_forge($augmentation[0]);
						$joueur->sauver();
					}
					$objet_r = recompose_objet($objet);
					$joueur->set_inventaire_slot_partie($objet_r, $_GET['key_slot']);
					$joueur->set_inventaire_slot(serialize($joueur->get_inventaire_slot_partie(false, true)));
					$joueur->set_pa($joueur->get_pa() - 10);
					$joueur->sauver();
				}
				else
					echo 'Cet objet &agrave; d&eacute;j&agrave; un slot!';
			}
			else
			{
				echo 'Vous n\'avez pas assez de PA.';
			}
		break;
		case 'enchasse' :
			$gemme = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
			$requete = "SELECT * FROM gemme WHERE id = ".$gemme['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			switch($row['type'])
			{
				case 'arme' :
					$type = 'a';
				break;
				case 'armure' :
					$type = 'p';
				break;
				case 'accessoire' :
					$type = 'm';
				break;
			}
			switch($row['niveau'])
			{
				case 1 :
					$difficulte = 10;
				break;
				case 2 :
					$difficulte = 30;
				break;
				case 3 :
					$difficulte = 100;
				break;
			}
			$craft = $joueur->get_forge();
			if($joueur->get_race() == 'scavenger') $craft = round($craft * 1.45);
			if($joueur->get_accessoire() !== false)
			{
				$accessoire = $joueur->get_accessoire();
				if($accessoire->type == 'fabrication')
					$craft = round($craft * (1 + ($accessoire->effet / 100)));
			}

			// Gemme de fabrique : augmente de effet % le craft
			if ($joueur->get_enchantement()!== false &&
					$joueur->is_enchantement('forge')) {
				$craft += round($craft * ($joueur->get_enchantement('forge','effet') / 100));
			}

			echo 'Dans quel objet voulez vous enchâsser cette gemme de niveau '.$row['niveau'].' ?
			<ul>';
			//Recherche des objets pour enchassement possible
			$i = 0;
			while($i <= $G_place_inventaire)
			{
				if($joueur->get_inventaire_slot_partie($i) != '')
				{
					$objet_i = decompose_objet($joueur->get_inventaire_slot_partie($i));
					//echo '<br />'.$joueur->get_inventaire_slot()[$i].'<br />';
					if($objet_i['identifier'] AND $objet_i['categorie'] != 'r')
					{
						if($objet_i['categorie'] == 'a') $table = 'arme';
						elseif($objet_i['categorie'] == 'p') $table = 'armure';
						elseif($objet_i['categorie'] == 'm') $table = 'accessoire';
						elseif($objet_i['categorie'] == 'o') $table = 'objet';
						elseif($objet_i['categorie'] == 'g') $table = 'gemme';
						else {
							print_debug("table introuvable pour $objet_i[categorie]");
							$i++;
							continue;
						}
						$requete = "SELECT type FROM ".$table." WHERE id = ".$objet_i['id_objet'];
						$req_i = $db->query($requete);
						$row_i = $db->read_row($req_i);
						$check = true;
						$j = 0;
						$parties = explode(';', $row['partie']);
						$count = count($parties);
						if (strlen($row['partie']) > 0) $check = false;
						while(!$check AND $j < $count)
						{
							if($parties[$j] == $row_i[0]) $check = true;
							//echo $parties[$j].' '.$row_i[0].'<br />';
							$j++;
						}
						if($check AND ($objet_i['categorie'] == $type) AND ($objet_i['slot'] >= $row['niveau']))
						{
							$nom = nom_objet($joueur->get_inventaire_slot_partie($i));
							$chance_reussite = pourcent_reussite($craft, $difficulte);
							//On peut mettre la gemme
							echo '<li><a href="inventaire.php?action=enchasse2&amp;key_slot='.$_GET['key_slot'].'&amp;key_slot2='.$i.'&amp;niveau='.$row['niveau'].$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">'.$nom.' / slot niveau '.$objet_i['slot'].'</a> <span class="xsmall">'.$chance_reussite.'% de chance de réussite</span></li>';
						}
					}
				}
				$i++;
			}
			echo '
			</ul>';
		break;
		case 'enchasse2' :
			if($joueur->get_pa() >= 20)
			{
				$craft = $joueur->get_forge();
				if($joueur->get_race() == 'scavenger') $craft = round($craft * 1.45);
				if($joueur->get_accessoire() !== false)
				{
					$accessoire = $joueur->get_accessoire();
					if($accessoire->type == 'fabrication')
						$craft = round($craft * (1 + ($accessoire->effet / 100)));
				}				

				$gemme = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']));
				$objet = decompose_objet($joueur->get_inventaire_slot_partie($_GET['key_slot2']));
				switch($_GET['niveau'])
				{
					case '1' :
						$difficulte = 10;
					break;
					case '2' :
						$difficulte = 30;
					break;
					case '3' :
						$difficulte = 100;
					break;
				}

				// Gemme de fabrique : augmente de effet % le craft
				if ($joueur->get_enchantement()!== false &&
						$joueur->is_enchantement('forge')) {
					$craft += round($craft * ($joueur->get_enchantement('forge','effet') / 100));
				}

				$craftd = rand(0, $craft);
				$diff = rand(0, $difficulte);
				echo 'dé du joueur : '.$craft.' / dé difficulté : '.$difficulte.'<br />
				Résultat joueur : '.$craftd.' / Résultat difficulte : '.$diff.'<br />';
				$gemme_casse = false;
				if($craftd >= $diff)
				{
					//Craft réussi
					echo 'Réussite !<br />';
					$objet['enchantement'] = $gemme['id_objet'];
					$objet['slot'] = 0;
					$gemme_casse = true;
					
					// Augmentation du compteur de l'achievement
					$achiev = $joueur->get_compteur('objets_slotted');
					$achiev->set_compteur($achiev->get_compteur() + 1);
					$achiev->sauver();
				}
				else
				{
					//Craft échec
					//66% chance objet plus enchassable, 34% gemme disparait
					$rand = rand(1, 100);
					if($rand <= 34)
					{
						echo 'Echec... la gemme a cassé...<br />';
						$gemme_casse = true;
					}
					else
					{
						echo 'Echec... L\'objet ne pourra plus être enchassable...<br />';
						$objet['slot'] = 0;
					}
				}
				$augmentation = augmentation_competence('forge', $joueur, 1);
				if ($augmentation[1] == 1)
				{
					$joueur->set_forge($augmentation[0]);
					$joueur->sauver();
				}
				$objet_r = recompose_objet($objet);
				$joueur->set_inventaire_slot_partie($objet_r, $_GET['key_slot2']);
				$joueur->set_inventaire_slot(serialize($joueur->get_inventaire_slot_partie(false, true)));
				$joueur->set_pa($joueur->get_pa() - 20);
				$joueur->sauver();
				if($gemme_casse) $joueur->supprime_objet($joueur->get_inventaire_slot_partie($_GET['key_slot']), 1);
			}
			else
			{
				echo 'Vous n\'avez pas assez de PA.';
			}
		break;
	}
	refresh_perso();
}
$joueur = new perso($joueur_id);
$tab_loc = array();

$tab_loc[0]['loc'] = 'accessoire';
$tab_loc[0]['type'] = 'accessoire';
$tab_loc[1]['loc'] = 'tete';
$tab_loc[1]['type'] = 'armure';
$tab_loc[2]['loc'] = 'cou';
$tab_loc[2]['type'] = 'armure';

$tab_loc[3]['loc'] = 'main_droite';
$tab_loc[3]['type'] = 'arme';
$tab_loc[4]['loc'] = 'torse';
$tab_loc[4]['type'] = 'armure';
$tab_loc[5]['loc'] = 'main_gauche';
$tab_loc[5]['type'] = 'arme';

$tab_loc[6]['loc'] = 'main';
$tab_loc[6]['type'] = 'armure';
$tab_loc[7]['loc'] = 'ceinture';
$tab_loc[7]['type'] = 'armure';
$tab_loc[8]['loc'] = 'doigt';
$tab_loc[8]['type'] = 'armure';

$tab_loc[9]['loc'] = ' ';
$tab_loc[9]['type'] = 'vide';
$tab_loc[10]['loc'] = 'jambe';
$tab_loc[10]['type'] = 'armure';
$tab_loc[11]['loc'] = 'dos';
$tab_loc[11]['type'] = 'armure';

$tab_loc[12]['loc'] = ' ';
$tab_loc[12]['type'] = 'vide';
$tab_loc[13]['loc'] = 'chaussure';
$tab_loc[13]['type'] = 'armure';
$tab_loc[14]['loc'] = ' ';
$tab_loc[14]['type'] = 'vide';
?>

	<table cellspacing="3" width="100%"
		style="background: url('image/666.png') center no-repeat;">

<?php
$color = 2;
$compteur=0;
foreach($tab_loc as $loc)
{
	if (($compteur % 3) == 0)
		{
			echo '<tr style="height : 55px;">';
		}
		if ($loc['type']=='vide')
		{
			echo '<td>';
		}
		else
		{
			echo '<td class="inventaire2">';
		}
		

		if($joueur->inventaire()->$loc['loc'] != '')
		{
			$objet = decompose_objet($joueur->get_inventaire_partie($loc['loc']));
			//On peut désequiper
			if(!$visu AND $joueur->get_inventaire_partie($loc['loc']) != '' AND $joueur->get_inventaire_partie($loc['loc']) != 'lock') $desequip = true; else $desequip = false;
			switch($loc['type'])
			{
				case 'arme' :
					if($joueur->get_inventaire_partie($loc['loc']) != 'lock')
					{
						$requete = "SELECT * FROM `arme` WHERE id = ".$objet['id_objet'];
						$sqlQuery = $db->query($requete);
						$row = $db->read_array($sqlQuery);
						$image = 'image/arme/arme'.$row['id'].'.png'; 
						$nom = $row['nom'];
					}
					else
					{
						$nom = 'Lock';
						$image = '';
					}
				break;
				case 'armure' :
					$requete = "SELECT * FROM `armure` WHERE id = ".$objet['id_objet'];
					$sqlQuery = $db->query($requete);
					$row = @$db->read_array($sqlQuery);
					$image = 'image/armure/'.$loc['loc'].'/'.$loc['loc'].$row['id'].'.png'; 
					$nom = $row['nom'];
					
				break;
				case 'accessoire' :
					$requete = "SELECT * FROM `accessoire` WHERE id = ".$objet['id_objet'];
					$sqlQuery = $db->query($requete);
					$row = @$db->read_array($sqlQuery);
					$image = 'image/accessoire/accessoire'.$row['id'].'.png'; 
					$nom = $row['nom'];
				break;
			}
			if($desequip)
			{
				echo '<a href="inventaire.php?action=desequip&amp;partie='.$loc['loc'].$filtre_url.'" onclick="return envoiInfo(this.href, \'information\');">
				<img src="'.$image.'" style="float : left;" title="Déséquiper" alt="Déséquiper" />
				</a>';
			}
			echo '<strong>'.$nom.'</strong>';
			if($objet['slot'] > 0)
			{
				echo '<br /><span class="xsmall">Slot niveau '.$objet['slot'].'</span>';
			}
			if($objet['slot'] == '0')
			{
				echo '<br /><span class="xsmall">Slot impossible</span>';
			}
			if($objet['enchantement'] > '0')
			{
				$requete = "SELECT * FROM gemme WHERE id = ".$objet['enchantement'];
				$req = $db->query($requete);
				$row_e = $db->read_assoc($req);
				echo '<br /><span class="xsmall">Enchantement de '.$row_e['enchantement_nom'].'</span>';
			}
		}
		else
		{
			echo $Gtrad[$loc['loc']];
		}
		
		
		if($joueur->get_inventaire_partie($loc['loc']) != '' AND $joueur->get_inventaire_partie($loc['loc']) != 'lock')
		{
			switch($loc['type'])
			{
				case 'arme' :
					if($loc['loc'] == 'main_droite')
					{
						echo '<br />Dégâts : '.$joueur->get_arme_degat('droite');
					}
					else
					{
						if($row['type'] == 'dague')	echo '<br />Dégâts : '.$joueur->get_arme_degat('gauche');
						else echo '<br />Dégâts absorbés : '.$joueur->get_bouclier()->degat;
					}
				break;
				case 'armure' :
					echo '<br />PP : '.$row['PP'].' / PM : '.$row['PM'];
				break;
			}
		}
		
	echo '</td>';
	if ((($compteur + 1) % 3) == 0)
	{ 
		echo '</tr>';
	}
$compteur++;
}
?>
</table>
<?php
if(!$visu)
{
	 if(array_key_exists('filtre', $_GET)) $filtre = $_GET['filtre'];
	 else $filtre = 'utile';
?>
	<p>
		Place restante dans l'inventaire :
		<?php echo ($G_place_inventaire - count($joueur->get_inventaire_slot_partie())) ?>
		/
		<?php echo $G_place_inventaire;?>
	</p>
<div id='messagerie_menu'>
		<span class="<?php if($filtre == 'utile'){echo 'seleted';}?>"
			onclick="envoiInfo('inventaire_slot.php?javascript=ok&amp;filtre=utile', 'inventaire_slot')">Utile</span>
		<span class="<?php if($filtre == 'arme'){ echo 'seleted';} ?>"
			onclick="envoiInfo('inventaire_slot.php?javascript=ok&amp;filtre=arme', 'inventaire_slot')">Arme</span>
		<span class="<?php if($filtre == 'armure'){echo 'seleted';}?>"
			onclick="envoiInfo('inventaire_slot.php?javascript=ok&amp;filtre=armure', 'inventaire_slot')">Armure</span>
		<span class="<?php if($filtre == 'autre'){echo 'seleted';}?>"
			onclick="envoiInfo('inventaire_slot.php?javascript=ok&amp;filtre=autre', 'inventaire_slot')">Autre</span>
</div>
<div id="inventaire_slot">
	<?php
	require_once('inventaire_slot.php');
	?>
</div>
<br />
<?php
// Augmentation du compteur de l'achievement
$achiev = $joueur->get_compteur('nbr_arme_siege');
$achiev->set_compteur(intval($arme_de_siege));
$achiev->sauver();
?>
</fieldset>
<?php
}
?>
