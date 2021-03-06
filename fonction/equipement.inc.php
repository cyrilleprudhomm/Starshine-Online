<?php
if (file_exists('../root.php'))
  include_once('../root.php');
?><?php
//Recherche le nombre d'objet de ce type dans l'inventaire
function recherche_nb_objet($joueur, $id_objet)
{
	global $G_place_inventaire;
	$objet_d = decompose_objet($id_objet);
	$trouver=  false;
	//Recherche si le joueur n'a pas des objets de ce type dans son inventaire
	$i = 0;
	$nb_objet = 0;
	while($i < $G_place_inventaire)
	{
		$objet_i = decompose_objet($joueur['inventaire_slot'][$i]);
		if($objet_i['sans_stack'] == $objet_d['sans_stack'])
		{
			if($objet_i['stack'] > 1) $nb_objet += $objet_i['stack'];
			else $nb_objet += 1;
		}
		else $i++;
	}
	if($nb_objet > 0)
	{
		return $return;
	}
	else return false;
}

//Renvoi l'objet décomposé sous forme de tableau => stack, slot, enchantement, id, id_objet, sans_stack
function decompose_objet($objet)
{
	if($objet == 'lock' || !$objet )
	{
		return false;
	}
	else
	{
		$objet_dec = array();
		$decomp = explode('x', $objet);
		$objet_dec['sans_stack'] = $decomp[0];
		$objet_dec['stack'] = $decomp[1];
		$decomp = explode('e', $objet_dec['sans_stack']);
		$objet_dec['id'] = $decomp[0];
		$objet_dec['enchantement'] = $decomp[1];
		$decomp = explode('s', $objet_dec['id']);
		$objet_dec['id'] = $decomp[0];
		$objet_dec['slot'] = $decomp[1];
		$objet_dec['identifier'] = true;
		if($objet_dec['id'][0] != 'h')
		{
			$objet_dec['id_objet'] = substr($objet_dec['id'], 1);
			$objet_dec['categorie'] = $objet_dec['id'][0];
		}
		else
		{
			$objet_dec['id_objet'] = substr($objet_dec['id'], 2);
			$objet_dec['categorie'] = $objet_dec['id'][1];
			$objet_dec['identifier'] = false;
		}
		switch($objet_dec['categorie'])
		{
			case 'p' :
				$objet_dec['table_categorie'] = 'armure';
			break;
			case 'a' :
				$objet_dec['table_categorie'] = 'arme';
			break;
			case 'l' :
				$objet_dec['table_categorie'] = 'grimoire';
			break;
			case 'o' :
				$objet_dec['table_categorie'] = 'objet';
			break;
			case 'd' :
				$objet_dec['table_categorie'] = 'objet_pet';
			break;
			case 'g' :
				global $db;
				$objet_dec['table_categorie'] = 'gemme';
				$requete = "SELECT * FROM gemme WHERE id = ".$objet_dec['id_objet'];
				$req = $db->query($requete);
				$objet_dec['valeurs'] = $db->read_assoc($req);
			break;
			case 'm' :
				$objet_dec['table_categorie'] = 'accessoire';
			break;
		}
		return $objet_dec;
	}
}

function enchant($gemme_id, $var)
{
	global $db;

	$requete = "SELECT * FROM gemme WHERE id = ".$gemme_id;
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	$enchants = explode(';', $row['enchantement_type']);
	$effets = explode(';', $row['enchantement_effet']);
	$i = 0;
	while($i < count($enchants))
	{
		switch($enchants[$i])
		{
		case 'degat' :
			$var->set_degat_arme($var->get_arme_degat() + $effets[$i]);
			break;
		case 'critique' :
			$var['enchantement'][$enchants[$i]]['effet'] = $effets[$i];
			$var['enchantement'][$enchants[$i]]['type'] = $enchants[$i];
			break;
		case 'hp' :
			$var->set_hp_max($var->get_hp_maximum() + $effets[$i]);
			break;
		case 'mp' :
			$var->set_mp_maximum( $var->get_mp_maximum() + $effets[$i]);
			break;
		case 'reserve' :
			$var['reserve'] += $effets[$i];
			break;
		case 'pp' :
			$var->set_pp($var->get_pp() + $effets[$i]);
			break;
			/* On ne peut pas le faire comme ca car le matos est pas charge en entier
		case 'pourcent_pp' :
			$var['PP'] = $var['PP'] + ceil($var['PP'] * $effets[$i] / 100);
			break; */
		case 'pm' :
			$var->set_pm($var->get_pm() + $effets[$i]);
			break;
			/* On ne peut pas le faire comme ca car le matos est pas charge en entier
		case 'pourcent_pm' :
			$var['PM'] = $var['PM'] + ceil($var['PM'] * $effets[$i] / 100);
			break; */
		case 'portee' :
			//echo 'plop';
			$var['arme_distance'] += $effets[$i];
			break;
		case 'star' :
			$var['chance_star'] = $effets[$i];
			break;
		case 'esquive' : /* gemmes de compétence: bonus ignoré à la montée */
		case 'melee' : 
		case 'distance' :
    case 'incantation' :
			$var->add_bonus_permanents($enchants[$i], $effets[$i]);
			break;
    default: /* gemmes ayant un effect ponctuel */
			$enchantements = $var->get_enchantement();
			if (isset($enchantements[$enchants[$i]])) {
				$enchantements[$enchants[$i]]['gemme_id'] .= ';'.$gemme_id;
				$enchantements[$enchants[$i]]['effet'] += $effets[$i];
			}
			else {
				$enchantements[$enchants[$i]]['gemme_id'] = $gemme_id; // pour la stack d'effets
				$enchantements[$enchants[$i]]['effet'] = $effets[$i]; // pour utilisation classique
			}
		}
		$i++;
	}
	return $var;
}

function enchant_description($gemme_id)
{
	global $db;
	$requete = "SELECT description FROM gemme WHERE id = ".$gemme_id;
	$req = $db->query($requete);
	$row = $db->read_assoc($req);
	return $row['description'];
}

function recompose_objet($objet)
{
	$objet_rec = $objet['id'];
	if(!is_null($objet['enchantement'])) $objet_rec .= 'e'.$objet['enchantement'];
	elseif(!is_null($objet['slot'])) $objet_rec .= 's'.$objet['slot'];
	if(!is_null($objet['stack'])) $objet_rec .= 'x'.$objet['stack'];
	return $objet_rec;
}

function nom_objet($id_objet)
{
	global $db;
	$objet = decompose_objet($id_objet);
	$id_objet = $objet['id'];
	$nom = '';
	switch($objet['categorie'])
	{
		case 'a' :
			$table = 'arme';
		break;
		case 'p' :
			$table = 'armure';
		break;
		case 'o' :
			$table = 'objet';
		break;
		case 'g' :
			$table = 'gemme';
		break;
		case 'm' :
			$table = 'accessoire';
		break;
		case 'l' :
			$table = 'grimoire';
		break;
		case 'r' :
			$table = 'objet_royaume';
		break;
		case 'd' :
			$table = 'objet_pet';
		break;
	}
	$requete = "SELECT nom FROM ".$table." WHERE id = ".$objet['id_objet'];
	$req = $db->query($requete);
	$row = $db->read_row($req);
	return $row[0];
}

function description_objet($id_objet)
{
	global $db, $Gtrad;
	$objet = decompose_objet($id_objet);
	$id_objet = $objet['id'];
	$description = '';
	switch($objet['categorie'])
	{
		case 'a' :
			$requete = "SELECT * FROM arme WHERE id = ".$objet['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$description .= '<strong>'.$row['nom'].'</strong><br />	<table> <tr> <td> Type </td> <td> '.$row['type'].' </td> </tr> <tr> <td> Nombre de mains </td> <td> '.count(explode(';', $row['mains'])).' </td> </tr> <tr> <td> Dégâts </td> <td> '.$row['degat'].' </td> </tr> <tr> <td> Force nécessaire </td> <td> '.$row['forcex'].' </td> </tr> <tr> <td> Portée </td> <td> '.$row['distance_tir'].' </td> </tr> '; if($row['type'] == 'arc') { $description .= ' <tr> <td> Tir à distance </td> <td> '.$row['distance'].' </td> </tr>'; } elseif($row['type'] == 'baton') { $description .= ' <tr> <td> Augmentation<br /> lancement de sorts </td> <td> '.$row['var1'].'% </td> </tr>'; } $description .= ' <tr> <td> Prix HT<br /> <span class=\\\'xsmall\\\'>(en magasin)</span> </td> <td> '.$row['prix'].' </td> </tr> </table>';
		break;
		case 'p' :
			$requete = "SELECT * FROM armure WHERE id = ".$objet['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$description .= '<strong>'.$row['nom'].'</strong><br />	<table> <tr> <td> Type </td> <td> '.$Gtrad[$row['type']].' </td> </tr> <tr> <td> PP </td> <td> '.$row['PP'].' </td> </tr> <tr> <td> PM </td> <td> '.$row['PM'].' </td> </tr> <tr> <td> Force nécessaire </td> <td> '.$row['forcex'].' </td> </tr> <tr> <td> Prix HT<br /> <span class=\\\'xsmall\\\'>(en magasin)</span> </td> <td> '.$row['prix'].' </td> </tr> </table>';
		break;
		case 'd' :
			$requete = "SELECT * FROM objet_pet WHERE id = ".$objet['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			if($row['type'] == "arme_pet")
			{
				$description .= '<strong>'.$row['nom'].'</strong><br />	<table> <tr> <td> Type </td> <td> '.$row['type'].' </td> </tr> <tr> <td> Nombre de mains </td> <td> '.count(explode(';', $row['mains'])).' </td> </tr> <tr> <td> Dégâts </td> <td> '.$row['degat'].' </td> </tr> <tr> <td> Dressage nécessaire </td> <td> '.$row['dressage'].' </td> </tr> <tr> <td> Portée </td> <td> '.$row['distance_tir'].' </td> </tr> '; 
				$description .= ' <tr> <td> Prix HT<br /> <span class=\\\'xsmall\\\'>(en magasin)</span> </td> <td> '.$row['prix'].' </td> </tr> </table>';
			}
			else
				$description .= '<strong>'.$row['nom'].'</strong><br />	<table> <tr> <td> Type </td> <td> '.$row['type'].' </td> </tr> <tr> <td> PP </td> <td> '.$row['PP'].' </td> </tr> <tr> <td> PM </td> <td> '.$row['PM'].' </td> </tr> <tr> <td> Dressage nécessaire </td> <td> '.$row['dressage'].' </td> </tr> <tr> <td> Prix HT<br /> <span class=\\\'xsmall\\\'>(en magasin)</span> </td> <td> '.$row['prix'].' </td> </tr> </table>';
		break;
		case 'm' :
			$requete = "SELECT * FROM accessoire WHERE id = ".$objet['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$description .= '<strong>'.$row['nom'].'</strong><br />	<table> <tr> <td> Type </td> <td> '.$Gtrad[$row['type']].' </td> </tr> <tr> <td> Effet </td> <td> '.description($row['description'], $row).' </td> </tr> <tr> <td> Puissance nécessaire </td> <td> '.$row['puissance'].' </td> </tr> <tr> <td> Prix HT<br /> <span class=\\\'xsmall\\\'>(en magasin)</span> </td> <td> '.$row['prix'].' </td> </tr> </table>';
		break;
		case 'o' :
			$requete = "SELECT * FROM objet WHERE id = ".$objet['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$keys = array_keys($row);
			if($row['pa'] > 0) $pa = '<tr><td>PA<br /></td><td>'.$row['pa'].'</td></tr>';
			else $pa = '';
			if($row['mp'] > 0) $mp = '<tr><td>MP<br /></td><td>'.$row['mp'].'</td></tr>';
			else $mp = '';
			$description .= '<strong>'.$row['nom'].'</strong><br /><table><tr><td>Type</td><td>'.$Gtrad[$row['type']].'</td></tr><tr><td>Stack</td><td>'.$row['stack'].'</td></tr><tr><td>Description</td></tr><tr><td>'.addslashes(description($row['description'], $row)).'</td></tr><tr><td>Prix HT<br /><span class=\\\'xsmall\\\'>(en magasin)</span></td><td>'.$row['prix'].'</td></tr>'.$pa.$mp.'</table>';
		break;
		case 'r' :
			$requete = "SELECT * FROM objet_royaume WHERE id = ".$objet['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$keys = array_keys($row);
			$description .= '<strong>'.$row['nom'].'</strong><br /><table> <tr> <td> Type </td> <td> '.$row['type'].' </td> </tr> </table>';
		break;
		case 'h' :
			$description = 'Objet non identifié';
		break;
		case 'g' :
			$requete = "SELECT * FROM gemme WHERE id = ".$objet['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_assoc($req);
			$keys = array_keys($row);
			$partie = '';
			if ($row['partie'] != '') {
				$partie = " ($row[partie])";
			}
			$description .= '<strong>'.$row['nom'].'</strong><br /><table> <tr> <td> Type </td> <td> '.$row['type'].$partie.' </td> </tr> <tr> <td> Niveau </td> <td> '.$row['niveau'].' </td> </tr> <tr> <td> Description </td> </tr> <tr> <td> '.description($row['description'], $keys).' </td> </tr> </table>';
		break;
	case 'l' :
	  $requete = "SELECT * FROM grimoire WHERE id = ".$objet['id_objet'];
	  $req = $db->query($requete);
	  $row = $db->read_assoc($req);
	  $description = '<strong>'.$row['nom'].
	    '</strong><br />';
	  if (isset($row['comp_jeu'])) {
	    $table = 'comp_jeu';
	    $id_comp = $row['comp_jeu'];
	    $type = "la compétence";
	  }
	  elseif (isset($row['comp_combat'])) {
	    $table = 'comp_combat';
	    $id_comp = $row['comp_combat'];
	    $type = "la compétence";
	  }
	  elseif (isset($row['sort_jeu'])) {
	    $table = 'sort_jeu';
	    $id_comp = $row['sort_jeu'];
	    $type = "le sort";
	  }
	  elseif (isset($row['sort_combat'])) {
	    $table = 'sort_combat';
	    $id_comp = $row['sort_combat'];
	    $type = "le sort";
	  }
	  if (isset($row['comp_perso_competence'])) {
	    $description .= 'Entraîne la compétence '.
	      traduit($row['comp_perso_competence']).
	      ' de '.$row['comp_perso_valueadd'];
	  }
	  else {
	    $requete2 = "SELECT * from $table where id ='$id_comp'";
	    $req2 = $db->query($requete2);
	    $row2 = $db->read_assoc($req2);
	    $description .= 'Apprend '.$type.' '.$row2['nom'].'<br />';
	    if (isset($row2['requis']) && $row2['requis'] != '999'
		&& $row2['requis'] != '') {
	      $rqs = explode(';', $row2['requis']);
	      foreach ($rqs as $rq) {
		$requete3 = "SELECT nom from $table where id ='$rq'";
		$req3 = $db->query($requete3);
		$row3 = $db->read_assoc($req3);
		$description .= '<br />Requiert '.$type.' '.$row3['nom'];
	      }
	    }
	    if ($row2['carac_requis'] > 0) {
		$description .= '<br />Requiert '.traduit($row2['carac_assoc']).
		  ' à '.$row2['carac_requis'];
	    }
	    if ($row2['comp_requis'] > 0) {
	      $requis = $row2['comp_requis'];
	      if ($type == 'le sort') {
		global $joueur;
		global $Trace;
		$requis = round($row2['comp_requis'] * $joueur->get_facteur_magie() * 
				(1 - (($Trace[$joueur->get_race()]['affinite_'.$row2['comp_assoc']] - 5)
				      / 10)));
	      }
	      $description .= '<br />Requiert '.traduit($row2['comp_assoc']).' à '.$requis;
	    }
	    if (isset($row2['incantation']) && $row2['incantation'] != 0) {
	      global $joueur;
	      $description .= '<br />Requiert '.traduit('incantation').
		' à '.($row2['incantation'] * $joueur->get_facteur_magie());
	    }
	  }

	  if (isset($row['classe_requis'])) {
	    $description .= '<br />Reservé aux ';
	    $classes = explode(';', $row['classe_requis']);
	    $virgule = false;
	    foreach ($classes as $c) {
	      if ($virgule) $description .= ', ';
	      else $virgule = true;
	      $description .= pluriel($c);
	    }
	  }
	}
	if($objet['enchantement'] != '') $description .= '<br />Enchantement : '.enchant_description($objet['enchantement']);
	return $description;
}

function equip_objet($objet, $joueur)
{
	global $db, $G_erreur;
	$equip = false;
	$conditions = array();
	if($objet_d = decompose_objet($objet))
	{
		//print_r($objet_d);
		$id_objet = $objet_d['id_objet'];
		$categorie = $objet_d['categorie'];
		switch ($categorie)
		{
			//Si c'est une arme
			case 'a' :
				$requete = "SELECT * FROM arme WHERE ID = ".$id_objet;
				//Récupération des infos de l'objet
				$req = $db->query($requete);
				$row = $db->read_array($req);
				if($row['type'] == 'baton')
				{
					$conditions[0]['attribut']	= 'coef_incantation';
					$conditions[0]['valeur']	= $row['forcex'] * $row['melee'];
				}
				elseif($row['type'] == 'bouclier')
				{
					$conditions[0]['attribut']	= 'coef_blocage';
					$conditions[0]['valeur']	= $row['forcex'] * $row['melee'];
				}
				else
				{
					$conditions[0]['attribut']	= 'coef_melee';
					$conditions[0]['valeur']	= $row['forcex'] * $row['melee'];
				}
				$conditions[1]['attribut']	= 'coef_distance';
				$conditions[1]['valeur']	= $row['forcex'] * $row['distance'];
				$type = explode(';', $row['mains']);
				$type = $type[0];
				$mains = $row['mains'];
			break;
			//Si c'est une protection
			case 'p' :
				$requete = "SELECT * FROM armure WHERE ID = ".$id_objet;
				//Récupération des infos de l'objet
				$req = $db->query($requete);
				$row = $db->read_array($req);
				$conditions[0]['attribut']	= 'force';
				$conditions[0]['valeur']	= $row['forcex'];
				$type = $row['type'];
			break;
			//Si c'est un accessoire
			case 'm' :
				$requete = "SELECT * FROM accessoire WHERE ID = ".$id_objet;
				//Récupération des infos de l'objet
				$req = $db->query($requete);
				$row = $db->read_array($req);
				$conditions[0]['attribut']	= 'puissance';
				$conditions[0]['valeur']	= $row['puissance'];
				$type = 'accessoire';
			break;
		}
		
		//Vérification des conditions
		if (is_array($conditions))
		{
			$i = 0;
			while ($i < count($conditions))
			{
				$get = 'get_'.$conditions[$i]['attribut'];
				if ($joueur->$get() < $conditions[$i]['valeur'])
				{
					$G_erreur = 'Vous n\'avez pas assez en '.$conditions[$i]['attribut'].'<br />';
					return false;
				}
				$i++;
			}
		}
		
		//Si c'est une dague main gauche, vérifie qu'il a aussi une dague en main droite
		if($type == 'main_gauche' AND $row['type'] == 'dague')
		{
			if($joueur->get_inventaire()->main_droite === 0)
			{
			}
			else
			{
				$main_droite = decompose_objet($joueur->get_inventaire()->main_droite);
				$requete = "SELECT * FROM arme WHERE ID = ".$main_droite['id_objet'];
				//Récupération des infos de l'objet
				$req_md = $db->query($requete);
				$row_md = $db->read_array($req_md);
				if($row['type'] == 'dague')
				{
					if($row_md['type'] != 'dague')
					{
						$G_erreur = 'L\'arme équipée en main droite n\'est pas une dague<br />';
						return false;
					}
				}
				elseif(count(explode(';', $row_md['mains'])) > 1)
				{
					$G_erreur = 'Vous devez enlever votre arme à 2 mains pour porter cet objet<br />';
					return false;
				}
			}
		}
		//Vérifie si il a une dague en main gauche et si c'est le cas et que l'arme n'est pas une dague, on désequipe
		if($type == 'main_droite' AND $row['type'] != 'dague')
		{
			if($joueur->get_inventaire()->main_gauche === 0 OR $joueur->get_inventaire()->main_gauche == '')
			{
			}
			else
			{
				if($main_gauche = decompose_objet($joueur->get_inventaire()->main_gauche))
				{
					$requete = "SELECT * FROM arme WHERE ID = ".$main_gauche['id_objet'];
					//Récupération des infos de l'objet
					$req_mg = $db->query($requete);
					$row_mg = $db->read_array($req_mg);
					if($row_mg['type'] == 'dague')
					{
						desequip('main_gauche', $joueur);
						$joueur = recupperso($joueur->get_id());
					}
				}
				else
				{
				}
			}
		}
		
		$desequip = true;
		if($categorie == 'a')
		{
			$mains = explode(';', $mains);
			$type = $mains[0];
			$count = count($mains);
		}
		//Verifie si il a déjà un objet de ce type sur lui
		if ($type != '')
		{
			//Desequipement
			if($categorie == 'a')
			{
				$i = 0;
				while($desequip AND $i < $count)
				{
					if($joueur->get_inventaire()->$mains[$i] === 'lock' AND $joueur->get_inventaire()->main_droite !== 0)
					{
						desequip('main_droite', $joueur);
					}
					$joueur = recupperso($joueur->get_id());
					$desequip = desequip($mains[$i], $joueur);
					$joueur = recupperso($joueur->get_id());
					$i++;
				}
			}
			else
			{
				$desequip = $joueur->desequip($type);
			}
		}
		
		if($desequip)
		{
			//On équipe
			$inventaire = $joueur->inventaire();
			$inventaire->$type = $objet;
			if($categorie == 'a' AND $count == 2) $inventaire->main_gauche = 'lock';
			$joueur->set_inventaire(serialize($inventaire));
			$joueur->set_inventaire_slot(serialize($joueur->get_inventaire_slot_partie()));
			$joueur->sauver();
			return true;
		}
		else
		{
			return false;
		}
	}
	else return false;
}

//Récupère les données d'un echange
function recup_echange($id_echange, $royaume = false)
{
	global $db;
	$echange = array();
	$echange['objet'] = array();
	if($royaume)
		$requete = "SELECT * FROM echange_royaume WHERE id_echange = ".$id_echange;
	else
		$requete = "SELECT * FROM echange WHERE id_echange = ".$id_echange;
	if($req_e = $db->query($requete))
	{
		$echange = $db->read_assoc($req_e);
		if($royaume)
			$requete = "SELECT * FROM echange_ressource_royaume WHERE id_echange = ".$id_echange;
		else
			$requete = "SELECT * FROM echange_objet WHERE id_echange = ".$id_echange;
		$req_o = $db->query($requete);
		while($row_o = $db->read_assoc($req_o))
		{
			if($row_o['type'] == 'objet')
			{
				$echange['objet'][] = $row_o;
			}
			elseif($row_o['type'] == "star" and !$royaume)
			{
				$echange['star'][$row_o['id_j']] = $row_o;
			}
			else
			{
				$echange['ressource'][$row_o['type']][$row_o['id_r']] = $row_o;
			}
		}
	}
	return $echange;
}

//Récupère tous les échanges entre 2 joueurs
function recup_echange_perso($joueur_id, $receveur, $royaume = false)
{
	global $db;
	$echanges = array();
	if($royaume)
		$requete = "SELECT id_echange, statut FROM echange_royaume WHERE ((id_r1 = ".$joueur_id." AND id_r2 = ".$receveur.") OR (id_r1 = ".$receveur." AND id_r2 = ".$joueur_id.")) AND statut <> 'fini' AND statut <> 'annule'";
	else
		$requete = "SELECT id_echange, statut FROM echange WHERE ((id_j1 = ".$joueur_id." AND id_j2 = ".$receveur.") OR (id_j1 = ".$receveur." AND id_j2 = ".$joueur_id.")) AND statut <> 'fini' AND statut <> 'annule'";
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$echanges[] = $row;
	}
	return $echanges;
}

//Récupère tous les échanges d'un perso avec option de tri
function recup_tout_echange_perso($joueur_id, $tri = 'id_echange DESC')
{
	global $db;
	$echanges = array();
	$requete = "SELECT id_echange, statut, id_j1, id_j2 FROM echange WHERE (id_j1 = ".$joueur_id." OR id_j2 = ".$joueur_id.") AND statut <> 'annule' ORDER BY ".$tri;
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$echanges[] = $row;
	}
	return $echanges;
}

//Récupère tous les échanges d'un perso avec option de tri
function recup_tout_echange_perso_ranger($joueur_id, $tri = 'id_echange DESC')
{
	global $db;
	$echanges = array();
	$requete = "SELECT id_echange, statut, id_j1, id_j2 FROM echange WHERE (id_j1 = ".$joueur_id." OR id_j2 = ".$joueur_id.") AND statut <> 'annule' ORDER BY ".$tri;
	$req = $db->query($requete);
	while($row = $db->read_assoc($req))
	{
		$echanges[$row['statut']][] = $row;
	}
	return $echanges;
}

//Ajoute un objet a l'échange (star ou objet)
function echange_objet_ajout($id_objet, $type, $id_echange, $id_joueur)
{
	global $db;
	if(verif_echange_joueur($id_echange, $id_joueur, $id_objet, $type))
	{
		$requete = "INSERT INTO echange_objet(id_echange, id_j, type, objet) VALUES (".$id_echange.", ".$id_joueur.", '".$type."', '".$id_objet."')";
		if($db->query($requete)) return true; else return false;
	}
	else return false;
}

//Ajoute des ressources a l'échange royaume
function echange_royaume_ajout($nombre, $type, $id_echange, $id_royaume)
{
	global $db;
	if(verif_echange_royaume($id_echange, $id_royaume, $nombre, $type))
	{
		$requete = "INSERT INTO echange_ressource_royaume(id_echange, id_r, type, nombre) VALUES (".$id_echange.", ".$id_royaume.", '".$type."', '".$nombre."')";
		if($db->query($requete)) return true; else return false;
	}
	else return false;
}

//Supprime un objet a l'échange
function echange_objet_suppr($id_objet_echange)
{
	global $db;
	$requete = "DELETE FROM echange_objet WHERE id_echange_objet = ".$id_objet_echange;
	if($db->query($requete)) return true; else return false;
}

//Supprime une ressource a l'échange royaume
function echange_royaume_suppr($id_echange_ressource)
{
	global $db;
	$requete = "DELETE FROM echange_ressource_royaume WHERE id_echange_ressource = ".$id_echange_ressource;
	if($db->query($requete)) return true; else return false;
}

function verif_echange_joueur($id_echange, $id_joueur, $id_objet = 0, $type_objet = 0)
{
	$joueur = new perso($id_joueur);
	$echange = recup_echange($id_echange);
	//Vérification des objets
	if($id_objet !== 0 && $type_objet == 'objet') $echange['objet'][] = array('id_j' => $id_joueur, 'objet' => $id_objet);
	$echange_objets = array();
	$invent_objets = array();
	if ($echange['objet'])
	{
		foreach($echange['objet'] as $objet)
		{
			if($objet['id_j'] == $id_joueur) $echange_objets[$objet['objet']]++;
		}
	}
	if($joueur->get_inventaire_slot() != '')
	{
		foreach($joueur->get_inventaire_slot_partie() as $invent)
		{
			$invent_d = decompose_objet($invent);
			if($invent_d['stack'] == '') $invent_d['stack'] = 1;
			$invent_objets[$invent_d['sans_stack']] += $invent_d['stack'];
		}
	}
	$check = true;
	foreach($echange_objets as $key => $objet_nb)
	{
		if($invent_objets[$key] < $objet_nb) $check = false;
	}
	//Vérification des stars
	if($type_objet == 'star')
	{
		if ($id_objet < 0) { security_block(BAD_ENTRY, "$id_objet < 0"); }
		//Si il a assez de stars	
		if($joueur->get_star() >= $id_objet)
		{
			// On verifie que les stars sont toujours là lors de la finalisation
			if($echange['statut'] == 'finalisation')
			{
				if($joueur->get_star() < $echange['star'][$id_joueur]['objet'])
					$check = false;
			}
			//Si ya déjà des stars, on les suppriment
			elseif(array_key_exists('star', $echange) && array_key_exists($id_joueur, $echange['star']))
			{
				echange_objet_suppr($echange['star'][$id_joueur]['id_echange_objet']);
			}
		}
		else $check = false;
	}
	elseif(array_key_exists('star', $echange) && array_key_exists($id_joueur, $echange['star']) && $joueur['star'] < intval($echange['star'][$id_joueur]['id_objet'])) $check = false;
	return $check;
}

function verif_echange_royaume($id_echange, $id_royaume, $nombre = 0, $type_ressource = null)
{
	$royaume = new royaume($id_royaume);
	$echange = recup_echange($id_echange, true);
	$check = true;
	if($type_ressource != null)
	{
		$get = "get_".$type_ressource;
		//Vérification de la ressource
		if ($nombre < 0) { security_block(BAD_ENTRY, "$nombre < 0"); }
		//Si il a assez de la ressource
		if($royaume->$get() >= $nombre)
		{
			//Si ya déjà de cet ressource, on la supprime
			if(isset($echange['ressource'][$type_ressource]))
			{
				if(array_key_exists($id_royaume, $echange['ressource'][$type_ressource]) && $echange['statut'] != 'finalisation')
				{
					echange_royaume_suppr($echange['ressource'][$type_ressource][$id_royaume]['id_echange_ressource']);
				}
			}
		}
		else $check = false;
	}
	else
	{
		foreach($echange['ressource'] AS $ressource)
		{
			$get = "get_".$ressource[$id_royaume]['type'];
			//Vérification de la ressource
			if ($ressource[$id_royaume]['nombre'] < 0) { security_block(BAD_ENTRY, $ressource[$id_royaume]['nombre']." < 0"); }
			//Si il a assez de la ressource
			if($royaume->$get() >= $ressource[$id_royaume]['nombre'])
			{
				//Si ya déjà de cet ressource, on la supprime
				if(array_key_exists($id_royaume, $echange['ressource'][$ressource['type']]) && $echange['statut'] != 'finalisation')
				{
					echange_royaume_suppr($echange['ressource'][$ressource['type']][$id_royaume]['id_echange_objet']);
				}
			}
			else return false;
		}
	}
	return $check;
}

function verif_echange($id_echange, $id_j1, $id_j2)
{
	if(verif_echange_joueur($id_echange, $id_j1) && verif_echange_joueur($id_echange, $id_j2)) return true;
	else return false;
}

function verif_echange_both_royaume($id_echange, $id_r1, $id_r2)
{
	if(verif_echange_royaume($id_echange, $id_r1) && verif_echange_royaume($id_echange, $id_r2)) return true;
	else return false;
}

function check_utilisation_objet($joueur, $objet)
{
	global $db;
	$id_objet = $objet['id'];
	//On chope les infos de l'objet
	$requete = "SELECT pa, mp FROM objet WHERE id = ".$objet['id_objet'];
	$req_o = $db->query($requete);
	$row_o = $db->read_assoc($req_o);
	//On vérifie les PA / MP
	if($joueur->get_pa() >= $row_o['pa'])
	{
		if($joueur->get_mp() >= $row_o['mp'])
		{
			$joueur->supprime_objet($id_objet, 1);
			//$id_objet = mb_substr($id_objet, 1);
			return true;
		}
		else echo '<h5>Vous n\'avez pas assez de MP</h5>';
	}
	else echo '<h5>Vous n\'avez pas assez de PA</h5>';
	return false;
}

?>