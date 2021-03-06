<?php // -*- tab-width:2; mode: php -*- 
if (file_exists('../root.php'))
  include_once('../root.php');

require_once('haut_roi.php');

//check_case('all');
$R = new royaume($Trace[$joueur->get_race()]['numrace']);
if ($R->is_raz()) $RAZ_ROYAUME = true;

if($joueur->get_rang_royaume() != 6 AND $joueur->get_id() != $royaume->get_ministre_militaire())
	echo '<p>Cette page vous est interdite</p>';
else if(!array_key_exists('direction', $_GET))
{
	echo "<div  style='float:right;'><div id='affiche_dist' style='width:375px;'><fieldset>";
	echo "<legend>Distances de pose</legend>";
	echo '<b>Bourgs : </b><ul>';
	echo '<li>Avec un autre bourg : '.floor(7*$royaume->get_facteur_entretien()).'</li>';
	echo '<li>Avec une capitale : 5</li>';
	echo '<b>Forts : </b><ul>';
	echo '<li>Avec un autre de vos forts : '.floor(4*$royaume->get_facteur_entretien()).'</li>';
	echo '<li>Avec un fort d\'un autre peuple : 4</li>';
	echo '<li>Avec une capitale : 7</li>';
	echo "</fieldset></div>";
	echo "<div id='affiche_minimap'>";
	echo "</div></div>";
	if ($RAZ_ROYAUME)
	{
		echo '<div><strong>Gestion impossible quand la capitale est mise à sac</strong></div>';
	}
	echo "<div id='contruction'>";
	
	$req = $db->query("SELECT *, placement.royaume AS r, placement.type FROM placement LEFT JOIN map ON (map.y = placement.y AND placement.x = map.x) WHERE (placement.type = 'drapeau' OR placement.type = 'arme_de_siege') AND placement.royaume != ".$royaume->get_id()." AND map.royaume = ".$royaume->get_id()." AND map.x <= 190 AND map.y <= 190");
	if ($db->num_rows($req)>0)
	{
		echo "<fieldset>";	
		echo "<legend>Liste des drapeaux et Armes en Construction sur votre territoire</legend>";
		$boutique_class = 't1';
		echo "<ul>";		
		while($row = $db->read_assoc($req))
		{			
			$royaume_req = new royaume($row['r']);
			$tmp = transform_sec_temp($row['fin_placement'] - time())." avant fin de construction";
			echo "
			<li class='$boutique_class'>
				<span style='display:block;width:220px;float:left;'>";
			
				if ($row['type'] == 'arme_de_siege')
				{
					$batiment = new batiment($row['id_batiment']);
					
					echo "<img src='../image/batiment/".$batiment->get_image()."_04.png' style='width:19px;vertical-align: top;' alt='".$batiment->get_nom()."' />".$batiment->get_nom();
				}
				else
				{
					echo "<img src='../image/drapeaux/drapeau_".$royaume->get_id().".png' style='width:19px;vertical-align: top;' alt='Drapeau' />".$row['nom'];
				}
				echo "</span>
				<span style='display:block;width:100px;float:left;'>".$Gtrad[$royaume_req->get_race()]."</span>
				<span style='display:block;width:100px;float:left;'>X : ".$row['x']." - Y : ".$row['y']."</span>
			</li>";
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}			
		}
	echo "</ul>";
	echo "</fieldset>";	
	}
	$req = $db->query("SELECT *, construction.royaume AS r, construction.type FROM construction LEFT JOIN map ON (map.y = construction.y AND construction.x = map.x) WHERE construction.type = 'arme_de_siege' AND construction.royaume != ".$royaume->get_id()." AND map.royaume = ".$royaume->get_id()." AND map.x <= 190 AND map.y <= 190");
	if ($db->num_rows($req)>0)
	{
		echo "<fieldset>";	
		echo "<legend>Liste Armes de sieges sur votre territoire</legend>";
		$boutique_class = 't1';
		echo "<ul>";		
		while($row = $db->read_assoc($req))
		{			
			$royaume_req = new royaume($row['r']);
			echo "
			<li class='$boutique_class'   onclick=\"minimap(".$row['x'].",".$row['y'].")\">
				<span style='display:block;width:220px;float:left;'>";
				$batiment = new batiment($row['id_batiment']);
				echo "<img src='../image/batiment/".$batiment->get_image()."_04.png' style='width:19px;vertical-align: top;' alt='".$batiment->get_nom()."' />".$batiment->get_nom();
				echo "</span>
				<span style='display:block;width:100px;float:left;'>".$Gtrad[$royaume_req->get_race()]."</span>
				<span style='display:block;width:100px;float:left;'>X : ".$row['x']." - Y : ".$row['y']."</span>
			</li>";
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}			
		}
	echo "</ul>";
	echo "</fieldset>";	
	}
	
	$req = $db->query("SELECT *, map.royaume AS r FROM placement LEFT JOIN map ON (map.y = placement.y AND placement.x = map.x) WHERE placement.type = 'drapeau' AND placement.royaume = ".$royaume->get_id()." AND map.x <= 190 AND map.y <= 190");
	if ($db->num_rows($req)>0)
	{
		echo "<fieldset>";	
		echo "<legend>Liste de vos drapeaux sur territoire ennemi</legend>";	
		echo "<ul>";
		$boutique_class = 't1';
		while($row = $db->read_assoc($req))
		{
			$royaume_req = new royaume($row['r']);
			if (empty($Gtrad[$royaume_req->get_race()])){$nom = 'Neutre';}else{$nom = $Gtrad[$royaume_req->get_race()];}
			$tmp = transform_sec_temp($row['fin_placement'] - time())."avant fin de construction";
			echo "
			<li class='$boutique_class' onclick=\"minimap(".$row['x'].",".$row['y'].")\" onmousemove=\"".make_overlib($tmp)."\" onmouseout='return nd();'>
				<span style='display:block;width:420px;float:left;'>
					<img src='../image/drapeaux/drapeau_".$royaume->get_id().".png' style='width:19px;' alt='Drapeau' /> ".$row['nom']." chez les ".$nom."
				</span>
				<span style='display:block;width:100px;float:left;'>X : ".$row['x']." - Y : ".$row['y']."</span>
			</li>";			
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}						
		}
		echo "</ul>";
		echo "</fieldset>";
	}
	$requete = $db->query("SELECT id FROM construction WHERE royaume = ".$royaume->get_id()." AND x <= 190 AND y <= 190");
	if ($db->num_rows($requete)>0)
	{
		echo "<fieldset>";	
		echo "<legend>Liste de vos batiments</legend>";	
		echo "<ul>";
		$boutique_class = 't1';		
		while($row = $db->read_assoc($requete))
		{
			$construction = new construction($row['id']);

			$tmp = "HP - ".$construction->get_hp();
			echo "
			<li class='$boutique_class'  onclick=\"minimap(".$construction->get_x().",".$construction->get_y().")\">
				<span style='display:block;width:320px;float:left;'>
					<img src='../image/batiment_low/".$construction->get_image()."_04.png' style='vertical-align : top;' title='".$construction->get_nom()."' /> ".$construction->get_nom();
			$batiment = new batiment($construction->get_id_batiment());
			
			//On peut l'upragder si il y a un suivant
			if($batiment->get_suivant() && !$joueur->is_buff('debuff_rvr'))
			{
				$batiment_suivant = new batiment($batiment->get_suivant());
				
				if ($batiment_suivant->get_cond1() < (time() - $construction->get_date_construction()))
				{
					echo ' - <a href="construction.php?direction=up_construction&amp;id='.$row['id'].'" onclick="if(confirm(\'Voulez-vous upgrader ce '.$construction->get_nom().' ?\')) return envoiInfo(this.href, \'message_confirm\'); else return false;">Upgrader - '.$batiment_suivant->get_cout().' stars</a>';
				}
				else
				{
					$tmp = transform_sec_temp($batiment_suivant->get_cond1() - (time() - $construction->get_date_construction()));
					echo "<span style='font-style: italic ;font-size:8pt;'> - update possible dans $tmp</span>";
				}
			}
			echo "</span>";
				
			//my_dump($batiment);
			//my_dump($construction);
			echo "<span style='display:block;width:100px;float:left;'> X : ".$construction->get_x()." - Y : ".$construction->get_y()."</span>";
			$longueur = round(100 * ($construction->get_hp() / $batiment->get_hp()), 2);
			echo "<img style='display:block;width:100px;float:left;height:6px;padding-top:5px;' src='genere_barre_hp.php?longueur=".$longueur."' alt='".$construction->get_hp()." / ".$batiment->get_hp()."' title='".$construction->get_hp()." / ".$batiment->get_hp()."'>";
			
			if( $construction->get_hp() >= $batiment->get_hp() * $G_prct_vie_suppression )
			{
  			echo "<span style='display:block;width:30px;float:left;cursor:pointer;padding-left:4px;'>
  					<a onclick=\"if(confirm('Voulez-vous supprimer ce ".$construction->get_nom()." ?')) {return envoiInfo('construction.php?direction=suppr_construction&amp;id=".$construction->get_id()."', 'message_confirm');} else {return false;};\"><img src='../image/interface/croix_quitte.png' alt='suppression' title='Supprimer.'/></a>
  				</span>";
      }
      else
			{
  			echo "<span style='display:block;width:30px;float:left;cursor:pointer;padding-left:4px;'>
  					<img src='../image/interface/croix_quitte_gris.png'/ alt='suppression impossibe' title='Vous ne pouvez pas supprimer un bâtiment qui a moins de ".floor($G_prct_vie_suppression*100)."% de ses HP.'></span>";
      }
			echo "</li>";
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}									
		}
		echo "</ul>";
		echo "</fieldset>";		
	}

	$req = $db->query("SELECT objet_royaume.*, COUNT(depot_royaume.id_objet) AS nbr_objet, depot_royaume.id_objet, depot_royaume.id AS id_depot FROM depot_royaume, objet_royaume WHERE depot_royaume.id_objet = objet_royaume.id AND id_royaume = ".$royaume->get_id()." GROUP BY depot_royaume.id_objet ASC");         
	if ($db->num_rows($req)>0)
	{
		echo "<fieldset>";	
		echo "<legend>Liste des objets disponibles dans votre depot militaire</legend>";	
		echo "<ul>";
		$boutique_class = 't1';
		while($row = $db->read_assoc($req))
		{
			echo "
			<li class='$boutique_class'>
				<span style='display:block;width:420px;float:left;'>
					".$row['nom']." : ".$row['nbr_objet']." 
				</span>
			</li>";			
			if ($boutique_class == 't1'){$boutique_class = 't2';}else{$boutique_class = 't1';}						
		}
		echo "</ul>";
		echo "</fieldset>";
	}
}
elseif ($RAZ_ROYAUME)
{
	echo '<h5>Gestion impossible quand la capitale est mise à sac</h5>';
	exit(0);
}
elseif($joueur->is_buff('debuff_rvr'))
{
	echo '<h5>RvR impossible pendant la trêve</h5>';
}
elseif($_GET['direction'] == 'suppr_construction')
{
	$construction = new construction($_GET['id']);
	//On vérifie que c'est le bon royaume
	if($construction->get_royaume() == $royaume->get_id())
	{
		$batiment = new batiment($construction->get_id_batiment());
		//On vérifie que la construction a plus de 10% de ses PV max
		if($construction->get_hp() > ($batiment->get_hp() * $G_prct_vie_suppression))
		{
			$requete = "DELETE FROM construction WHERE id = ".sSQL($_GET['id']);
			if($db->query($requete))
			{
				echo '<h6>La construction a été correctement supprimée.</h6>';


				echo "<script type='text/javascript'>
					// <![CDATA[\n

					envoiInfo('construction.php','contenu_jeu');
						// ]]>
				  </script>";


				//On supprime un bourg au compteur
				if($row[0] == 'bourg')
				{
					supprime_bourg($row[1]);
				}
			}
			else echo '<h5>Erreur dans la requête</h5>';
		}
		else echo '<h5>Ce batiment ne vous appartient pas</h5>';
	}
}
elseif($_GET['direction'] == 'up_construction')
{
	$construction = new construction(sSQL($_GET['id']));
	$ancien_batiment = new batiment($construction->get_id_batiment());
	$batiment = new batiment($ancien_batiment->get_suivant());
	if($ancien_batiment->get_suivant() && $royaume->get_star() >= $batiment->get_cout() &&
		 $batiment->get_cond1() < (time() - $construction->get_date_construction()))
	{
		// On modifie la contruction
		$construction->set_id_batiment($batiment->get_id());
		$construction->set_nom($batiment->get_nom());
		$construction->set_image($batiment->get_image());
		$construction->set_date_construction(time());
		$construction->set_hp($construction->get_hp() + $batiment->get_hp() - $ancien_batiment->get_hp());
		$construction->set_point_victoire($batiment->get_point_victoire());
		$construction->sauver();
		
		$royaume->set_star($royaume->get_star() - $batiment->get_cout());
		$royaume->sauver();
		echo '<h6>La construction a été correctement upgradée</h6>';
		/*
		//On migre les anciens extracteurs vers le nouveau bourg
		$requete = "UPDATE construction SET rechargement = ".$construction_bourg->get_id()." WHERE type = 'mine' AND rechargement = ".sSQL($_GET['id']);
		$db->query($requete);
		$requete = "UPDATE placement SET rez = ".$construction_bourg->get_id()." WHERE type = 'mine' AND rez = ".sSQL($_GET['id']);
		$db->query($requete);
			*/
	}
	else
	{
		echo "<h5>Construction impossible à upgrader</h5>";
	}


}
echo "</div>";
?>
