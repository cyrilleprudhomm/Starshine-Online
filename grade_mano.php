<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
include_once(root.'class/db.class.php');
include_once(root.'fonction/time.inc.php');
include_once(root.'fonction/action.inc.php');

//Récupère le timestamp en milliseconde de début de création de la page
$debut = getmicrotime();

//Récupération des variables de connexion à la base et connexion à cette base
include_once(root.'connect.php');

//Inclusion du fichier contenant toutes les variables indispensablent
include_once(root.'inc/variable.inc.php');

//Inclusion du fichier contenant toutes les informations sur les races
include_once(root.'inc/race.inc.php');

//Inclusion du fichier contenant toutes les informations sur les classes
include_once(root.'inc/classe.inc.php');

//Inclusion du fichier contenant les traductions
include_once(root.'inc/traduction.inc.php');

//Inclusion du fichier contenant toutes les variables du terrain (nom, pa)
include_once(root.'inc/type_terrain.inc.php');

//Inclusion du fichier contenant toutes les fonctions de base
include_once(root.'fonction/base.inc.php');

//Inclusion du fichier contenant toutes les fonctions concernant les groupes
include_once(root.'fonction/groupe.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer les quètes
include_once(root.'fonction/quete.inc.php');

//Inclusion du fichier contenant les fonctions permettant de gérer l'équipement
include_once(root.'fonction/equipement.inc.php');

//Inclusion du fichier contenant la classe inventaire
include_once(root.'class/inventaire.class.php');

//Groupes du forum
$groupe = array();
$groupe['barbare'][0] = 5;
$groupe['barbare'][1] = 16;
$groupe['barbare'][2] = 27;
$groupe['elfebois'][0] = 6;
$groupe['elfebois'][1] = 17;
$groupe['elfebois'][2] = 29;
$groupe['elfehaut'][0] = 7;
$groupe['elfehaut'][1] = 18;
$groupe['elfehaut'][2] = 30;
$groupe['humain'][0] = 8;
$groupe['humain'][1] = 19;
$groupe['humain'][2] = 31;
$groupe['humainnoir'][0] = 9;
$groupe['humainnoir'][1] = 20;
$groupe['humainnoir'][2] = 28;
$groupe['mortvivant'][0] = 10;
$groupe['mortvivant'][1] = 21;
$groupe['mortvivant'][2] = 32;
$groupe['nain'][0] = 11;
$groupe['nain'][1] = 22;
$groupe['nain'][2] = 33;
$groupe['orc'][0] = 12;
$groupe['orc'][1] = 23;
$groupe['orc'][2] = 34;
$groupe['scavenger'][0] = 13;
$groupe['scavenger'][1] = 24;
$groupe['scavenger'][2] = 35;
$groupe['troll'][0] = 14;
$groupe['troll'][1] = 25;
$groupe['troll'][2] = 36;
$groupe['vampire'][0] = 15;
$groupe['vampire'][1] = 26;
$groupe['vampire'][2] = 37;

include_once(root.'grade.php');
?>