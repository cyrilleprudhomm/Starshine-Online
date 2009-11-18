<?php
if (file_exists('../root.php'))
  include_once('../root.php');


//Vérification si une quête est finie ou non
function verif_quete($id_quete, $id_quete_joueur, $joueur)
{
	global $db;	
	$requete = "SELECT * FROM quete WHERE id = ".$id_quete;
	$req = $db->query($requete);
	$row = $db->read_array($req);
	$objectif = unserialize($row['objectif']);
	$i = 0;
	$valid = true;
	$liste_quete = $joueur->get_liste_quete();
	while(($i < count($objectif) AND $valid))
	{
		if($liste_quete[$id_quete_joueur]['objectif'][$i]->nombre < $objectif[$i]->nombre)
		{
			$valid = false;
		}
		$i++;
	}
	return $valid;
}

//Verifie si l'action fait avancer une quète
function verif_action($type_cible, $joueur, $mode)
{
	global $db;
	$i = 0;
	$liste_quete = $joueur->get_liste_quete();
	$count = count($liste_quete);
	if($liste_quete != '')
	{
		$echo = '';
		while($i < $count && array_key_exists('id_quete', $liste_quete[$i]))
		{
			if ($liste_quete[$i]['id_quete'] == '') {
				mail('irulan@geekwu.org', '[Starshine-Online] bug', $joueur->get_nom().
						 " a une quete invalide dans sa liste\n".
						 print_r($liste_quete, true));
				$i++;
				continue;
			}
			$requete = "SELECT id, nom, objectif, honneur, star, exp, reward, mode FROM quete WHERE id = ".$liste_quete[$i]['id_quete'];
			$req = $db->query($requete);
			$row = $db->read_array($req);
			//Vérification si quête solo ou groupe
			if($mode == 's' OR ($mode == 'g' AND $row['mode'] == 'g'))
			{
				$row['objectif'] = unserialize($row['objectif']);
				$j = 0;
				$count2 = count($row['objectif']);
				while($j < $count2)
				{
					//On a validé cette étape
					if($liste_quete[$i]['objectif'][$j]->nombre >= $row['objectif'][$j]->nombre)
					{
						$valid_objectif[$j] = true;
					}
					else
					{
						$valid_objectif[$j] = false;
					}
					$id_cible = substr($row['objectif'][$j]->cible, 1);
					$id_type_cible = substr($type_cible, 1);
					$type_cible_objectif = $row['objectif'][$j]->cible;
					$echo .= "Identifiant quète du perso : ".$i."\n Cible de la quète : ".$row['objectif'][$j]->cible."\n Cible de l'action : ".$type_cible."\n Identifiant de la cible de la quète : ".$id_cible."\n Identifiant de la cible de l'action : ".$id_type_cible."\n";
					//Si c'est la bonne cible, ou spécial pour PNJ si la cible = 0
					if($row['objectif'][$j]->cible == $type_cible OR ($row['objectif'][$j]->cible == 'P0' AND $type_cible[0] == 'P') OR ($type_cible != 'J127' AND intval($id_type_cible) >= intval($id_cible) AND $type_cible[0] == 'J' AND $type_cible_objectif[0] == 'J'))
					{
						$echo .= "Ca c'est ok !\n";
						$requis = explode(';', $liste_quete[$i]['objectif'][$j]->requis);
						$check = false;
						foreach($requis as $requi)
						{
							if($liste_quete[$i]['objectif'][$j]->requis == '' OR $valid_objectif[$liste_quete[$i]['objectif'][$j]->requis])
							{
								$check = true;
							}
						}
						if($check)
						{
							$liste_quete[$i]['objectif'][$j]->nombre++;
							$joueur->set_quete(serialize($liste_quete));
						}
						if($liste_quete[$i]['objectif'][$j]->nombre >= $row['objectif'][$j]->nombre)
						{
							$liste_quete[$i]['objectif'][$j]->nombre = $row['objectif'][$j]->nombre;
							$valid_objectif[$i] = true;
						}
						if(verif_quete($liste_quete[$i]['id_quete'], $i, $joueur))
						{
							fin_quete($joueur, $i, $liste_quete[$i]['id_quete']);
							$count--;
						}
						else
						{
							//Mis à jour des quêtes du perso
							$joueur->set_quete(serialize($liste_quete));
							$joueur->sauver();
						}
					}
					$echo .= "\n";
					$j++;
				}
			}
			$i++;
		}
		//echo $echo;
	}
}

function fin_quete($joueur, $id_quete_joueur, $id_quete)
{
	global $db;
	$requete = "SELECT id, nom, objectif, honneur, star, exp, reward, mode FROM quete WHERE id = ".$id_quete;
	$req = $db->query($requete);
	$row = $db->read_array($req);
	$liste_quete = $joueur->get_liste_quete();
	//Validation de la quête et mis à jour des quêtes du perso
	array_splice($liste_quete, $id_quete_joueur, 1);
	$joueur->set_quete(serialize($liste_quete));
	//On vérifie si la quète a déjà était fini, si non, on la mets dans les quètes finies
	$quete_fini = explode(';', $joueur->get_quete_fini());
	if(!in_array($id_quete, $quete_fini))
	{
		$quete_fini[] = $id_quete;
		$joueur->set_quete_fini(implode(';', $quete_fini));
	}
	$rewards = explode(';', $row['reward']);
	//print_r($rewards);
	$r = 0;
	$echo = '';
	while($r < count($rewards))
	{
		$reward_exp = explode('-', $rewards[$r]);
		$reward_id = $reward_exp[0];
		$reward_id_objet = substr($reward_id, 1);
		$reward_nb = $reward_exp[1];
		switch($reward_id[0])
		{
			//On gagne une recette
			case 'r' :
				$requete = "SELECT id FROM perso_recette WHERE id_recette = ".$reward_id_objet." AND id_perso = ".$joueur->get_id();
				//echo $requete;
				$req_r = $db->query($requete);
				if($db->num_rows > 0)
				{
				}
				else
				{
					$requete = "SELECT * FROM recette WHERE id = ".$reward_id_objet;
					//echo $requete;
					$req_r = $db->query($requete);
					$row_r = $db->read_assoc($req_r);
					$echo .= ' Recette de '.$row_r['nom'].' X '.$reward_nb.', ';
					//On lui donne la recette
					$requete = "INSERT INTO perso_recette VALUES('', ".$reward_id_objet.", ".$joueur->get_id().", 0)";
					$db->query($requete);
				}
			break;
			//On gagne un objet aléatoire
			case 'x' :
				$requete = "SELECT id FROM objet";
				$req_r = $db->query($requete);
				$liste = array();
				while($row_r = $db->read_row($req_r))
				{
					$liste[] = $row_r;
				}
				$count2 = count($liste);
				$random = floor(rand(0, $count2));
				$joueur->prend_objet('o'.$liste[$random][0]);
			break;
		}
		$r++;
	}
	$joueur->get_grade();
	$stars = round($row['star'] * (1 + ($joueur->grade->get_rang() * 2 / 100)));
	$honneur_gagne = $row['honneur'];
	if($joueur->is_buff('moral')) $honneur_gagne = $honneur_gagne * (1 + ($joueur->get_buff('moral', 'effet') / 100));
	$joueur->set_honneur($joueur->get_honneur() + $honneur_gagne);
	$joueur->set_exp($joueur->get_exp() + $row['exp']);
	$joueur->set_star($joueur->get_star() + $stars);
	$joueur->sauver();
	echo $joueur->get_nom().' finit la quête "'.$row['nom'].'", et gagne '.$stars.' stars, '.$echo.' '.$row['exp'].' points d\'expérience et '.$honneur_gagne.' points d\'honneur.<br />';
	$req = $db->query($requete);
	//Mis dans le journal
	$requete = "INSERT INTO journal VALUES('', ".$joueur->get_id().", 'f_quete', '".$joueur->get_nom()."', '', NOW(), '".addslashes($row['nom'])."', 0, 0, 0)";
	$db->query($requete);
}

function affiche_quetes($fournisseur, $joueur)
{
	global $db, $R;
	$return = array();
	$quetes = array();
	$liste_quete = $joueur->get_liste_quete();
	if(is_array($liste_quete))
	{
		foreach($liste_quete as $quete)
		{
			if ($quete['id_quete']!='')
			{
				$quetes[] = $quete['id_quete'];
			}
		
		}
		if(count($quetes) > 0) $notin = "AND quete.id NOT IN (".implode(',', $quetes).")";
		else $notin = '';
	}
	else $notin = '';
	$where = "";
	$id_royaume = $R->get_id();
	if($id_royaume < 10) '0'.$id_royaume;
	$requete = "SELECT *, quete.id as idq FROM quete LEFT JOIN quete_royaume ON quete.id = quete_royaume.id_quete WHERE ((achat = 'oui' AND quete_royaume.id_royaume = ".$R->get_id().") OR (achat = 'non' AND royaume LIKE '%".$id_royaume."%')) AND quete.fournisseur = '".$fournisseur."' AND quete.niveau_requis <= ".$joueur->get_level()." AND quete.honneur_requis <= ".$joueur->get_honneur()." ".$where." ".$notin." ORDER BY quete.lvl_joueur";
	$req = $db->query($requete);
	if($db->num_rows > 0)
	{
		$return[0] .= '<ul class="ville">';
	}
	while($row = $db->read_array($req))
	{
		$quete_fini = explode(';', $joueur->get_quete_fini());
		//Si c'est une quète non répétable et que le joueur a déjà fini la quète, on affiche pas.
		if($row['repete'] == 'n' AND in_array($row['idq'], $quete_fini))
		{
		}
		//Si c'est une quète qui en nécessite une autre mais que le joueur ne l'a pas déjà faite.
		else
		{
			$check = true;
			$requis = explode(';', $row['quete_requis']);
			$i = 0;
			$count = count($requis);
			while($check AND $i < $count)
			{
				if($requis[$i] != '' && !in_array($requis[$i], $quete_fini)) $check = false;
				$i++;
			}
			if($check)
			{
		$return[0] .= '<li>
		<a href="bureau_quete.php?action=description&amp;id='.$row['idq'].'" onclick="return envoiInfo(this.href, \'carte\')">'.$row['nom'].'</a> <span class="small">(Niv. '.$row['lvl_joueur'].')</span>
	</li>';
			}
		}
	}
	if($db->num_rows > 0)
	{
		$return[0] .=  '</ul>';
	}
	$return[1] = $db->num_rows;
	return $return;
}

function prend_quete($id_quete, $joueur)
{
	global $db, $G_erreur;
	$requete = "SELECT * FROM quete WHERE id = ".$id_quete;
	$req = $db->query($requete);
	$row = $db->read_array($req);
	$liste_quete = $joueur->get_liste_quete();
	$numero_quete = (count($liste_quete));
	$valid = true;
	$G_erreur = '';
	//Vérifie si le joueur n'a pas déjà pris la quète.
	if($liste_quete != '')
	{
		foreach($liste_quete as $quest)
		{
			if($quest['id_quete'] == $id_quete) $valid = false;
		}
	}
	else
	{
		$numero_quete = 0;
	}
	//Vérifie si il peut prendre cette quète
	$quete_fini = explode(';', $joueur->get_quete_fini());
	$quete_requis = explode(';', $row['quete_requis']);
	foreach($quete_requis as $requis)
	{
		if(!in_array($requis, $quete_fini)) $valid = false;
	}
	if($valid)
	{
		$quete = unserialize($row['objectif']);
		$count = count($quete);
		$i = 0;
		while($i < $count)
		{
			$liste_quete[$numero_quete]['objectif'][$i]->cible = $quete[$i]->cible;
			$liste_quete[$numero_quete]['objectif'][$i]->requis = $quete[$i]->requis;
			$liste_quete[$numero_quete]['id_quete'] = $row['id'];
			$liste_quete[$numero_quete]['objectif'][$i]->nombre = 0;
			$i++;
		}
		$joueur_quete = serialize($liste_quete);
		$requete = "UPDATE perso SET quete = '".$joueur_quete."' WHERE id = ".$joueur->get_id();
		$req = $db->query($requete);
	}
	else
	{
		$G_erreur = 'Vous avez déjà cette quète en cours !<br />';
	}
	if($row['fournisseur'] == '') $link = 'bureau_quete';
	else $link = $row['fournisseur'];
	return $link;
}

function verif_inventaire($id_quete, $joueur)
{
	global $db;
	$i = 0;
	$liste_quete = $joueur->get_liste_quete();
	$count = count($liste_quete);
	$check = false;
	while($i < $count AND !$check)
	{
		if($liste_quete[$i]['id_quete'] == $id_quete)
		{
			$check = true;
			$id_quete_joueur = $i;
		}
		$i++;
	}
	if($check)
	{
		$requete = "SELECT objectif FROM quete WHERE id = ".$id_quete;
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$row['objectif'] = unserialize($row['objectif']);
		$check = true;
		$i = 0;
		$count = count($row['objectif']);
		while($i < $count AND $check)
		{
			$cible = substr($row['objectif'][$i]->cible, 1);
			if(!$joueur->recherche_objet($cible)) {
				$check = false;
				echo "Il vous manque l'objet $cible.<br />";
			}
			$i++;
		}
		if($check)
		{
			$i = 0;
			$count = count($row['objectif']);
			while($i < $count AND $check)
			{
				$liste_quete[$id_quete_joueur]['objectif'][$i]->nombre = 1;
				$i++;
			}
			verif_quete($id_quete, $id_quete_joueur, $joueur);
			fin_quete($joueur, $id_quete_joueur, $id_quete);
		}
	}
	return $check;
}

//Supprime une quète, renvoi le joueur après suppression
function supprime_quete($joueur, $quete_joueur)
{
	global $db;
	$liste_quete = $joueur->get_liste_quete();
	array_splice($liste_quete, $quete_joueur, 1);
	$requete = "UPDATE perso SET quete = '".serialize($liste_quete)."' WHERE id = ".$joueur->get_id();
	$db->query($requete);
	return $joueur;
}

?>
