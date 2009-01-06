<?php
$x = $bataille->x;
$y = $bataille->y;

$reperes = array();
//On prend les reperes
foreach($bataille->reperes as $rep)
{
	$reperes[convert_in_pos($rep->x, $rep->y)] = $rep;
}

//-- Champ de vision = 3 par défaut
$champ_vision = 11;
//-- Nombre de case affichées en longueur et largeur
$case_affiche = ($champ_vision * 2) + 1;

{//-- Sert à calculer le point d'origine en haut a gauche pour la carte
	if($x < ($champ_vision + 1))			{ $x_min = 1;		$x_max = $x + ($case_affiche - ($x)); }
	elseif($x > (150 - $champ_vision))		{ $x_max = 150;		$x_min = $x - ($case_affiche - (150 - $x + 1)); }
	else								{ $x_min = $x - $champ_vision;	$x_max = $x + $champ_vision; };
	
	if($y < ($champ_vision + 1))		{ $y_min = 1;		$y_max = $y + ($case_affiche - ($y)); }
	elseif($y > (150 - $champ_vision))	{ $y_max = 150;		$y_min = $y - ($case_affiche - (150 - $y + 1)); }
	else								{ $y_min = $y - $champ_vision; 	$y_max = $y + $champ_vision; }
}	

//On va afficher la carte
$RqMap = $db->query("SELECT * FROM map 
					 WHERE ( (FLOOR(ID / $G_ligne) >= $y_min) AND (FLOOR(ID / $G_ligne) <= $y_max) ) 
					 AND ( ((ID - (FLOOR(ID / $G_colonne) * 1000) ) >= $x_min) AND ((ID - (FLOOR(ID / $G_colonne) * 1000)) <= $x_max) ) 
					 ORDER BY ID;");
					 
echo '<div id="carte" style="width : 605px; height : 610px;">';
{//-- Affichage du bord haut (bh) de la map
	echo "<ul id='map_bord_haut'>
		   <li id='map_bord_haut_gauche' style='width : 20px; height : 20px;' onclick=\"switch_map();\">&nbsp;</li>";
	for ($bh = $x_min; $bh <= $x_max; $bh++)
	{
		if($bh == $x) { $class_x = "id='bord_haut_x' "; } else { $class_x = ""; }; //-- Pour mettre en valeur la position X ou se trouve le joueur
		echo "<li $class_x style='width : 20px; height : 20px;'>$bh</li>";
	}
	echo "</ul>";
}
{//-- Affichage du reste de la map
	$y_BAK = 0;
	$Once = false;
	$case = 0;
	while($objMap = $db->read_object($RqMap))
	{
		$coord = convert_in_coord($objMap->ID);
		$class_map = "decor texl".$objMap->decor;	//-- Nom de la classe "terrain" contenu dans texture.css
		
		if($coord['y'] != $y_BAK)
		{//-- On passe a la ligne
			if($Once) { echo "</ul>"; } else { $Once = true; };
			if($coord['y'] == $y) { $class_y = "id='bord_haut_y' "; } else { $class_y = ""; }; //-- Pour mettre en valeur la position Y ou se trouve le joueur
			echo "<ul class='map' style='height : 20px;'>
			 	   <li $class_y style='width : 20px; height : 20px;'>".$coord['y']."</li>"; //-- Bord gauche de la map
			 
			$y_BAK = $coord['y'];
		}
		$background = "";
		$overlib = "";
		//Repere
		if(array_key_exists($objMap->ID, $reperes)) $repere = $reperes[$objMap->ID]->id_type;
		else $repere = '&nbsp;';
		//Batiment
		if(array_key_exists($objMap->ID, $batiments)) $background = "background-image : url('../image/batiment/".$batiments[$objMap->ID]["image"]."_04.png') !important;";
		
		$border = "border:0px solid ".$Gcouleurs[$objMap->royaume].";";
		echo "<li class='$class_map' style='width : 20px; height : 20px;'>
			   <div class='map_contenu' 
			   		id='marq$case' 
			   		style=\"".$background.$border."width : 20px; height : 20px;\" ";
		echo " 		onclick=\"envoiInfo('gestion_bataille.php?id_bataille=".$bataille->id."&amp;case=".$objMap->ID."&amp;info_case', 'information');\" 
			   >".$repere."</div>
			  </li>";	
		
		$case++;
	}
	echo "</ul>";
}
?>
</div>
<div id="information" style="float : right;">
	INFOS
</div>