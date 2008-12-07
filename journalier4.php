<?php

$mail = '';

include('class/db.class.php');
include('fonction/time.inc.php');
include('fonction/action.inc.php');

//R?cup?re le timestamp en milliseconde de d?but de cr?ation de la page
$debut = getmicrotime();

//R?cup?ration des variables de connexion ? la base et connexion ? cette base
include('connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include('inc/variable.inc.php');

//Inclusion du fichier contenant toutes les informations sur les races
include('inc/race.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include('inc/type_terrain.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include('fonction/base.inc.php');

$ressources = array();

$requete = "SELECT royaume.race as race, info, FLOOR(COUNT(*) / 10) as tot, COUNT(*) as tot_terrain FROM `map` LEFT JOIN royaume ON map.royaume = royaume.id WHERE royaume <> 0 GROUP BY info, royaume";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	if($row['tot'] > 0)
	{
		$typeterrain = type_terrain($row['info']);
		$ressources[$row['race']][$typeterrain[1]] = $row['tot'];
		$terrain[$row['race']][$typeterrain[1]] = $row['tot_terrain'];
	}
}

$ress = array();
$ress['Plaine']['Pierre'] = 4;
$ress['Plaine']['Bois'] = 4;
$ress['Plaine']['Eau'] = 5;
$ress['Plaine']['Sable'] = 2;
$ress['Plaine']['Nourriture'] = 8;
$ress['Plaine']['Star'] = 0;
$ress['Plaine']['Charbon'] = 0;
$ress['Plaine']['Essence Magique'] = 0;

$ress['Forêt']['Pierre'] = 3;
$ress['Forêt']['Bois'] = 8;
$ress['Forêt']['Eau'] = 4;
$ress['Forêt']['Sable'] = 0;
$ress['Forêt']['Nourriture'] = 5;
$ress['Forêt']['Star'] = 0;
$ress['Forêt']['Charbon'] = 0;
$ress['Forêt']['Essence Magique'] = 3;

$ress['Désert']['Pierre'] = 6;
$ress['Désert']['Bois'] = 0;
$ress['Désert']['Eau'] = 0;
$ress['Désert']['Sable'] = 8;
$ress['Désert']['Nourriture'] = 2;
$ress['Désert']['Star'] = 0;
$ress['Désert']['Charbon'] = 2;
$ress['Désert']['Essence Magique'] = 4;

$ress['Montagne']['Pierre'] = 8;
$ress['Montagne']['Bois'] = 4;
$ress['Montagne']['Eau'] = 3;
$ress['Montagne']['Sable'] = 5;
$ress['Montagne']['Nourriture'] = 2;
$ress['Montagne']['Star'] = 0;
$ress['Montagne']['Charbon'] = 0;
$ress['Montagne']['Essence Magique'] = 1;

$ress['Marais']['Pierre'] = 0;
$ress['Marais']['Bois'] = 1;
$ress['Marais']['Eau'] = 1;
$ress['Marais']['Sable'] = 3;
$ress['Marais']['Nourriture'] = 2;
$ress['Marais']['Star'] = 0;
$ress['Marais']['Charbon'] = 4;
$ress['Marais']['Essence Magique'] = 8;

$ress['Terre Maudite']['Pierre'] = 2;
$ress['Terre Maudite']['Bois'] = 2;
$ress['Terre Maudite']['Eau'] = 0;
$ress['Terre Maudite']['Sable'] = 1;
$ress['Terre Maudite']['Nourriture'] = 1;
$ress['Terre Maudite']['Star'] = 0;
$ress['Terre Maudite']['Charbon'] = 8;
$ress['Terre Maudite']['Essence Magique'] = 5;

$ress['Glace']['Pierre'] = 1;
$ress['Glace']['Bois'] = 0;
$ress['Glace']['Eau'] = 8;
$ress['Glace']['Sable'] = 0;
$ress['Glace']['Nourriture'] = 2;
$ress['Glace']['Star'] = 0;
$ress['Glace']['Charbon'] = 2;
$ress['Glace']['Essence Magique'] = 3;

$ress['Route']['Pierre'] = 0;
$ress['Route']['Bois'] = 0;
$ress['Route']['Eau'] = 0;
$ress['Route']['Sable'] = 0;
$ress['Route']['Nourriture'] = 0;
$ress['Route']['Star'] = 30;
$ress['Route']['Charbon'] = 0;
$ress['Route']['Essence Magique'] = 0;

$i = 0;
$key = array_keys($ressources);
foreach($ressources as $res)
{
	$j = 0;
	$keys = array_keys($res);
	while($j < count($res))
	{
		$k = 0;
		$kei = array_keys($ress[$keys[$j]]);
		foreach($ress[$keys[$j]] as $rr)
		{
			$ressource_final[$key[$i]][$kei[$k]] += $rr * $ressources[$key[$i]][$keys[$j]];
			if($kei[$k] == 'Nourriture') $tot_nou += $rr * $ressources[$key[$i]][$keys[$j]];
			$k++;
		}
		$j++;
	}
	$i++;
}

foreach($ressource_final as $key => $value)
{
	$requete = "UPDATE royaume SET pierre = pierre + ".$value['Pierre'].", bois = bois + ".$value['Bois'].", eau = eau + ".$value['Eau'].", sable = sable + ".$value['Sable'].", charbon = charbon + ".$value['Charbon'].", essence = essence + ".$value['Essence Magique'].", star = star + ".$value['Star'].", food = food + ".$value['Nourriture']." WHERE race = '".$key."'";
	$db->query($requete);
}

//Nourriture
$requete = "SELECT ID, race, food FROM royaume WHERE ID != 0";
$req = $db->query($requete);
while($row = $db->read_assoc($req))
{
	$tab_royaume[$row['race']] = array('id' => $row['ID'], 'food' => $row['food']);
}
foreach($tab_royaume as $royaume => $value)
{
	if($royaume['food_necessaire'] < $royaume['food'])
	{
		$requete = "UPDATE royaume SET food = food - ".$royaume['food_necessaire'];
	}
}
?>