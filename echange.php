<?php
include('inc/fp.php');
echo '<h2>Effectuer un échange</h2>';
$joueur = recupperso($_SESSION['ID']);
//Si un identifiant d'echange est passé alors on récupère les infos sur cet échange
if(array_key_exists('id_echange', $_GET))
{
	$echange = recup_echange($_GET['id_echange']);
	$receveur = recupperso_essentiel($echange['id_j2']);
	//Vérification si le joueur fait parti du donneur ou receveur
	if($joueur['ID'] != $echange['id_j1'] AND $joueur['ID'] != $echange['id_j2'])
	{
		?>
		Vous ne faîtes pas parti de cet échange...
		<?php
		exit();
	}
	else
	{
		$j1 = recupperso_essentiel($echange['id_j1']);
		$j2 = recupperso_essentiel($echange['id_j2']);
	}
}
//Sinon c'est le début d'un echange
else
{
	$W_ID = $_GET['id_joueur'];
	$receveur = recupperso_essentiel($W_ID);
	$j1 = recupperso_essentiel($joueur['ID']);
	$j2 = recupperso_essentiel($W_ID);
}



//Si on commence un nouvel échange
if(array_key_exists('nouvel_echange', $_GET))
{
	//On créé l'échange
	$requete = "INSERT INTO echange(id_j1, id_j2, statut, date_debut, date_fin) VALUES(".$joueur['ID'].", ".$receveur['ID'].", 'creation', ".time().", ".(time() + 100000).")";
	$db->query($requete);
	$echange = recup_echange($db->last_insert_id());
}

//Si début d'un echange
if(!isset($echange))
{
	$W_ID = $_GET['id_joueur'];
	$receveur = recupperso_essentiel($W_ID);
	echo '<div class="information_case">';
	//On demande au joueurs si il veut faire un échange ou en récupérer un ancien
	$echanges = recup_echange_perso($joueur['ID'], $receveur['ID']);
	//Il y a déjà eu des échanges
	if(count($echanges) > 0)
	{
		//Listing des échanges
		?>
			<ul>
		<?php
		foreach($echanges as $echange_liste)
		{
			?>
				<li><a href="javascript:envoiInfo('echange.php?id_echange=<?php echo $echange_liste['id_echange']; ?>', 'information');">Echange ID : <?php echo $echange_liste['id_echange']; ?> - <?php echo $echange_liste['statut']; ?></a></li>
			<?php
		}
		?>
					</ul>
					<br />
					<a href="javascript:envoiInfo('echange.php?id_joueur=<?php echo $W_ID; ?>&amp;nouvel_echange=true', 'information');">Débuter un nouvel échange avec ce joueur.</a>
				</div>

		<?php
	}
	//Sinon on lui demande si il veut en créer un nouveau
	else
	{
		?>
			Vous n'avez actuellement aucun échange en cours avec ce joueur.<br />
			<br />
			<a href="javascript:envoiInfo('echange.php?id_joueur=<?php echo $W_ID; ?>&amp;nouvel_echange=true', 'information');">Débuter un nouvel échange avec ce joueur.</a>
		</div>
		<?php
	}

}

//Validation d'étapes
if(array_key_exists('valid_etape', $_GET))
{
	switch($echange['statut'])
	{
		case 'creation' :
			//Ajout des stars dans la bdd
			if(echange_objet_ajout($_GET['star'], 'star', $echange['id_echange'], $joueur['ID']))
			{
				$echange = recup_echange($echange['id_echange']);
			}
			//On passe l'échange en mode proposition
			$requete = "UPDATE echange SET statut = 'proposition' WHERE id_echange = ".sSQL($_GET['id_echange']);
			if($db->query($requete))
			{
				//On envoi un message au gars
				$titre = $joueur['nom'].' vous propose un échange';
				$message = mysql_escape_string($joueur['nom'].' vous propose un échange[br]
				Pour voir ce qu\'il vous propose cliquez ici : [echange:'.$_GET['id_echange'].']');
				$requete = "INSERT INTO message VALUES('', ".$receveur['ID'].", ".$joueur['ID'].", '".$joueur['nom']."', '".$receveur['nom']."', '".$titre."', '".$message."', '', '".time()."', 0)";
				$req = $db->query($requete);
				//C'est ok
				echo '<h6>Votre proposition a bien été envoyée</h6>';
				
				unset($echange);
			}
		break;
		case 'proposition' :
			//Ajout des stars dans la bdd
			if(echange_objet_ajout($_GET['star'], 'star', $echange['id_echange'], $joueur['ID']))
			{
				$echange = recup_echange($echange['id_echange']);
			}
			//On passe l'échange en mode finalisation
			$requete = "UPDATE echange SET statut = 'finalisation' WHERE id_echange = ".sSQL($_GET['id_echange']);
			if($db->query($requete))
			{
				//On envoi un message au gars <== a faire ==>
				
				//C'est ok
				echo '<h6>Votre proposition a bien été envoyée</h6>';
				unset($echange);
			}
		break;
		case 'finalisation' :
			//Finalisation de l'échange donc vérifications
			//Les joueurs doivent être a moins d'une case l'un de l'autre
			$pos1 = convert_in_pos($j1['x'], $j1['y']);
			$pos2 = convert_in_pos($j2['x'], $j2['y']);
			$j1 = recupperso($j1['ID']);
			$j2 = recupperso($j2['ID']);
			if(detection_distance($pos1, $pos2) > 1)
			{
				echo '<h6>Vous êtes trop loin pour finaliser cette échange</h6>';
			}
			//Vérification que les joueurs ont bien les objets dans leur inventaire
			else
			{
				if(verif_echange($_GET['id_echange'], $j1['ID'], $j2['ID']))
				{
					$check = true;
					//Vérification qu'ils ont bien assez de place
					if($G_place_inventaire - count($j1['inventaire_slot']) < ($nb_objet['j2'] - $nb_objet['j1']))
					{
						$check = false;
						echo '<h5>'.$j1['nom'].' n\'a pas assez de place dans son inventaire</h5>';
					}
					if($G_place_inventaire - count($j2['inventaire_slot']) < ($nb_objet['j1'] - $nb_objet['j2']))
					{
						$check = false;
						echo '<h5>'.$j2['nom'].' n\'a pas assez de place dans son inventaire</h5>';
					}
					if($check)
					{
						//On supprime tous les objets
						$i = 0;
						$count = count($echange['objet']);
						while($i < $count)
						{
							if($j1['ID'] == $echange['objet'][$i]['id_j']) $j = 'j1'; else $j = 'j2';
							supprime_objet($$j, $echange['objet'][$i]['objet'], 1);
							$$j = recupperso($echange['objet'][$i]['id_j']);
							$i++;
						}
						//On donne tous les objets
						$i = 0;
						$count = count($echange['objet']);
						while($i < $count)
						{
							if($j1['ID'] == $echange['objet'][$i]['id_j']) $j = 'j2'; else $j = 'j1';
							prend_objet($echange['objet'][$i]['objet'], $$j);
							$$j = recupperso(${$j}['ID']);
							$i++;
						}
						//On échange les stars
						$star['j1'] = intval($echange['star'][$j1['ID']]['objet']);
						$star['j2'] = intval($echange['star'][$j2['ID']]['objet']);
						$j1star = $star['j1'] - $star['j2'];
						$j2star = $star['j2'] - $star['j1'];
						$requete = "UPDATE perso SET star = star - ".$j1star." WHERE ID = ".$j1['ID'];
						$db->query($requete);
						$requete = "UPDATE perso SET star = star - ".$j2star." WHERE ID = ".$j2['ID'];
						$db->query($requete);
						//On met a jour le statut de l'échange
						//On passe l'échange en mode fini
						$requete = "UPDATE echange SET statut = 'fini' WHERE id_echange = ".sSQL($_GET['id_echange']);
						if($db->query($requete))
						{
							//On envoi un message au gars <== a faire ==>
							
							//C'est ok
							echo '<h6>L\'échange c\'est déroulé avec succès</h6>';
							unset($echange);
						}
					}
				}
				else
				{
					echo '<h5>Il manque un ou plusieurs objets a un joueur pour finaliser l\'échange</h5>';
				}
			}
		break;
	}
}

//Ajout d'un objet a l'échange en cours
if(array_key_exists('ajout_objet', $_GET))
{
	//Ajout de l'objet dans la bdd
	if(echange_objet_ajout($_GET['ajout_objet'], 'objet', $echange['id_echange'], $joueur['ID']))
	{
		$echange = recup_echange($echange['id_echange']);
	}
}

//Suppression d'un objet a l'échange en cours
if(array_key_exists('suppr_objet', $_GET))
{
	//Ajout de l'objet dans la bdd
	if(echange_objet_suppr($_GET['suppr_objet']))
	{
		array_splice($echange['objet'], $_GET['index_objet'], 1);
	}
}

if(isset($echange))
{
?>
<h3>Echange avec <?php echo $receveur['nom']; ?> - N° : <?php echo $echange['id_echange']; ?> - <?php echo $echange['statut']; ?></h3>
<div class="information_case">
<?php
	if(($echange['statut'] == 'proposition') OR ($echange['statut'] == 'finalisation'))
	{
		?>
		Proposition de <?php echo $j1['nom']; ?> :
		<div>
		Stars : <?php echo $echange['star'][$j1['ID']]['objet']; ?><br />
		Objets :
		<ul>
			<?php
			if(is_array($echange['objet']))
			{
				$i = 0;
				$keys = array_keys($echange['objet']);
				$count = count($echange['objet']);
				while($i < $count)
				{
					if($echange['objet'][$keys[$i]]['type'] == 'objet' AND $echange['objet'][$keys[$i]]['id_j'] == $j1['ID'])
					{
					?>
					<li><?php echo nom_objet($echange['objet'][$keys[$i]]['objet']); ?></li>
					<?php
					}
					$i++;
				}
			}
			?>
		</ul>
		</div>
		<?php
	}
	if($echange['statut'] == 'finalisation')
	{
		?>
		Proposition de <?php echo $j2['nom']; ?> :
		<div>
		Stars : <?php echo $echange['star'][$j2['ID']]['objet']; ?><br />
		Objets :
		<ul>
			<?php
			$i = 0;
			if(is_array($echange['objet']))
			{
				$keys = array_keys($echange['objet']);
				$count = count($echange['objet']);
				while($i < $count)
				{
					if($echange['objet'][$keys[$i]]['type'] == 'objet' AND $echange['objet'][$keys[$i]]['id_j'] == $j2['ID'])
					{
					?>
					<li><?php echo nom_objet($echange['objet'][$keys[$i]]['objet']); ?></li>
					<?php
					}
					$i++;
				}
			}
			?>
		</ul>
		</div>
		<?php
		if($echange['id_j1'] == $joueur['ID'])
		{
		?>
		<input type="button" value="Finir l'échange" onclick="envoiInfo('echange.php?id_echange=<?php echo $echange['id_echange']; ?>&amp;valid_etape=true', 'information');" />
		<?php
		}
	}
	elseif(($echange['statut'] == 'creation' AND $echange['id_j1'] == $joueur['ID']) OR ($echange['statut'] == 'proposition' AND $echange['id_j2'] == $joueur['ID']))
	{
		$j1['bonus'] = recup_bonus($j1['ID']);
		$j2['bonus'] = recup_bonus($j2['ID']);
		$echange_star = false;
		$echange_objet = false;
		//Si mode création alors on check pour j1 peut donner et j2 peut recevoir
		if($echange['statut'] == 'creation')
		{
			if(array_key_exists(3, $j1['bonus']) AND array_key_exists(1, $j2['bonus'])) $echange_star = true;
			if(array_key_exists(4, $j1['bonus']) AND array_key_exists(2, $j2['bonus'])) $echange_objet = true;
		}
		elseif($echange['statut'] == 'proposition')
		{
			if(array_key_exists(3, $j2['bonus']) AND array_key_exists(1, $j1['bonus'])) $echange_star = true;
			if(array_key_exists(4, $j2['bonus']) AND array_key_exists(2, $j1['bonus'])) $echange_objet = true;
		}
	?>
Vous proposez :
<div>
	<form method="post" action="envoiInfoPostData('echange.php?direction=motk2&amp', 'information', 'message=' + message);">
		Stars : <input type="text" name="star" id="star" value="0" <?php if(!$echange_star) echo 'disabled="true"'; ?> /><br />
		Objets :
		<ul>
			<?php
			if(is_array($echange['objet']))
			{
				$i = 0;
				$keys = array_keys($echange['objet']);
				$count = count($echange['objet']);
				while($i < $count)
				{
					if($echange['objet'][$keys[$i]]['type'] == 'objet' AND $echange['objet'][$keys[$i]]['id_j'] == $joueur['ID'])
					{
					?>
					<li><?php echo nom_objet($echange['objet'][$keys[$i]]['objet']); ?> <a href="javascript:envoiInfo('echange.php?id_echange=<?php echo $echange['id_echange']; ?>&amp;suppr_objet=<?php echo $echange['objet'][$keys[$i]]['id_echange_objet']; ?>&amp;index_objet=<?php echo $keys[$i]; ?>', 'information');">X</a></li>
					<?php
					}
					$i++;
				}
			}
			?>
		</ul>
			<?php
			if($echange_objet)
			{
				$options = '';
				//On affiche la liste des objets échangeables par ce joueur
				if($joueur['inventaire_slot'] != '')
				{
					foreach($joueur['inventaire_slot'] as $invent)
					{
						if($invent !== 0 AND $invent != '')
						{
							$objet_d = decompose_objet($invent);
							//Si ca n'est pas un objet non identifié ou un objet de royaume
							$check = true;
							if($objet_d['categorie'] != 'r' AND $objet_d['id'][0] != 'h' AND $check)
							{
								$options .= '
				<option value="'.$objet_d['sans_stack'].'">'.nom_objet($objet_d['id']).'</option>
								';
							}
						}
					}
				}
				if($options != '')
				{
				?>
				<select name="objet" id="objet">
				<?php echo $options; ?>
				</select>
				<input type="button" value="Ajouter" onclick="envoiInfo('echange.php?id_echange=<?php echo $echange['id_echange']; ?>&amp;ajout_objet=' + document.getElementById('objet').value, 'information');"><br />
				<?php
				}
			}
			?>
				<br />
				<input type="button" value="Proposer ces éléments" onclick="envoiInfo('echange.php?id_echange=<?php echo $echange['id_echange']; ?>&amp;valid_etape=true&amp;star=' + document.getElementById('star').value, 'information');" />
	</form>
</div>
<?php
	}
	elseif($echange['statut'] == 'creation' AND $echange['id_j2'] == $joueur['ID'])
	{
		echo 'Un échange est en train d\'être créé';
	}
	elseif($echange['statut'] == 'proposition' AND $echange['id_j1'] == $joueur['ID'])
	{
		echo 'Votre proposition est étudié par le joueur';
	}
}
echo '</div>';
if($echange['statut'] != 'annule' AND isset($echange))
{
?>
<div class="information_case"><input type="button" onclick="if(confirm('Voulez vous supprimer cet échange ?')) envoiInfo('liste_echange.php?id_echange=<?php echo $echange['id_echange']; ?>&amp;annule=ok', 'information');" value="Supprimer l'échange" /></div>
<?php
}
?>