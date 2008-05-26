<?php

//Inclusion du haut du document html
include('haut_ajax.php');

$joueur = recupperso($_SESSION['ID']);

check_perso($joueur);

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_case = $_GET['poscase'];
$W_requete = 'SELECT * FROM map WHERE ID =\''.sSQL($W_case).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_array($W_req);
$R = get_royaume_info($joueur['race'], $W_row['royaume']);

$_SESSION['position'] = convert_in_pos($joueur['x'], $joueur['y']);
?>
    <h2 class="ville_titre"><?php if(!array_key_exists('fort', $_GET)) return_ville( '<a href="javascript:envoiInfo(\'ville.php?poscase='.$W_case.'\', \'centre\')">'.$R['nom'].'</a> - ', $W_case); ?>  <?php echo '<a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'\',\'carte\')">';?> Gestion du Royaume </a></h2>
		<?php include('ville_bas.php');?>

	<div class="ville_test">
	<div id="gestion-royaume">
<?php

$W_distance = detection_distance($W_case,$_SESSION["position"]);

$W_coord = convert_in_coord($W_case);
if($W_distance == 0)
{
        //Si on est dans notre royaume
        if($R['diplo'] == 127)
        {
            if($joueur['rang_royaume'] == 6)
            {
                echo '<strong>Stars du royaume</strong> : '.$R['star'].'<br />';
                echo '<strong>Taux de taxe</strong> : '.$R['taxe_base'].'%<br />';
                $W_requete = "SELECT COUNT(*) as count FROM perso WHERE race = '".$R['race']."' AND statut = 'actif'";
                $W_req = $db->query($W_requete);
                $W_row = $db->read_row($W_req);
                echo '<strong>Habitants</strong> : '.$W_row[0].'<br />';
				$semaine = time() - (3600 * 24 * 7);
                $W_requete = "SELECT COUNT(*) as count FROM perso WHERE race = '".$R['race']."' AND level > 3 AND dernier_connexion > ".$semaine." AND statut = 'actif'";
                $W_req = $db->query($W_requete);
                $W_row = $db->read_row($W_req);
                $hta = $W_row[0];
                echo '<strong>Habitants très actifs</strong> : '.$hta.'<br />';
                ?>
                <a href="javascript:envoiInfo('gestion_royaume.php?poscase=<?php echo $W_case; ?>', 'carte')">Retour au menu de gestion du royaume</a><br />
                <hr />
                <?php
                if($_GET['direction'] == 'motk')
                {
	                //Message actuel
	                $requete = "SELECT message FROM motk WHERE id_royaume = ".$R['ID'];
	                $req = $db->query($requete);
	                $row = $db->read_row($req);
					$message = htmlspecialchars(stripslashes($row[0]));
					$message1 = str_replace('[br]', '<br />', $message);
					$message2 = str_replace('[br]', "\n", $message);
	                echo '<h3>Message du roi actuel</h3>
	                '.$message1.'<br />
	                <h3>Modifier</h3>';
	                ?>
					<form method="post" action="javascript:message = document.getElementById('message').value.replace(new RegExp('\n', 'gi'), '[br]'); envoiInfoPost('gestion_royaume.php?poscase=<?php echo $W_case; ?>&amp;direction=motk2&amp;message=' + message, 'carte');">
					<?php
					echo '
		                <textarea name="message" id="message" cols="45" rows="12">'.$message2.'</textarea><br />
						<input type="submit" name="btnSubmit" value="Envoyer" />
					</form>';
                }
                elseif($_GET['direction'] == 'motk2')
                {
					$message = addslashes($_GET['message']);
					if ($message != '')
					{
						$requete = "UPDATE motk SET message = '".$message."', date = ".time()." WHERE id_royaume = ".$R['ID'];
						if($req = $db->query($requete)) 
						{
							echo 'Message du roi bien modifié !<br />';
						}
						else echo('Erreur lors de l\'envoi du message');
					}
					else
					{
						echo 'Vous n\'avez pas saisi de message';
					}
                }
                elseif($_GET['direction'] == 'propagande')
                {
	                //Message actuel
	                $requete = "SELECT propagande FROM motk WHERE id_royaume = ".$R['ID'];
	                $req = $db->query($requete);
	                $row = $db->read_row($req);
					$message = htmlspecialchars(stripslashes($row[0]));
					$message1 = str_replace('[br]', '<br />', $message);
					$message2 = str_replace('[br]', "\n", $message);
	                echo '<h3>Propagande actuelle</h3>
	                '.$message1.'<br />
	                <h3>Modifier</h3>';
	                ?>
					<form method="post" action="javascript:message = document.getElementById('message').value.replace(new RegExp('\n', 'gi'), '[br]'); envoiInfoPost('gestion_royaume.php?poscase=<?php echo $W_case; ?>&amp;direction=propagande2&amp;message=' + message, 'carte');">
					<?php
					echo '
		                <textarea name="message" id="message" cols="45" rows="12">'.$message2.'</textarea><br />
						<input type="submit" name="btnSubmit" value="Envoyer" />
					</form>';
                }
                elseif($_GET['direction'] == 'propagande2')
                {
					$message = addslashes($_GET['message']);
					if ($message != '')
					{
						$requete = "UPDATE motk SET propagande = '".$message."' WHERE id_royaume = ".$R['ID'];
						if($req = $db->query($requete)) 
						{
							echo 'Propagande bien modifiée !<br />';
						}
						else echo('Erreur lors de l\'envoi du message');
					}
					else
					{
						echo 'Vous n\'avez pas saisi de message';
					}
        		}
        		elseif($_GET['direction'] == 'quete')
        		{
	    			?>
	    			<h3>Gestion des Quètes</h3>
	    			<?php
        			if($_GET['action'] == 'achat')
        			{
        				//Récupère les informations sur la quète
        				$requete = "SELECT * FROM quete WHERE id = ".sSQL($_GET['id']);
        				$req = $db->query($requete);
        				$row = $db->read_assoc($req);
        				//Vérifie que le royaume a assez de stars pour l'acheter
        				if($R['star'] >= $row['star_royaume'])
        				{
        					//Ajout de la quète dans la liste des quètes du royaume
        					$requete = "INSERT INTO quete_royaume VALUES('', ".$R['ID'].", ".$row['id'].")";
        					$req = $db->query($requete);
        					//Mis a jour des stars du royaume
        					$requete = "UPDATE royaume SET star = star - ".$row['star_royaume']." WHERE ID = ".$R['ID'];
        					$req = $db->query($requete);
        					echo 'Votre royaume a bien acheté la quète "'.$row['nom'].'"';
        				}
        				else
        				{
        					echo 'Votre royaume n\'a pas assez de stars pour acheter cette quète.';
        				}
        				?>
        				<br /><a href="javascript:envoiInfo('gestion_royaume.php?poscase=<?php echo $W_case; ?>&amp;direction=quete', 'carte')">Retour au menu des quètes</a>
        				<?php
        			}
        			elseif($_GET['action'] == 'voir')
        			{
        				//Récupère les informations sur la quète
        				$requete = "SELECT * FROM quete WHERE id = ".sSQL($_GET['id']);
        				$req = $db->query($requete);
        				$row = $db->read_assoc($req);
        				?>
						<h3 style="margin-bottom : 3px;""><?php echo $row['nom']; ?></h3>
						<span style="font-style : italic;">Niveau conseillé <?php echo $row['lvl_joueur']; ?><br />
						Répétable : <?php if($row['repete'] == 'y') echo 'Oui'; else echo 'Non'; ?><br />
						<?php if($row['mode'] == 'g') echo 'Groupe'; else echo 'Solo'; ?></span><br />
						<br />
						<?php echo nl2br($row['description']); ?>
						<h3>Requis</h3>
						<ul>
							<li>Niveau requis : <?php echo $row['niveau_requis']; ?></li>
							<li>Honneur requis : <?php echo $row['honneur_requis']; ?></li>
							<?php
							if($row['quete_requis'] != '')
							{
								$qrequis = explode(';', $row['quete_requis']);
								foreach($qrequis as $qid)
								{
									$requete = "SELECT nom FROM quete WHERE id = ".$qid;
									$qreq = $db->query($requete);
									$qrow = $db->read_assoc($qreq);
									?>
								<li>Avoir fini la quète : <?php echo $qrow['nom']; ?></li>
									<?php
								}
							}
							?>
						</ul>
						<h3>Récompense</h3>
						<ul>
							<li>Stars : <?php echo $row['star']; ?></li>
							<li>Expérience : <?php echo $row['exp']; ?></li>
							<li>Honneur : <?php echo $row['honneur']; ?></li>
							<li><strong>Objets</strong> :</li>
							<?php
							$rewards = explode(';', $row['reward']);
							$r = 0;
							while($r < count($rewards))
							{
								$reward_exp = explode('-', $rewards[$r]);
								$reward_id = $reward_exp[0];
								$reward_id_objet = mb_substr($reward_id, 1);
								$reward_nb = $reward_exp[1];
								switch($reward_id[0])
								{
									case 'r' :
										$requete = "SELECT * FROM recette WHERE id = ".$reward_id_objet;
										$req_r = $db->query($requete);
										$row_r = $db->read_assoc($req_r);
										echo '<li>Recette de '.$row_r['nom'].' X '.$reward_nb.'</li>';
									break;
									case 'x' :
										echo '<li>Objet aléatoire</li>';
									break;
								}
								$r++;
							}
							?>
						</ul>
						<h3>Cout pour le royaume : <?php echo $row['star_royaume']; ?> stars</h3>
						<br />
						<a href="javascript:envoiInfo('gestion_royaume.php?poscase=<?php echo $W_case; ?>&amp;direction=quete&amp;action=achat&amp;id=<?php echo $row['id']; ?>', 'carte')">Acheter cette quête</a><br />
						<br />
						<a href="javascript:envoiInfo('gestion_royaume.php?poscase=<?php echo $W_case; ?>&amp;direction=quete', 'carte')">Retour à la liste des quêtes</a><br />
						<?php
                    }
                    else
                    {
                        $requete = "SELECT * FROM quete WHERE quete.achat = 'oui' AND id NOT IN (SELECT id_quete FROM quete_royaume WHERE id_royaume = ".$R['ID'].") ORDER BY star_royaume";
                        $req = $db->query($requete);
                    
                    ?>
                            <table>
                            <tr>
                                <td>
                                    Nom
                                </td>
                                <td>
                                    Cout en star
                                </td>
                                <td>
                                    Achat
                                </td>
                            </tr>
                    <?php
                        while($row = $db->read_array($req))
                        {
                            echo '
                            <tr>
                                <td>
                                    '.$row['nom'].'
                                </td>
                                <td>
                                    '.$row['star_royaume'].'
                                </td>
                                <td>
                                    <a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=quete&amp;action=voir&amp;id='.$row['id'].'\', \'carte\')">Détails de la quète</a>
                                </td>
                            </tr>';
                        }
                    ?>
                            </table>
                    <?php
                    }
                }
                elseif($_GET['direction'] == 'taxe')
                {
                    $duree = (60 * 60 * 24) * 7;
                    if((time() - $duree) < $R['taxe_time'])
                    {
                        echo 'Vous avez déjà modifié le taux de taxe récemment.<br />
                        Vous pourrais le modifier dans '.transform_sec_temp(($R['taxe_time'] + $duree) - time());
                    }
                    else
                    {
                        if($_GET['action'] == 'valid')
                        {
                            $requete = "UPDATE royaume SET taxe = ".sSQL($_GET['taux']).", taxe_time = ".time()." WHERE id = ".$R['ID'];
                            if($db->query($requete))
                            {
                                echo 'Taux de taxe modifié !';
                            }
                        }
                        else
                        {
                        ?>
                        <form action="gestion_royaume.php">
                            Modifier le taux de taxe pour : <select name="taux" id="taux">
                            <?php
                                $debut = $R['taxe_base'] - 3;
                                $fin = $R['taxe_base'] + 3;
                                for($i = $debut; $i < $fin; $i++)
                                {
                                    echo '
                                    <option value="'.$i.'">'.$i.' %</option>';
                                }
                            ?>
                            </select>
                            <input type="button" onclick="envoiInfo('gestion_royaume.php?poscase=<?php echo $W_case; ?>&amp;direction=taxe&amp;action=valid&amp;taux=' + document.getElementById('taux').value, 'carte')" value="Ok" />
                        </form>
                        <?php
                        }
                    }
                }
                elseif($_GET['direction'] == 'entretien')
                {
					//Entretien des batiments et constructions
					//On récupère le nombre d'habitants très actifs
					$semaine = time() - (3600 * 24 * 7);
					$royaumes = array();
					$requete = "SELECT race, COUNT(*) as tot FROM perso WHERE level > 3 AND dernier_connexion > ".$semaine." GROUP BY race";
					$req = $db->query($requete);
					while($row = $db->read_row($req))
					{
						$habitants[$row[0]] = $row[1];
					}
					$min_habitants = min($habitants);
					$ratio = $hta / $min_habitants;
					if($ratio < 1) $ratio = 1;
					//PHASE 1, entretien des batiments internes
					//On récupère les couts d'entretiens
					echo '
					<table>
						<tr>
							<td style="vertical-align : top; font : normal 12px arial;">
								<h4>ENTRETIEN DES BATIMENTS INTERNES : (en stars / jour)</h4>
								<ul>';
								$requete = "SELECT *, construction_ville.id as id_const FROM construction_ville RIGHT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE construction_ville.statut = 'actif' AND id_royaume = ".$R['ID'];
								$req = $db->query($requete);
								while($row = $db->read_assoc($req))
								{
									$entretien = ceil($row['entretien'] * $ratio);
									echo '<li>'.$row['nom'].' : '.$entretien.'</li>';
									$royaumes[$row['id_royaume']]['batiments'][$row['id_const']] = $entretien;
									$royaumes[$row['id_royaume']]['total'] += $entretien;
								}
								 echo '
								 </ul>
								 TOTAL : '.$royaumes[$R['ID']]['total'];
								//PHASE 2, entretien des batiments externes
								//On récupère les couts d'entretiens
								$requete = "SELECT *, construction.id AS id_const, batiment.hp AS hp_m, construction.hp AS hp_c FROM batiment RIGHT JOIN construction ON construction.id_batiment = batiment.id WHERE royaume = ".$R['ID'];
								$req = $db->query($requete);
								while($row = $db->read_assoc($req))
								{
									$entretien = ceil($row['entretien'] * $ratio);
									$royaumes[$row['royaume']]['constructions'][$row['id_const']]['entretien'] = $entretien;
									$royaumes[$row['royaume']]['constructions'][$row['id_const']]['max_hp'] = $row['hp_m'];
									$royaumes[$row['royaume']]['constructions'][$row['id_const']]['hp'] = $row['hp_c'];
									$royaumes[$row['royaume']]['total_c'] += $entretien;
								}
								echo '<h4>ENTRETIEN DES BATIMENTS EXTERNES : '.$royaumes[$R['ID']]['total_c'].' STARS / JOUR</h4>
							</td>
							<td style="vertical-align : top; font : normal 12px arial;">
								<h4>RECETTES, RECOLTE DES TAXES (hier)</h4>
								<ul>
								';
								$sources[2] = 'Hotel des ventes';
								$sources[3] = 'Taverne';
								$sources[4] = 'Forgeron';
								$sources[5] = 'Armurerie';
								$sources[6] = 'Alchimiste';
								$sources[7] = 'Enchanteur';
								$sources[8] = 'Ecole de Magie';
								$sources[9] = 'Ecole de Combat';
								$sources[10] = 'Teleportation';
								$sources[11] = 'Monstres';
								$requete = "SELECT ".$R['race']." FROM stat_jeu WHERE date = '".date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")))."'";
								$req = $db->query($requete);
								$row = $db->read_array($req);
								$stats = explode(';', $row[$R['race']]);
								$i = 0;
								$total = 0;
								$count = count($stats);
								while($i < $count)
								{
									if(array_key_exists($i, $sources))
									{
										echo '<li>'.$sources[$i].' : '.$stats[$i].'</li>';
										$total += $stats[$i];
									}
									$i++;
								}
								echo '
								</ul>
								TOTAL : '.$total.'
							</td>
						</tr>
					</table>';
                }
                elseif($_GET['direction'] == 'drapeau')
                {
                    check_case('all');
                    echo '<h3>Liste des drapeaux ennemis sur votre territoire</h3>';
                    $requete = "SELECT *, placement.royaume AS r FROM placement LEFT JOIN map ON map.id = ((placement.y * 1000) + placement.x) WHERE placement.type = 'drapeau' AND placement.royaume != ".$R['ID']." AND map.royaume = ".$R['ID'];
                    $req = $db->query($requete);
                    echo '<table  style="width:100%;">';
                    while($row = $db->read_assoc($req))
                    {
                        $Royaume = get_royaume_info($joueur['race'], $row['r']);
                        echo '
                        <tr>
                        	<td style="width:33%;">
                        		<image src="image/drapeau.gif" style="vertical-align : top;" title="Drapeau" alt="Drapeau" /> '.$row_b['nom'].'
		                        <div style="display: none; z-index: 2; position: absolute; top: 250px; right: 150px; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 150px; padding: 5px;" id="info_'.$row['id'].'">
		                            '.transform_sec_temp($row['fin_placement'] - time()).' avant fin de construction 
		                        </div>
                        	</td>
                        	<td style="width:33%;">
                        		'.$Gtrad[$Royaume['race']].'
                        	</td>
                        	<td style="width:33%;">
                        		X : '.$row['x'].' - Y : '.$row['y'].'
                        	</td>
                        </tr>';
                    }
                    echo '</table>';
                    echo '<h3>Liste de vos drapeaux sur territoire énnemi</h3>';
                    $requete = "SELECT *, map.royaume AS r FROM placement LEFT JOIN map ON map.id = ((placement.y * 1000) + placement.x) WHERE placement.type = 'drapeau' AND placement.royaume = ".$R['ID'];
                    $req = $db->query($requete);
                    echo '<table  style="width:100%;">';
                    while($row = $db->read_assoc($req))
                    {
                        $Royaume = get_royaume_info($joueur['race'], $row['r']);
                        echo '
                        <tr>
                        	<td style="width:33%;">
                        		<span onmousemove="afficheInfo(\'info_'.$row['id'].'\', \'block\', event, \'centre\');" onmouseout="afficheInfo(\'info_'.$row['id'].'\', \'none\', event, \'centre\');"><image src="image/drapeau.gif" style="vertical-align : top;" title="Drapeau" alt="Drapeau" /> '.$row_b['nom'].' chez '.$Gtrad[$Royaume['race']].'</span>
		                        <div style="display: none; z-index: 2; position: absolute; top: 250px; right: 150px; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 150px; padding: 5px;" id="info_'.$row['id'].'">
		                            '.transform_sec_temp($row['fin_placement'] - time()).' avant fin de construction 
		                        </div>
                        	</td>
                        	<td style="width:33%;">
                        		X : '.$row['x'].' - Y : '.$row['y'].'
                        	</td>
                        </tr>';
                    }
                    echo '</table>';
                    echo '<h3>Liste de vos batiments</h3>';
                    $requete = "SELECT * FROM construction WHERE royaume = ".$R['ID'];
                    $req = $db->query($requete);
                    echo '<table  style="width:100%;">';
                    while($row = $db->read_assoc($req))
                    {
                        echo '
                        <tr>
                        	<td style="width:33%;">
                        		<span onmousemove="afficheInfo(\'info_'.$row['id'].'\', \'block\', event, \'centre\');" onmouseout="afficheInfo(\'info_'.$row['id'].'\', \'none\', event, \'centre\');"><image src="image/mini_fortin.png" style="vertical-align : top;" title="'.$row['nom'].'" alt="'.$row['nom'].'" /> '.$row['nom'].'</span> </td><td style="width:33%;"> X : '.$row['x'].' - Y : '.$row['y'].'
                		        <div style="display: none; z-index: 2; position: absolute; top: 250px; right: 150px; background-color:#ffffff; border: 1px solid #000000; font-size:12px; width: 150px; padding: 5px;" id="info_'.$row['id'].'">
        		                    HP - '.$row['hp'].' 
		                        </div>
                        	</td>
                        	<td style="width:33%;">
                        		<a href="javascript:if(confirm(\'Voulez vous supprimer ce '.$row['nom'].' ?\')) envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=suppr_construction&amp;id='.$row['id'].'\', \'carte\')">Supprimer</a>
                        	</td>
                        </tr>';
                        if($row['type'] == 'bourg')
                        {
	                        $bat = recupbatiment($row['id_batiment'], 'none');
	                        //On peut l'upragder
	                        if($bat['nom'] != 'Bourg')
	                        {
		                        $bat_suivant = recupbatiment(($row['id_batiment'] + 1), 'none');
		                        echo ' - <a href="javascript:if(confirm(\'Voulez vous upgrader ce '.$row['nom'].' ?\')) envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=up_construction&amp;id='.$row['id'].'\', \'carte\')">Upgrader - '.$bat_suivant['cout'].' stars</a>';
	                        }
                        }
                    }
                    echo '</table>';
                }
                elseif($_GET['direction'] == 'suppr_construction')
                {
	                $requete = "SELECT type, royaume FROM construction WHERE id = ".sSQL($_GET['id']);
	                $req = $db->query($requete);
	                $row = $db->read_row($req);
	                $requete = "DELETE FROM construction WHERE id = ".sSQL($_GET['id']);
	                if($db->query($requete))
	                {
		                echo 'La construction a été correctement supprimée.';
                    	//On supprime un bourg au compteur
		            	if($row[0] == 'bourg')
        		    	{
	        		    	supprime_bourg($row[1]);
        		    	}
	                }
                    echo '<a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=drapeau\', \'carte\')">Retour à la liste des drapeaux et constructions</a>';
                }
                elseif($_GET['direction'] == 'up_construction')
                {
	                $requete = "SELECT x, y, type, id_batiment, royaume FROM construction WHERE id = ".sSQL($_GET['id']);
	                $req = $db->query($requete);
	                $row = $db->read_assoc($req);
	                $bat = recupbatiment(($row['id_batiment'] + 1), 'none');
	                //Si le royaume a assez de stars
	                if($R['star'] >= $bat['cout'])
	                {
		                //On supprime l'ancien bourg
	                	$requete = "DELETE FROM construction WHERE id = ".sSQL($_GET['id']);
	                	$db->query($requete);
	                	//On place le nouveau
						$requete = "INSERT INTO construction VALUES('', ".$bat['id'].", ".$row['x'].", ".$row['y'].", ".$row['royaume'].", ".$bat['hp_max'].", '".$bat['nom']."', '".$row['type']."', 0, 0, '".$Gtrad[$bat['nom']]."')";
	                	if($db->query($requete))
	                	{
							$requete = "UPDATE royaume SET star = star - ".$bat['cout']." WHERE ID = ".$R['ID'];
							$db->query($requete);
				    	    echo 'La construction a été correctement upgradée.';
	    		    	}
        			}
        		}
        		elseif($_GET['direction'] == 'diplomatie')
        		{
	    			?>
	    			<h3>Diplomatie</h3>
	    			<?php
        			$requete = "SELECT * FROM diplomatie WHERE race = '".$joueur['race']."'";
        			$req = $db->query($requete);
        			$row = $db->read_assoc($req);
        			if($_GET['action'] == 'valid')
        			{
        				if($R['diplo_time'][$_GET['race']] > time())
        				{
        				    echo 'Vous ne pouvez pas changer votre diplomatie avec ce royaume avant : <br />'.transform_sec_temp($R['diplo_time'][$_GET['race']] - time()).'<br />';
        				}
        				else
        				{
        				    //Si modification moins, on envoi la demande à l'autre royaume
        				    if($_GET['diplo'] == 'm')
        				    {
        				        $diplo = $row[$_GET['race']] - 1;
        				        $star = $_GET['star'];
        				        if($star > $R['star']) $star = $R['star'];
        				        //Suppression des stars
        				        $requete = "UPDATE royaume SET star = star - ".$star." WHERE ID = ".$R['ID'];
        				        $db->query($requete);
        				        //Envoi de la demande
        				        $requete = "INSERT INTO diplomatie_demande VALUES(NULL, ".$diplo.", '".$joueur['race']."', '".$_GET['race']."',  ".$star.")";
        				        $db->query($requete);
        				        echo 'Une demande au royaume '.$Gtrad[$_GET['race']].' pour passer en diplomatie : '.$Gtrad['diplo'.$diplo].' en échange de '.$star.' stars a été envoyée<br /><br />';
        				    }
        				    //Sinon, on change la diplomatie.
        				    else
        				    {
        				        $diplo = $row[$_GET['race']] + 1;
        				        $duree = (pow(2, abs(5 - $diplo)) * 60 * 60 * 24);
        				        $prochain_changement = time() + $duree;
        				        //Requète de changement pour ce royaume
        				        $requete = "UPDATE diplomatie SET ".sSQL($_GET['race'])." = ".$diplo." WHERE race = '".$joueur['race']."'";
        				        $db->query($requete);
        				        //Requète de changement pour l'autre royaume
        				        $requete = "UPDATE diplomatie SET ".$joueur['race']." = ".$diplo." WHERE race = '".sSQL($_GET['race'])."'";
        				        $db->query($requete);
        				        $requete = "SELECT diplo_time FROM royaume WHERE race = '".sSQL($_GET['race'])."'";
        				        $req = $db->query($requete);
        				        $row2 = $db->read_assoc($req);
        				        $row2['diplo_time'] = unserialize($row2['diplo_time']);
        				        $row2['diplo_time'][$joueur['race']] = $prochain_changement;
        				        $row2['diplo_time'] = serialize($row2['diplo_time']);
        				        $R['diplo_time'][$_GET['race']] = $prochain_changement;
        				        $R['diplo_time'] = serialize($R['diplo_time']);
        				        $requete = "UPDATE royaume SET diplo_time = '".$row2['diplo_time']."' WHERE race = '".sSQL($_GET['race'])."'";
        				        $db->query($requete);
        				        $requete = "UPDATE royaume SET diplo_time = '".$R['diplo_time']."' WHERE ID = ".$R['ID'];
        				        $db->query($requete);
        				        echo 'Vous êtes maintenant en '.$Gtrad['diplo'.$diplo].' avec les '.$Gtrad[$_GET['race']].'<br /><br />';
        				        //Recherche du roi
        				        $requete = "SELECT ID, nom FROM perso WHERE race = '".sSQL($_GET['race'])."' AND rang_royaume = 6";
        				        $req = $db->query($requete);
        				        $row_roi = $db->read_assoc($req);
        				        //Envoi d'un message au roi
        				        $message = 'Le roi des '.$Gtrad[$joueur['race']].' a changé son attitude diplomatique envers votre royaume en : '.$Gtrad['diplo'.$diplo];
        				        $requete = "INSERT INTO message VALUES('', ".$row_roi['ID'].", 0, 'Mess. Auto', '".$row_roi['nom']."', 'Modification de diplomatie', '".$message."', '', '".time()."', 0)";
        				        $db->query($requete);
        				    }
        				    $requete = "SELECT * FROM diplomatie WHERE race = '".$joueur['race']."'";
        				    $req = $db->query($requete);
        				    $row = $db->read_assoc($req);
        				}
        			}
        			$i = 0;
        			$keys = array_keys($row);
        			$count = count($keys);
        			echo '
        			<table>';
        			while($i < $count)
        			{
        				if($keys[$i] != 'race' AND $row[$keys[$i]] != 127)
        				{
	        				echo '
        			<tr>
        				<td>
							'.$Gtrad[$keys[$i]].'
        				</td>
        				<td style="font-weight : normal;">';
	    					$temps = $R['diplo_time'][$keys[$i]] - time();
	    					if($temps > 0) $show = transform_sec_temp($temps).' avant changement possible';
	    					else $show = 'Modif. Possible';
        					echo $Gtrad['diplo'.$row[$keys[$i]]].'
        				</td>
        				<td>
        					<a style="font-size : 0.8em;" href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=diplomatie&amp;action=modif&amp;race='.$keys[$i].'\', \'carte\')"><span class="xsmall">'.$show.'</span></a>
        				</td>
        			</td>';
        				}
        				$i++;
        			}
        			?>
        			</table>
        			<?php
        			if($_GET['action'] == 'modif')
        			{
        				?>
        				<h3>Modification de la diplomatie avec <?php echo $Gtrad[$_GET['race']]; ?></h3>
        				Changer votre diplomatie pour :<br />
        				<select name="diplo" id="diplo">
        				<?php
        				$diplo = $row[$_GET['race']];
        				if($diplo > 0) $diplom = $diplo - 1;
        				if($diplo < 10) $diplop = $diplo + 1;
        				if(isset($diplom)) echo '<option value="m">'.$Gtrad['diplo'.$diplom].' - Exige l\'accord de l\'autre roi</option>';
        				if(isset($diplop)) echo '<option value="p">'.$Gtrad['diplo'.$diplop].'</option>';
        				?>
        				</select><br />
        				<?php
        				//Si monter de diplo, on peut donner des stars
        				if(isset($diplom))
        				{
	    				    ?>
	    				<span>Vous pouvez donner des stars au royaume destinataire de la demande en échange de son acceptation.<br />
	    				Ces stars seront prise dès l'envoi de la demande.</span>
	    				<input type="text" value="0" name="star" id="star" />
	    					<?php
	    					$href_star = "' + document.getElementById('star').value";
        				}
        				else $href_star = "0'";
        				?>
        				<input type="button" onclick="envoiInfo('gestion_royaume.php?poscase=<?php echo $W_case; ?>&amp;direction=diplomatie&amp;action=valid&amp;race=<?php echo $_GET['race']; ?>&amp;diplo=' + document.getElementById('diplo').value + '&amp;star=<?php echo $href_star; ?>, 'carte')" value="Effectuer le changement diplomatique">
        				<?php
        			}
        		}
        		elseif($_GET['direction'] == 'diplomatie_demande')
        		{
        		    //Recherche de la demande
        		    $requete = "SELECT * FROM diplomatie_demande WHERE id = ".sSQL($_GET['id_demande']);
        		    $req = $db->query($requete);
        		    $row = $db->read_assoc($req);
        		    //Suppression de la demande
        		    $requete = "DELETE FROM diplomatie_demande WHERE id = ".sSQL($_GET['id_demande']);
        		    $db->query($requete);
        		    //Recherche du roi
        		    $requete = "SELECT ID, nom FROM perso WHERE race = '".$row['royaume_demande']."' AND rang_royaume = 6";
        		    $req = $db->query($requete);
        		    $row_roi = $db->read_assoc($req);
        		    if($_GET['reponse'] == 'non')
        		    {
        		        //Envoi d'un message au roi
        		        $message = 'Le roi des '.$Gtrad[$joueur['race']].' a refusé votre demande diplomatique';
        		        $requete = "INSERT INTO message VALUES('', ".$row_roi['ID'].", 0,'Mess. Auto', '".$row_roi['nom']."', 'Refus de diplomatie', '".$message."', '', '".time()."', 0)";
        		        $db->query($requete);
        		        //On redonne les stars
        		        $requete = "UPDATE royaume SET star = star + ".$row['stars']." WHERE race = '".$row['royaume_demande']."'";
        		        $db->query($requete);
        		        echo 'Demande refusée<br />';
        		    }
        		    else
        		    {
        		        $diplo = $row['diplo'];
        		        $duree = (pow(2, abs(5 - $diplo)) * 60 * 60 * 24);
        		        $prochain_changement = time() + $duree;
        		        //Requète de changement pour ce royaume
        		        $requete = "UPDATE diplomatie SET ".$row['royaume_demande']." = ".$diplo." WHERE race = '".$joueur['race']."'";
        		        $db->query($requete);
        		        //On donne les stars au royaume qui recoit
        		        $requete = "UPDATE royaume SET star = star + ".$row['stars']." WHERE race = '".$row['royaume_recois']."'";
        		        $db->query($requete);
        		        //Requète de changement pour l'autre royaume
        		        $requete = "UPDATE diplomatie SET ".$joueur['race']." = ".$diplo." WHERE race = '".$row['royaume_demande']."'";
        		        $db->query($requete);
        		        $requete = "SELECT diplo_time FROM royaume WHERE race = '".$row['royaume_demande']."'";
        		        $req = $db->query($requete);
        		        $row2 = $db->read_assoc($req);
        		        $row2['diplo_time'] = unserialize($row2['diplo_time']);
        		        $row2['diplo_time'][$joueur['race']] = $prochain_changement;
        		        $row2['diplo_time'] = serialize($row2['diplo_time']);
        		        $row3['diplo_time'] = $R['diplo_time'];
        		        $row3['diplo_time'][$row['royaume_demande']] = $prochain_changement;
        		        $row3['diplo_time'] = serialize($row3['diplo_time']);
        		        $requete = "UPDATE royaume SET diplo_time = '".$row2['diplo_time']."' WHERE race = '".$row['royaume_demande']."'";
        		        $db->query($requete);
        		        $requete = "UPDATE royaume SET diplo_time = '".$row3['diplo_time']."' WHERE race = '".$R['race']."'";
        		        $db->query($requete);
        		        echo 'Vous êtes maintenant en '.$Gtrad['diplo'.$diplo].' avec les '.$Gtrad[$row['royaume_demande']].'<br /><br />';
        		        //Envoi d'un message au roi
        		        $message = 'Le roi des '.$Gtrad[$joueur['race']].' a accepté votre demande diplomatique';
        		        $requete = "INSERT INTO message VALUES('', ".$row_roi['ID'].", 0,'Mess. Auto', '".$row_roi['nom']."', 'Accord diplomatique', '".$message."', '', '".time()."', 0)";
        		        $db->query($requete);
        		    }
        		}
        		elseif($_GET['direction'] == 'telephone')
        		{
        		    $requete = "SELECT * FROM perso WHERE rang_royaume = 6 AND ID <> ".$joueur['ID'];
        		    $req = $db->query($requete);
        		    echo '<table class="ville">';
        		    while($row = $db->read_assoc($req))
        		    {
        		        echo '
        		        <tr>
        		        	<td>
        		        		<a href="javascript:envoiInfo(\'envoimessage.php?ID='.$row['ID'].'\', \'information\')">'.$row['nom'].'</a>
        		        	</td>
        		        	<td>
        		        		 - Roi des '.$Gtrad[$row['race']].'
        		        	</td
        		        </td>';
        		    }
        		    echo '</table>';
        		}
        		elseif($_GET['direction'] == 'construction')
        		{
        		    $requete = "SELECT *, construction_ville.id as id_const FROM construction_ville LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE id_royaume = ".$R['ID'];
        		    $req = $db->query($requete);
        		    echo '
        		    <h3>Liste des batiments de la ville :</h3>
        		    <ul class="ville">';
        		    while($row = $db->read_assoc($req))
        		    {
	    		        if($row['statut'] == 'actif')
	    		        {
        		        ?>
        		        <li><?php echo $row['nom']; ?><span class="small">, entretien : <?php echo $row['entretien']; ?> <a href="javascript:envoiInfo('gestion_royaume.php?poscase=<?php echo $W_case; ?>&amp;direction=amelioration&amp;action=list&amp;batiment=<?php echo $row['type']; ?>', 'carte')">Améliorer</a></li>
        		        <?php
        		    	}
        		    	else
        		    	{
        		        ?>
        		        <li><?php echo $row['nom']; ?><span class="small">, inactif <a href="javascript:if(confirm('Voulez vous vraiment réactiver cette construction ?')) envoiInfo('gestion_royaume.php?poscase=<?php echo $W_case; ?>&amp;direction=reactif&amp;action=list&amp;batiment=<?php echo $row['id_const']; ?>', 'carte')">Réactiver pour <?php echo $row['dette']; ?> stars</a></li>
        		        <?php
        		    	}
        		    }
        		    echo '</ul>';
        		}
        		elseif($_GET['direction'] == 'reactif')
        		{
        		    $id_batiment = $_GET['batiment'];
        		    $requete = "SELECT * FROM construction_ville WHERE id = ".$id_batiment;
        		    $req = $db->query($requete);
        		    $row = $db->read_assoc($req);
        		    if($R['star'] >= $row['dette'])
        		    {
	    		        $requete = "UPDATE construction_ville SET statut = 'actif', dette = 0 WHERE id = ".$id_batiment;
	    		        $db->query($requete);
	    		        $requete = "UPDATE royaume SET star = star - ".$row['dette']." WHERE ID = ".$R['ID'];
	    		        if($db->query($requete)) echo 'Batiment bien réactivé.';
        		    }
        		    else
        		    {
	    		        echo 'Vous n\'avez pas assez de stars pour réactiver cette construction !';
        		    }
        		}
        		elseif($_GET['direction'] == 'amelioration')
        		{
        		    $type = $_GET['batiment'];
        		    $action = $_GET['action'];
        		    $requete = "SELECT *, construction_ville.id AS id_batiment_ville FROM construction_ville LEFT JOIN batiment_ville ON construction_ville.id_batiment = batiment_ville.id WHERE id_royaume = ".$R['ID']." AND batiment_ville.type = '".$type."'";
        		    $req = $db->query($requete);
        		    $row = $db->read_assoc($req);
        		    $id_batiment_ville = $row['id_batiment_ville'];
        		    switch($action)
        		    {
        		        case 'list' :
        		            ?>
        		            Actuellement vous possédez : <?php echo $row['nom']; ?><br />
        		            Vous pouvez l'améliorer en :
        		            <ul class="ville">
        		            <?php
        		            $requete = "SELECT * FROM batiment_ville WHERE level > ".$row['level']." AND type = '".$type."'";
        		            $req = $db->query($requete);
        		            while($row = $db->read_assoc($req))
        		            {
        		                ?>
        		                <li><?php echo $row['nom']; ?>, coût : <?php echo $row['cout']; ?>, entretien par jour : <?php echo $row['entretien']; ?> <a href="javascript:envoiInfo('gestion_royaume.php?poscase=<?php echo $W_case; ?>&amp;direction=amelioration&amp;action=ameliore&amp;batiment=<?php echo $row['type']; ?>&amp;id_batiment=<?php echo $row['id']; ?>', 'carte')">Améliorer</a></li>
        		                <?php
        		            }
        		            ?>
        		            </ul>
        		            <?php
        		        break;
        		        case 'ameliore' :
        		            $id_batiment = $_GET['id_batiment'];
        		            $requete = "SELECT * FROM batiment_ville WHERE id = ".$id_batiment;
        		            $req = $db->query($requete);
        		            $row = $db->read_assoc($req);
        		            //Si le royaume a assez de stars on achète le batiment
        		            if($R['star'] >= $row['cout'])
        		            {
        		                //On paye
        		                $R['star'] = $R['star'] - $row['cout'];
        		                $requete = "UPDATE royaume SET star = ".$R['star']." WHERE ID = ".$R['ID'];
        		                $db->query($requete);
        		                //On ajoute le batiment et on supprime l'ancien
        		                $requete = "DELETE FROM construction_ville WHERE id = ".$id_batiment_ville;
        		                $db->query($requete);
        		                $requete = "INSERT INTO construction_ville VALUES ('', ".$R['ID'].", ".$id_batiment.", 'actif', '')";
        		                if($db->query($requete))
        		                {
        		                    echo $row['nom'].' bien acheté.';
        		                }
        		            }
        		            else
        		            {
        		                echo 'Le royaume ne possède pas assez de stars';
        		            }
        		        break;
        		    }
        		}
        		elseif($_GET['direction'] == 'carte')
        		{
        			echo '<img src="carte_roy2.php?url='.$joueur['race'].'" />';
        		
        		}
        		elseif($_GET['direction'] == 'stats')
        		{
	    		    //Statistiques du royaume
	    		    $requete = "SELECT *, COUNT(*) as tot FROM perso WHERE race = '".$joueur['race']."' AND statut = 'actif' GROUP BY classe ORDER BY tot";
	    		    $req = $db->query($requete);
	    		    ?>
	    		    <h3>Nombre de joueurs de votre race par classe</h3>
	    		    <table>
	    		    <tr>
	    		    	<td>
	    		    		Classe
	    		    	</td>
	    		    	<td>
	    		    		Nombre
	    		    	</td>
	    		    </tr>
	    		    <?php
	    		    while($row = $db->read_array($req))
	    		    {
				        echo '
				        <tr>
				        	<td>
				        	'.$row['classe'].'
				        	</td>
				        	<td>
				        	'.$row['tot'].'
				        	</td>
				        </tr>'; 
	    		    }
	    		    ?>
	    		    </table>
	    		    <?php
	    		    $requete = "SELECT nom, melee FROM perso WHERE race = '".$joueur['race']."' AND statut = 'actif' ORDER BY melee DESC LIMIT 0, 5";
	    		    $req = $db->query($requete);
	    		    ?>
	    		    <h3>Meilleurs guerriers</h3>
	    		    <table>
	    		    <tr>
	    		    	<td>
	    		    		Nom
	    		    	</td>
	    		    	<td>
	    		    		Mélée
	    		    	</td>
	    		    </tr>
	    		    <?php
	    		    while($row = $db->read_array($req))
	    		    {
				        echo '
				        <tr>
				        	<td>
				        	'.$row['nom'].'
				        	</td>
				        	<td>
				        	'.$row['melee'].'
				        	</td>
				        </tr>'; 
	    		    }
	    		    ?>
	    		    </table>
	    		    <?php
	    		    $requete = "SELECT nom, distance FROM perso WHERE race = '".$joueur['race']."' AND statut = 'actif' ORDER BY distance DESC LIMIT 0, 5";
	    		    $req = $db->query($requete);
	    		    ?>
	    		    <h3>Meilleurs Archers</h3>
	    		    <table>
	    		    <tr>
	    		    	<td>
	    		    		Nom
	    		    	</td>
	    		    	<td>
	    		    		Tir à distance
	    		    	</td>
	    		    </tr>
	    		    <?php
	    		    while($row = $db->read_array($req))
	    		    {
				        echo '
				        <tr>
				        	<td>
				        	'.$row['nom'].'
				        	</td>
				        	<td>
				        	'.$row['distance'].'
				        	</td>
				        </tr>'; 
	    		    }
	    		    ?>
	    		    </table>
	    		    <?php
	    		    $requete = "SELECT nom, esquive FROM perso WHERE race = '".$joueur['race']."' AND statut = 'actif' ORDER BY esquive DESC LIMIT 0, 5";
	    		    $req = $db->query($requete);
	    		    ?>
	    		    <h3>Meilleurs esquiveurs</h3>
	    		    <table>
	    		    <tr>
	    		    	<td>
	    		    		Nom
	    		    	</td>
	    		    	<td>
	    		    		Esquive
	    		    	</td>
	    		    </tr>
	    		    <?php
	    		    while($row = $db->read_array($req))
	    		    {
				        echo '
				        <tr>
				        	<td>
				        	'.$row['nom'].'
				        	</td>
				        	<td>
				        	'.$row['esquive'].'
				        	</td>
				        </tr>'; 
	    		    }
	    		    ?>
	    		    </table>
	    		    <?php
	    		    $requete = "SELECT nom, incantation FROM perso WHERE race = '".$joueur['race']."' AND statut = 'actif' ORDER BY incantation DESC LIMIT 0, 5";
	    		    $req = $db->query($requete);
	    		    ?>
	    		    <h3>Meilleurs mages</h3>
	    		    <table>
	    		    <tr>
	    		    	<td>
	    		    		Nom
	    		    	</td>
	    		    	<td>
	    		    		Incantation
	    		    	</td>
	    		    </tr>
	    		    <?php
	    		    while($row = $db->read_array($req))
	    		    {
				        echo '
				        <tr>
				        	<td>
				        	'.$row['nom'].'
				        	</td>
				        	<td>
				        	'.$row['incantation'].'
				        	</td>
				        </tr>'; 
	    		    }
	    		    ?>
	    		    </table>
	    		    <?php
        		}
        		elseif($_GET['direction'] == 'criminel')
        		{
	    		    //Sélection de tous les joueurs ayant des points de crime
	    		    $requete = "SELECT * FROM perso WHERE crime > 0 AND race = '".$R['race']."' AND statut = 'actif' ORDER BY crime DESC";
	    		    $req = $db->query($requete);
	    		    ?>
	    		    <table>
	    		    <tr>
	    		    	<td>
	    		    		Nom
	    		    	</td>
	    		    	<td>
	    		    		Pts de crime
	    		    	</td>
	    		    	<td>
	    		    		Amende
	    		    	</td>
	    		    	<td>
	    		    	</td>
	    		    </tr>
	    		    <?php
	    		    while($row = $db->read_assoc($req))
	    		    {
				        if($row['amende'] > 0)
				        {
				            $requete = "SELECT montant FROM amende WHERE id = ".$row['amende'];
				            $req_a = $db->query($requete);
				            $row_a = $db->read_row($req_a);
				            $amende = $row_a[0];
				        }
				        else $amende = 0;
				        ?>
	    		    <tr>
	    		    	<td>
	    		    		<?php echo $row['nom']; ?>
	    		    	</td>
	    		    	<td>
	    		    		<?php echo $row['crime']; ?>
	    		    	</td>
	    		    	<td>
	    		    		<?php echo $amende; ?>
	    		    	</td>
	    		    	<td>
	    		    		<a href="javascript:envoiInfo('gestion_royaume.php?poscase=<?php echo $W_case; ?>&amp;direction=gestion_criminel&amp;id=<?php echo $row['ID']; ?>', 'carte')">Gérer</a>
	    		    		<?php
	    		    		if($amende != 0)
	    		    		{
				        		?>
				        		/ <a href="javascript:envoiInfo('gestion_royaume.php?poscase=<?php echo $W_case; ?>&amp;direction=suppr_criminel&amp;id=<?php echo $row['ID']; ?>', 'carte')">Supprimer</a>
				        		<?php
	    		    		}
	    		    		?>
	    		    	</td>
	    		    </tr>
	    		    	<?php
	    		    }
	    		    ?>
	    		    </table>
	    		    <?php
        		}
        		elseif($_GET['direction'] == 'suppr_criminel')
        		{
	    		    $amende = recup_amende($_GET['id']);
					//On supprime l'amende du joueur
					$requete = "UPDATE perso SET amende = 0 WHERE ID = ".sSQL($_GET['id']);
					$db->query($requete);
					$requete = "DELETE FROM amende WHERE id = ".$amende['id'];
					$db->query($requete);
					echo 'Amende bien supprimée.';
        		}
        		elseif($_GET['direction'] == 'gestion_criminel')
        		{
	    		    $joueur = recupperso($_GET['id']);
	    		    //Récupère l'amende
	    		    $amende = recup_amende($_GET['id']);
	    		    $amende_max = ($joueur['crime'] * $joueur['crime']) * 10;
	    		    $etats = array('normal');
	    		    if($joueur['crime'] > 30) $etats[] = 'bandit';
	    		    if($joueur['crime'] > 60) $etats[] = 'criminel';
	    		    //Si il en a pas
	    		    if(!$amende)
	    		    {
				        ?>
				        <form method="post" action="javascript:envoiInfoPost('gestion_royaume.php?poscase=<?php echo $W_case; ?>&amp;direction=gestion_criminel2&amp;id=<?php echo $joueur['ID']; ?>&amp;acces_ville=' + document.getElementById('acces_ville').checked + '&amp;spawn_ville=' + document.getElementById('spawn_ville').checked + '&amp;statut=' + document.getElementById('statut').value + '&amp;montant=' + document.getElementById('montant').value, 'carte');">
				        	<input type="checkbox" name="acces_ville" id="acces_ville" /> Empèche le joueur d'accéder à la ville<br />
				        	<input type="checkbox" name="spawn_ville" id="spawn_ville" <?php if($joueur['crime'] > 30) echo 'disabled'; ?> /> Empèche de renaître à la ville<br />
				        	<br />
				        	Statut du personnage <select name="statut" id="statut">
				        	<?php
				        	foreach($etats as $etat)
				        	{
				            	?>
				            	<option value="<?php echo $etat; ?>"><?php echo $etat; ?></option>
				            	<?php
				        	}
				        	?>
				        	</select><br />
				        	<br />
				        	 Montant de l'amende (max : <?php echo $amende_max; ?>) <input type="text" name="montant" id="montant" /><br />
				        	 <br />
				        	 <input type="submit" value="Valider cette amende" />
				        </form>
				        <?php
	    		    }
        		}
        		elseif($_GET['direction'] == 'gestion_criminel2')
        		{
	    		    $joueur = recupperso($_GET['id']);
	    		    //Récupère l'amende
	    		    $amende = recup_amende($_GET['id']);
	    		    $amende_max = ($joueur['crime'] * $joueur['crime']) * 10;
	    		    //Vérification d'usage
	    		    if($_GET['montant'] > 0)
	    		    {
				        if($_GET['montant'] <= $amende_max)
				        { 
				        	if($_GET['spawn_ville'] == 'true') $spawn_ville = 'y'; else $spawn_ville = 'n';
				        	if($_GET['acces_ville'] == 'true') $acces_ville = 'y'; else $acces_ville = 'n';
				        	//Inscription de l'amende dans la bdd
				        	$requete = "INSERT INTO amende(id, id_joueur, id_royaume, montant, acces_ville, respawn_ville, statut) VALUES ('', ".$joueur['ID'].", ".$Trace[$joueur['race']]['numrace'].", ".sSQL($_GET['montant']).", '".$acces_ville."', '".$spawn_ville."', '".sSQL($_GET['statut'])."')";
				        	if($db->query($requete))
				        	{
				            	$amende = recup_amende($joueur['ID']);
				            	$requete = "UPDATE perso SET amende = ".$amende['id']." WHERE ID = ".$joueur['ID'];
				            	if($db->query($requete)) echo 'Amende bien prise en compte !';
				        	}
	    		    	}
	    		    	else
	    		    	{
				        	echo 'Le montant de l\'amende est trop élevé';
	    		    	}
	    		    }
        		}
        		else
        		{
        		    echo '
					<h3>Politique Extérieure</h3>
					<table style="width : 100%;">
					<tr style="width : 100%;">
						<td style="width : 50%; vertical-align : top;">
							<h4>Diplomatie</h4>
							<ul class="gestion-royaume">
								<li><a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=diplomatie\', \'carte\')">Diplomatie</a>
								<li><a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=telephone\', \'carte\')">Téléphone rouge</a>
							</ul>
						</td>
						<td style="width : 50%; vertical-align : top;">
							<h4>Militaire</h4>
							<ul class="gestion-royaume">
								<li><a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=drapeau\', \'carte\')">Drapeaux & batiments</a>
								<li><a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=carte\', \'carte\')">Carte des constructions et habitants</a>
							</ul>
						</td>
					</tr>
					</table>
					<h3>Politique Intérieure</h3>
					<table style="width : 100%;">
					<tr style="width : 100%;">
						<td style="width : 50%; vertical-align : top;">
							<h4>Economie</h4>
							<ul class="gestion-royaume">
								<li><a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=construction\', \'carte\')">Construction de la ville</a>
								<li><a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=entretien\', \'carte\')">Entretien</a>
								<li><a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=quete\', \'carte\')">Gestion des quètes</a>
								<li><a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=taxe\', \'carte\')">Gestion des taxes</a>
							</ul>
						</td>
						<td style="width : 50%; vertical-align : top;">
							<h4>Divers</h4>
							<ul class="gestion-royaume">
								<li><a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=criminel\', \'carte\')">Criminels</a>
								<li><a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=motk\', \'carte\')">Message du roi</a>
								<li><a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=propagande\', \'carte\')">Propagande</a>
								<li><a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=stats\', \'carte\')">Statistiques</a>
							</ul>
						</td>
					</tr>
					</table>
        		    ';
        		}
        		$requete = "SELECT * FROM diplomatie_demande WHERE royaume_recois = '".$joueur['race']."'";
        		$req = $db->query($requete);
        		if($db->num_rows > 0)
        		{
        		    echo '<h3>Demande(s) diplomatiques</h3>
        		    <ul>';
        		    while($row = $db->read_assoc($req))
        		    {
        		        echo '
        		        <li>
        		            Le roi '.$Gtrad[$row['royaume_demande']].' vous demande de passer en diplomatie et vous donne '.$star.' : '.$Gtrad['diplo'.$row['diplo']].'<br />
        		            Accépter ? <a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=diplomatie_demande&amp;reponse=oui&amp;id_demande='.$row['id'].'\', \'carte\');">Oui</a> / <a href="javascript:envoiInfo(\'gestion_royaume.php?poscase='.$W_case.'&amp;direction=diplomatie_demande&amp;reponse=non&amp;id_demande='.$row['id'].'\', \'carte\');">Non</a>
        		        </li>';
        		    }
        		    ?>
        		    </ul>
        		    <?php
        		}
            }
        }
}
?>
</div>
</div>
