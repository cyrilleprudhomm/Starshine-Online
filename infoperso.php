<?php // -*- tab-width:2; mode: php -*-
if (file_exists('root.php'))
  include_once('root.php');

{//-- Initialisation
	require_once(root.'inc/fp.php');
	if(!isset($joueur)) { $joueur = new perso($_SESSION["ID"]); }; 		//-- Récupération du tableau contenant toutes les informations relatives au joueur
	$joueur->check_perso();
	echo '<div id="perso_contenu">';
	
	//-- Dans le cas ou le joueur a pris un level on traite son level up.
	if ($joueur->get_exp() > prochain_level($joueur->get_level()))
	{
		$joueur->set_level($joueur->get_level() + 1);
		$joueur->set_point_sso($joueur->get_point_sso() + 1);
		$joueur->sauver();
	}
}
{//-- Javascript
	echo "<script type='text/javascript'>
			// <![CDATA[\n";
	{//-- cancelBuff(buff_id)
		echo "	function cancelBuff(buff_id, buff_nom)
				{
					if(confirm('Voulez vous supprimer '+ buff_nom +' ?'))
					{
						envoiInfo('suppbuff.php?id='+ buff_id, 'perso');
					}
				}";
	}
	echo "	// ]]>
		  </script>";
}


{//-- PA, HP, MP, XP, ...
	
	echo "<div id='infos_perso' style=\"background:transparent url('./image/interface/fond_info_perso_".$joueur->get_race_a().".png') 10px 10px no-repeat;\"> <div style='left: 18px;    position: absolute;    top: 75px;'>niv.".$joueur->get_level()."</div>"; 
	echo " <div id='joueur_nom' onclick=\"envoiInfo('personnage.php', 'information');\" title=\"Accès à la fiche de votre personnage\">".$titre[0]." ".ucwords($joueur->get_grade()->get_nom())." ".ucwords($joueur->get_nom())." ".$titre[1]."</div>";
	echo " <div id='joueur_HP' rel='tooltip' data-placement='right' class='progress progress-danger' title='Point de vie : ".$joueur->get_hp()."/".floor($joueur->get_hp_maximum())."'><div class='bar' style='width: ".($joueur->get_hp()/floor($joueur->get_hp_maximum())*100)."%;'></div></div>";
	echo " <div id='joueur_MP' rel='tooltip' data-placement='right' class='progress' title='Point de magie : ".$joueur->get_mp()."/".floor($joueur->get_mp_maximum())."'><div class='bar' style='width: ".($joueur->get_mp()/floor($joueur->get_mp_maximum())*100)."%;'></div></div>";
	echo " <div id='joueur_XP' rel='tooltip' data-placement='right' class='progress progress-warning' title='Point Experience'><div class='bar' style='width:".progression_level(level_courant($joueur->get_exp()))."%;'></div></div>";
	echo " <div id='joueur_PA' rel='tooltip' data-placement='right' class='progress progress-success' title='Point action : ".$joueur->get_pa()."/".$G_PA_max."'><div class='bar' style='width: ".($joueur->get_pa()/$G_PA_max*100)."%;'></div></div>";
	echo " <div id='joueur_PO' title='Vos stars'>".$joueur->get_star()."</div>";
	echo ' <div id="joueur_PH" title="Votre honneur : '.$joueur->get_honneur().' / Votre réputation : '.$joueur->get_reputation().'">'.$joueur->get_honneur().'</div>';
	$script_attaque = recupaction_all($joueur->get_action_a());
	//-- Index, Forums, Exit, Options


	echo "</div>";
}
{//-- Buffs, Grade, Pseudo
	$titre_perso = new titre($_SESSION["ID"]);
	
	$bonus = recup_bonus($joueur->get_id());
	$titre = $titre_perso->get_titre_perso($bonus);
	echo "<div id='joueur_buffs_nom'>";
	
	echo " <div id='buff_list'>
			<ul>";
	//my_dump($joueur->get_buff());
    $buffs = $joueur->get_buff();
		if(is_array($buffs))
		{
			foreach($buffs as $buff)
			{//-- Listing des buffs
				if($buff->get_debuff() == 0)
				{
					$overlib = str_replace("'", "\'", trim("<ul><li class='overlib_titres'>".$buff->get_nom()."</li><li>".description($buff->get_description(), $buff)."</li><li>Durée ".transform_sec_temp($buff->get_fin() - time())."</li><li class='overlib_infos'>(double-cliquer pour annuler ce buff)</li></ul>"));
					echo "<li class='buff'>
						   <img src='image/buff/".$buff->get_type()."_p.png'
								alt='".$buff->get_type()."'
								ondblclick=\"cancelBuff('".$buff->get_id()."', '".addslashes($buff->get_nom())."');\"
								rel='tooltip'
								data-html='true'
								data-placement='right' 
								title=\"$overlib\" />
						   ".genere_image_buff_duree($buff)."
						  </li>";
				}
			}
		}
		if($joueur->get_nb_buff() < ($joueur->get_grade()->get_nb_buff()) )
		{
			$case_buff_dispo = ($joueur->get_grade()->get_nb_buff()) - $joueur->get_nb_buff();
			for($b = 0; $b < $case_buff_dispo; $b++)
			{
				echo "<li class='buff_dispo' title='vous pouvez encore recevoir $case_buff_dispo buffs'>&nbsp;</li>";
			}
		}
		if(($joueur->get_grade()->get_nb_buff(true)) < 10)
		{
			$RqNextGrade = $db->query("SELECT nom,honneur,rang FROM grade WHERE rang > ".$joueur->get_grade()->get_rang()." ORDER BY rang ASC;");
			while($objNextGrade = $db->read_object($RqNextGrade))
			{
				$tmp = "il faut être ".strtolower($objNextGrade->nom)." pour avoir cette case";
				if($objNextGrade->honneur > 0) { $tmp .= " (encore ".number_format(($objNextGrade->honneur - $joueur->get_honneur()), 0, ".", ".")."pt d&apos;honneur)"; }
				$title_grade[$objNextGrade->rang + 2] = $tmp.".";
			}
			for($b = ($joueur->get_grade()->get_nb_buff() + 1); $b <= 10; $b++)
			{
				echo "<li class='buff_nondispo' title='".$title_grade[$b]."'>&nbsp;</li>";
			}
		}
		echo " </ul>
		</div>
		<br />
		<div id='debuff_list'>";
		if(is_array($buffs))
		{
			echo "<ul>";
			foreach($buffs as $buff)
			{//-- Listing des debuffs
				if($buff->get_debuff() == 1)
				{
					$overlib = str_replace("'", "\'", trim("<ul><li class='overlib_titres'>".$buff->get_nom()."</li><li>".description($buff->get_description(), $buff)."</li><li>Durée ".transform_sec_temp($buff->get_fin() - time())."</li></ul>"));
					echo "<li class='buff'>
						   <img src='image/buff/".$buff->get_type()."_p.png'
								alt='".$buff->get_type()."'
								rel='tooltip'
								data-html='true'
								data-placement='right' 
								title=\"$overlib\"
								 />
						   ".genere_image_buff_duree($buff)."
						  </li>";
				}
			}
		echo " </ul>";
		}

		  echo "</div>";
	echo "</div>";
}
if($joueur->get_groupe() != 0)
{//-- Affichage du groupe si le joueur est groupé
	if(!isset($groupe)) $groupe = new groupe($joueur->get_groupe());

	echo "<div id='joueur_groupe'><div id='joueur_groupe_container'>
			<div id='joueur_groupe_bouton'>
		   <div id='info_groupe' title='Voir les informations de mon groupe.' onclick=\"return envoiInfo('infogroupe.php?id=".$groupe->get_id()."', 'information');\"></div>
		   <div id='mail_groupe' title=\"Envoyer un message à l'ensemble du groupe.\" onclick=\"return envoiInfo('envoimessage.php?id_type=g".$groupe->get_id()."', 'information');\"></div>
		   </div>";
	echo " <ul>";
	$nombre_joueur_groupe = 0;
	foreach($groupe->get_membre_joueur() as $membre)
	{//-- Récupération des infos sur le membre du groupe
		if($joueur->get_id() != $membre->get_id())
		{
      if (map::is_masked_coordinates($membre->get_x(), $membre->get_y()))
        $masked_coords = true;
      else
        $masked_coords = false;

			$membre->poscase = calcul_distance(convert_in_pos($membre->get_x(), $membre->get_y()), convert_in_pos($joueur->get_x(), $joueur->get_y()));
			$membre->pospita = calcul_distance_pytagore(convert_in_pos($membre->get_x(),$membre->get_y()), convert_in_pos($joueur->get_x(), $joueur->get_y()));
			$overlib = "<ul><li class='overlib_titres'>".
        ucwords($membre->get_grade()->get_nom())." ".
        ucwords($membre->get_nom())."</li><li>".
        ucwords($membre->get_race())." - ".
        ucwords($membre->get_classe())." (Niv.".$membre->get_level().
        ")</li><li>HP : ".$membre->get_hp()." / ".
        floor($membre->get_hp_maximum())."</li><li>MP : ".
        $membre->get_mp()." / ".floor($membre->get_mp_maximum())."</li>";
      if (!$masked_coords) $overlib .= "<li>Position : x:".$membre->get_x().
                             ", y:".$membre->get_y()."</li><li>Distance : ".
                             $membre->poscase." - Pytagorienne : ".
                             $membre->pospita."</li>";
			{//-- Récupération des buffs
				$overlib .= "<li>";
				foreach($membre->get_buff() as $buff)
				{
					if($buff->get_debuff() == 0)
					{
						$overlib .= "<img src='image/buff/".$buff->get_type()."_p.png' style='margin:0px 2px;' alt='".$buff->get_type()."' />";
					}
				}
				foreach($membre->get_buff() as $debuff)
				{
					if($debuff->get_debuff() == 1)
					{
						$overlib .= "<img src='image/buff/".$debuff->get_type()."_p.png' style='margin:0px 2px;' alt='".$debuff->get_type()."' />";
					}
				}
				$overlib .= "</li>";
			}
			
			$laptime_last_connexion = time() - $membre->get_dernieraction();
			if($laptime_last_connexion > (21 * 86400))														{ $activite_perso = "badge-inverse"; 	$libelle_activite = "ce joueur est inactif ou banni"; }	
			elseif( ($laptime_last_connexion <= (21 * 86400)) && ($laptime_last_connexion > (1 * 86400)) )	{ $activite_perso = "badge-important"; 	$libelle_activite = "s'est connecté il y a plus d'1 jour."; }	
			elseif( ($laptime_last_connexion <= (1 * 86400)) && ($laptime_last_connexion > (10 * 60)) )		{ $activite_perso = "badge-info"; 	$libelle_activite = "s'est connecté il y a moins d'1 jour."; }	
			elseif($laptime_last_connexion <= (10 * 60))													{ $activite_perso = "badge-success"; 	$libelle_activite = "s'est connecté il y a moins de 10 min."; }	
			else	
			{ $activite_perso = "rouge"; 	$libelle_activite = "impossible de deacute;finir l&apos;activit&eacute; de ce joueur."; }
			if ($membre->get_hp() <= 0) { $joueur_mort = "Le personnage est mort"; } else { $joueur_mort = ""; };
			$overlib .= "<li>$joueur_mort<br/>$libelle_activite</li><li class='overlib_infos'>(Cliquer pour plus d'information)</li>";
			$overlib = str_replace("'", "\'", trim($overlib));

			if($membre->get_hp() <= 0) $image = '<img src="image/interface/mort.png" alt="M" title="Mort" style="margin-left : 1px;" /> ';
			elseif($membre->get_id() == $groupe->get_id_leader()) $image = '<img src="image/icone/couronne.png" alt="C" title="Chef de groupe" /> ';
			else $image = ' ';
			

			
			
			
			echo "<li  onclick=\"envoiInfo('infojoueur.php?ID=".$membre->get_id()."&amp;poscase=".$membre->poscase."', 'information');\">
				   <span class='joueur_groupe_ischef'>".$image."</span>
				   <span class='joueur_groupe_activite'></span>
				   <span class='joueur_groupe_pseudo'>".ucwords($membre->get_nom())." : </span>
				   <span class='joueur_groupe_barre_hp'><div rel='tooltip' data-placement='right' class='progress progress-danger' title='Point de vie : ".$membre->get_hp()."/".floor($membre->get_hp_maximum())."' style='height:5px;'> <div class='bar' style='width: ".($membre->get_hp()/floor($membre->get_hp_maximum())*100)."%;'></div></div></span>
				   <span class='joueur_groupe_barre_mp'><div rel='tooltip' data-placement='right' class='progress' title='Point de magie : ".$membre->get_mp()."/".floor($membre->get_mp_maximum())."'  style='height:5px;'><div class='bar' style='width: ".($membre->get_mp()/floor($membre->get_mp_maximum())*100)."%;'></div></div></span>";
			if ($membre->get_hp() <= 0) { echo "<span class='joueur_groupe_mort'></span>"; } 
			echo " <div class='spacer'></div>
				  </li>";
			$nombre_joueur_groupe++;
		}
	}
	for($i = $nombre_joueur_groupe ; $i <4 ; $i++)
	{
		echo "<li class='inactif'></li>";
	}
	echo " </ul>
		  </div></div>";
}
else
{
$invitation = invitation::create('receveur', $_SESSION['ID']);

//Si il y a une invitation pour le joueur
if (count($invitation) > 0)
{
	$perso = new perso($invitation[0]->get_inviteur());
	echo '
	<div id="joueur_groupe">
	Vous avez reçu une invitation pour grouper de la part de '.$perso->get_nom().'<br />
	<a href="reponseinvitation.php?id='.$invitation[0]->get_id().'&groupe='.$invitation[0]->get_groupe().'&reponse=oui" onclick="return envoiInfo(this.href, \'information\');">Accepter</a> / <a href="reponseinvitation.php?id='.$invitation[0]->get_id().'&reponse=non" onclick="return envoiInfo(this.href, \'information\');">Refuser</a>
	</div>';
}
}
echo "</div>
		";

?>
