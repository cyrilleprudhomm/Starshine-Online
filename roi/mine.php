<?php
require('haut_roi.php');
include('../class/construction.class.php');
include('../class/bourg.class.php');
include('../class/mine.class.php');
include('../class/placement.class.php');

if(array_key_exists('id', $_GET))
{
	$bourg = new bourg($_GET['id']);
	$bourg->get_mines(true);
	$bourg->get_placements();
	$bourg->get_mine_total();
	$x = $bourg->x;
	$y = $bourg->y;
	$batiments = array_merge($bourg->mines, $bourg->placements);
	$batiments[] = $bourg;
	?>
	<div id="map_mine">
	<?php
	$map = new map($x, $y, 5, '../', false, 'high');
	$map->set_batiment_objet($batiments);
	$map->set_onclick("envoiInfo('mine.php?case=%%ID%%&amp;id_bourg=".$bourg->id."', 'info_mine');");
	$map->affiche();
	?>
	</div>
	<div id="infos">
		<div id="info_bourg">
			Type : <?php echo $bourg->nom; ?><br />
			X : <?php echo $bourg->x; ?><br />
			Y : <?php echo $bourg->y; ?><br />
			Mines : <?php echo $bourg->mine_total; ?> / <?php echo $bourg->mine_max; ?>
			<ul style="margin-left : 15px;">
			<?php
				foreach($bourg->mines as $mine)
				{
					$mine->get_evolution();
					$overlib = 'Pierre : '.$mine->ressources['Pierre'].'<br />Bois : '.$mine->ressources['Bois'].'<br />Eau : '.$mine->ressources['Eau'].'<br />Sable : '.$mine->ressources['Sable'].'<br />Nourriture : '.$mine->ressources['Nourriture'].'<br />Charbon : '.$mine->ressources['Charbon'].'<br />Essence Magique : '.$mine->ressources['Essence Magique'].'<br />Star : '.$mine->ressources['Star'];
					echo '
					<li onmouseover="'.make_overlib($overlib).'" onmouseout="return nd();">
						'.$mine->nom.' - X : '.$mine->x.' - Y : '.$mine->y.' - <a href="mine.php?mine='.$mine->id.'&amp;up" onclick="return envoiInfo(this.href, \'info_mine\');">Evoluer ('.$mine->evolution['cout'].' stars)</a> - <a href="mine.php?mine='.$mine->id.'&amp;suppr" onclick="return envoiInfo(this.href, \'info_mine\');">X</a>
					</li>';
				}
			?>
			</ul>
			En construction
			<ul style="margin-left : 15px;">
			<?php
				foreach($bourg->placements as $placement)
				{
					echo '
					<li onmouseover="'.make_overlib($overlib).'" onmouseout="return nd();">
						'.$placement->nom.' - X : '.$placement->x.' - Y : '.$placement->y.' - fin dans '.transform_sec_temp($placement->fin_placement - time()).'
					</li>';
				}
			?>
			</ul>
		</div>
		<div id="info_mine">
		</div>
	</div>
	<?php
}
//Info d'une case
elseif(array_key_exists('case', $_GET))
{
	$coord = convert_in_coord($_GET['case']);
	check_case($coord);
	echo 'CASE : X : '.$coord['x'].' - Y : '.$coord['y'].'<br />';
	$bourg = new bourg($_GET['id_bourg']);
	$bourg->get_mines();
	$bourg->get_placements();
	$bourg->get_mine_total();
	if($bourg->mine_max > $bourg->mine_total)
	{
		//On vérifie que la case appartient bien au royaume
		$requete = "SELECT ID FROM map WHERE ID = ".$_GET['case']." AND royaume = ".$R['ID'];
		$db->query($requete);
		if($db->num_rows == 0)
		{
			echo 'Construction impossible, ce terrain ne vous appartient pas';
		}
		else
		{
			//On vérifie qu'il y a pas déjà une construction sur cette case
			$requete = "SELECT id FROM construction WHERE x = ".$coord['x']." AND y = ".$coord['y'];
			$db->query($requete);
			if($db->num_rows > 0)
			{
				echo 'Construction impossible, il y a déjà un batiment';
			}
			else
			{
				//On vérifie qu'il y a pas déjà une construction sur cette case
				$requete = "SELECT id FROM placement WHERE x = ".$coord['x']." AND y = ".$coord['y'];
				$db->query($requete);
				if($db->num_rows > 0)
				{
					echo 'Construction impossible, il y a déjà un batiment en construction';
				}
				//On peut construire une mine
				else
				{
					$requete = "SELECT id, nom, cout, bonus1, bonus2 FROM batiment WHERE type = 'mine' AND cond1 = 0";
					$req = $db->query($requete);
					?>
					Quel mine voulait vous construire ?<br />
					<select name="type_mine" id="type_mine">
					<?php
					while($row = $db->read_assoc($req))
					{
						$description = '';
						if($row['bonus2'] != 0)
						{
							switch($row['bonus2'])
							{
								case 1 :
									$description = 'Pierre x'.$row['bonus1'];
								break;
								case 2 :
									$description = 'Bois x'.$row['bonus1'];
								break;
								case 3 :
									$description = 'Eau x'.$row['bonus1'];
								break;
								case 4 :
									$description = 'Sable x'.$row['bonus1'];
								break;
								case 5 :
									$description = 'Nourriture x'.$row['bonus1'];
								break;
								case 6 :
									$description = 'Star x'.$row['bonus1'];
								break;
								case 7 :
									$description = 'Charbon x'.$row['bonus1'];
								break;
								case 8 :
									$description = 'Essence Magique x'.$row['bonus1'];
								break;
							}
						}
						else $description = 'Toute ressources x'.$row['bonus1'];
						echo '<option value="'.$row['id'].'">'.$row['nom'].' - '.$row['cout'].' stars ('.$description.')</option>';
					}
					?>
					</select>
					<input type="button" onclick="envoiInfo('mine.php?bourg=<?php echo $_GET['id_bourg']; ?>&amp;x=<?php echo $coord['x']; ?>&amp;y=<?php echo $coord['y']; ?>&amp;add=' + $('type_mine').value, 'info_mine');" value="Valider" />
					<?php
				}
			}
		}
	}
	else
	{
		echo 'Construction impossible, ce bourg ne peut plus avoir de mine associée';
	}
}
//Ajout d'une mine
elseif(array_key_exists('add', $_GET))
{
	$bourg = new bourg($_GET['bourg']);
	$bourg->get_mine_total();

	if($bourg->mine_total < $bourg->mine_max)
	{
		$requete = "SELECT nom, hp,temps_construction, cout FROM batiment WHERE id = ".$_GET['add'];
		$req = $db->query($requete);
		$row = $db->read_assoc($req);

		//On vérifie si on a assez de stars
		if($R['star'] >= $row['cout'])
		{
			$distance = calcul_distance(convert_in_pos($Trace[$R['race']]['spawn_x'], $Trace[$R['race']]['spawn_y']), convert_in_pos($_GET['x'], $_GET['y']));
			$time = time() + ($row['temps_construction'] * $distance);

			$placement = new placement();
			$placement->id_royaume = $R['ID'];
			$placement->id_batiment = $_GET['add'];
			$placement->x = $_GET['x'];
			$placement->y = $_GET['y'];
			$placement->hp = $row['hp'];
			$placement->nom = $row['nom'];
			$placement->rez = $_GET['bourg'];
			$placement->type = 'mine';
			$placement->fin_placement = $time;
			$placement->sauver();
			
			//On enlève les stars au royaume
			$requete = "UPDATE royaume SET star = star - ".$row['cout']." WHERE ID = ".$R['ID'];
			$db->query($requete);
		}
		else
		{
			echo 'Vous n\'avez pas assez de stars';
		}
	}
}
//Ajout d'une mine
elseif(array_key_exists('up', $_GET))
{
	$mine = new mine($_GET['mine']);
	$mine->get_evolution();


	//On vérifie si on a assez de stars
	if($R['star'] >= $mine->evolution['cout'])
	{
		$mine->id_batiment = $mine->evolution['id'];
		$mine->hp = round(($mine->hp / $mine->get_hp_max()) * $mine->evolution['hp']);
		$mine->nom = $mine->evolution['nom'];
		$mine->sauver();
	}
	else
	{
		echo 'Vous n\'avez pas assez de stars';
	}
}
elseif(array_key_exists('suppr', $_GET))
{
	$mine = new mine($_GET['mine']);
	if($mine->id_royaume == $R['ID']) $mine->supprimer();
}
else
{
	$requete = "SELECT id, royaume, id_batiment, x, y, hp, nom, type, rez, rechargement, image FROM construction WHERE type = 'bourg' AND royaume = ".$R['ID'];
	$req = $db->query($requete);
	
	?>
	<ul>
	<?php
	while($row = $db->read_assoc($req))
	{
		$bourg = new bourg($row);
		$bourg->get_mines();
		$bourg->get_placements();
		$bourg->get_mine_total();
		echo '<li><a href="mine.php?id='.$bourg->id.'" onclick="return envoiInfo(this.href, \'conteneur\');">'.$bourg->nom.'</a> - X : '.$bourg->x.' - Y : '.$bourg->y.' - ('.$bourg->mine_total.' / '.$bourg->mine_max.')</li>';
		if(count($bourg->mines) > 0)
		{
		?>
		<ul style="margin-left : 15px;">
		<?php
			foreach($bourg->mines as $mine)
			{
				echo '<li>'.$mine->nom.' - X : '.$mine->x.' - Y : '.$mine->y.'</li>';
			}
		?>
		</ul>
		<ul style="margin-left : 15px;">
		<?php
			foreach($bourg->placements as $placement)
			{
				echo '<li>'.$placement->nom.' - X : '.$placement->x.' - Y : '.$placement->y.' - fin dans '.transform_sec_temp($placement->fin_placement - time()).'</li>';
			}
		?>
		</ul>
		<?php
		}
	}
	?>
	</ul>
<?php
}
?>