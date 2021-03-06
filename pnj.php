<?php // -*- mode: php; tab-width:2 -*-

/*
 * Aide pour les discours des PNJ :
 * Les différents discours sont séparés pas '*****', leur id correspond à leur ordre d'apparition en commençant par 0.
 * Plusieur balises "pseudo-bbscript" existent :
 * - ID : crée un lien vers la partie suivant du discours, ex [ID:1]suite[/ID:1]
 * - quete_finie / QUETEFINI
 * - non_quete
 * - ISQUETE
 * - retour : affiche un lien pour retourner à la description de la case
 * - quete : validation de la quête
 * - quetegroupe : validation de la quête de groupe
 * - prendquete : prise d'une quête
 * - donneitem : donne un item
 * - vendsitem : vends un item
 * - run : lancement fonction personalisée (cf. fonction/pnj.inc.php)
 * - if : IF fonction personalisée (cf. fonction/pnj.inc.php)
 * - ifnot : IFNOT fonction personalisée (cf. fonction/pnj.inc.php)
 * - verifinventaire : validation inventaire
 */
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'inc/fp.php');
$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();
$W_case = $joueur->get_poscase();

if(array_key_exists('reponse', $_GET)) $reponse = $_GET['reponse']; else $reponse = 0;

$id = $_GET['id'];
$requete = 'SELECT * FROM pnj WHERE id = \''.sSQL($id).'\'';  
$req = $db->query($requete);
$row = $db->read_assoc($req);

if ($row['x'] != $joueur->get_x() ||
		$row['y'] != $joueur->get_y()) {
	security_block(URL_MANIPULATION, 'PNJ pas sur la même case');
}

echo '<fieldset><legend>'.$row['nom'].'</legend>';
/*$reponses = explode('*****', nl2br($row['texte']));
$message = preg_replace("`\[ID:([^[]*)\]([^[]*)\[/ID:([^[]*)\]`i", "<li><a href=\"pnj.php?id=".$id."&amp;reponse=\\1&amp;poscase=".$W_case."\" onclick=\"return envoiInfo(this.href, 'information')\">\\2</a></li>", $reponses[$reponse]);
$message = preg_replace("/(\r\n|\r|\n)/", '', $message);*/
//On vérifie si ya une quête pour ce pnj
/*$supp = true;
$quetes_actives = array();
if($joueur->get_quete() != '')
{
	foreach(unserialize($joueur->get_quete()) as $quete)
	{
		if (!is_numeric($quete['id_quete'])) continue;
		$requete = 'SELECT * FROM quete WHERE id = '.$quete['id_quete'];
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$objectif = unserialize($row['objectif']);
		$i = 0;
		$quetes_actives[] = $row['id'];
		foreach($quete['objectif'] as $objectif_fait)
		{
			$total_fait = $objectif_fait->nombre;
			$total = $objectif[$i]->nombre;
			//On vérifie si il peut accéder à cette partie de la quête
			if($total_fait >= $total) $objectif[$i]->termine = true;
			else $objectif[$i]->termine = false;
			/*
			echo $i.'<br />';
			my_dump($objectif[$i]);
			my_dump($objectif_fait);
			echo '<br />';
			*//*
			if (($objectif_fait->requis == '' OR $objectif[$objectif_fait->requis]->termine) AND !$objectif[$i]->termine)
			{
				$cible = $objectif_fait->cible;
			}
			$i++;
		}
	}
}

if($joueur->get_quete_fini() != '')
{
	foreach($quete_fini as $quetef)
	{
		// Nouvelle version
		$message = preg_replace("`\[quete_finie:${quetef}\]([^[]*)\[/quete_finie:${quetef}\]`i", "\\1", $message);
		//On affiche le lien pour la discussion
		$message = preg_replace("`\[QUETEFINI".$quetef.":([^[]*)\]([^[]*)\[/QUETEFINI".$quetef.":([^[]*)\]`i", "<li><a href=\"pnj.php?id=".$id."&amp;reponse=\\1&amp;poscase=".$W_case."\" onclick=\"return envoiInfo(this.href, 'information')\">\\2</a></li>", $message);
		$supp = false;
	}
}
//"`\[non_quete:([0-9]*)\](.*)\[/non_quete:\g1\]`i", $message, $regs)
while (preg_match("`\[non_quete:([^[]*)\]([^[]*)\[/non_quete:([^[]*)\]`i", $message, $regs))
{
	$numq = $regs[1];
	if (in_array($numq, $quetes_actives) == false &&
			in_array($numq, $quete_fini) == false)
	{
		$message = preg_replace("`\[non_quete:${numq}\]([^[]*)\[/non_quete:$numq\]`i",
														 $regs[2], $message);
	}
	else
	{
		$message = preg_replace("`\[non_quete:${numq}\]([^[]*)\[/non_quete:$numq\]`i",
														 '', $message);
	}
}
while (preg_match("`\[ISQUETE:([^[]*)\]([^[]*)\[/ISQUETE:([^[]*)\]`i", $message, $regs))
{
	$numq = $regs[1];
	if (in_array($numq, $quetes_actives))
	{
		$message = preg_replace("`\[ISQUETE:${numq}\]([^[]*)\[/ISQUETE:$numq\]`i",
														 $regs[2], $message);
	}
	else
	{
		$message = preg_replace("`\[ISQUETE:${numq}\]([^[]*)\[/ISQUETE:$numq\]`i",
														 '', $message);
	}
}
//On supprime le lien pour la discussion
$message = preg_replace("`\[QUETE([^[]*)\]([^[]*)\[/QUETE([^[]*)\]`i", "", $message);
//On supprime les autres liens
$message = preg_replace("`\[QUETEFINI([^[]*)\]([^[]*)\[/QUETEFINI([^[]*)\]`i", "", $message);
$message = preg_replace("`\[quete_finie:([^[]*)\]([^[]*)\[/quete_finie:([^[]*)\]`i", "", $message);
$message = preg_replace("`\[retour]`i", "<li><a href=\"informationcase.php?case=".$W_case."\" onclick=\"return envoiInfo(this.href, 'information')\">Retour aux informations de la case</a></li>", $message);
//Validation de la quête
if(preg_match("`\[quete]`i", $message))
{
	verif_action('P'.$id, $joueur, 's');
	$message = preg_replace("`\[quete]`i", "", $message);
}
//Validation de la quête de groupe
if(preg_match("`\[quetegroupe]`i", $message))
{
  if ($joueur->get_groupe() > 0) {
    $groupe = new groupe($joueur->get_groupe());
    foreach ($groupe->get_membre_joueur() as $pj)
      verif_action('P'.$id, $pj, 'g');
  }
  else
    verif_action('P'.$id, $joueur, 's');
	$message = preg_replace("`\[quetegroupe]`i", "", $message);
}
//Prise d'une quête
if(preg_match("`\[prendquete:([^[]*)\]`i", $message, $regs))
{
	prend_quete($regs[1], $joueur);
	$message = preg_replace("`\[prendquete:([^[]*)\]`i", "", $message);
}
//Donne un item
if(preg_match("`\[donneitem:([^[]*)\]`", $message, $regs))
{
	$joueur->prend_objet($regs[1]);
	$message = preg_replace("`\[donneitem:$regs[1]\]`i", "", $message);
	verif_action($regs[1], $joueur, 's');
}
//Vends un item
if(preg_match("`\[vendsitem:([^[\:]*):([^[]*)\]`i", $message, $regs))
{
	if ($joueur->get_star() < $regs[2])
	{
		$replace = "Vous n'avez pas assez de stars !!<br/>";
	}
	else
	{
		$joueur->set_star($joueur->get_star() - $regs[2]);
		$joueur->prend_objet($regs[1]);
		$joueur->sauver();
		$replace = "Vous recevez un objet.<br/>";
		verif_action($regs[1], $joueur, 's');
	}
	$message = preg_replace("`\[vendsitem:$regs[1]:$regs[2]\]`i",
                          $replace, $message);
}
//lancement fonction personalisée (cf. fonction/pnj.inc.php)
while (preg_match("`\[run:([a-z0-9_]+)\]`i", $message, $regs))
{
  include_once('fonction/pnj.inc.php');
  $run = 'pnj_run_'.$regs[1];
  $replace = $run($joueur);
  $message = preg_replace("`\[run:$regs[1]\]`i", $replace, $message);
}
//IF fonction personalisée (cf. fonction/pnj.inc.php)
while (preg_match("`\[if:([a-z0-9_]+)\]`i", $message, $regs))
{
	$markup = 'if:'.$regs[1];
  include_once('fonction/pnj.inc.php');
  $run = 'pnj_if_'.$regs[1];
  $ok = $run($joueur);
  if ($ok) {
    $message = preg_replace("`\[$markup\]`i", '', $message);
    $message = preg_replace("`\[/$markup\]`i", '', $message);
  }
  else {
		// NE PAS OUBLIER le modificateur 's' pour PCRE_DOTALL
		$s = preg_match("`\[$markup\]`i", $message);
		$e = preg_match("`\[/$markup\]`i", $message);
    $message = preg_replace("`\[$markup\].*\[/$markup\]`si", '',
                            $message);
		if (!$s || !$e) die("Erreur de dialogue pnj: id = $id, reponse = $reponse, s = $s, e = $e");
  }
}
//IFNOT fonction personalisée (cf. fonction/pnj.inc.php)
while (preg_match("`\[ifnot:([a-z0-9_]+)\]`i", $message, $regs))
{
	$markup = 'ifnot:'.$regs[1];
  include_once('fonction/pnj.inc.php');
  $run = 'pnj_if_'.$regs[1];
  $ok = $run($joueur);
  if (!$ok) {
    $message = preg_replace("`\[$markup\]`i", '', $message);
    $message = preg_replace("`\[/$markup\]`i", '', $message);
  }
  else {
		// NE PAS OUBLIER le modificateur 's' pour PCRE_DOTALL
		$s = preg_match("`\[$markup\]`i", $message);
		$e = preg_match("`\[/$markup\]`i", $message);
    $message = preg_replace("`\[$markup\].*\[/$markup\]`si", '',
                            $message);
		if (!$s || !$e) die("Erreur de dialogue pnj: id = $id, reponse = $reponse, s = $s, e = $e");
  }
}
//validation inventaire
if(preg_match("`\[verifinventaire:([^[]*)\]`i", $message, $regs))
{
	if (verif_inventaire($regs[1], $joueur) == false) {
		$message = "<h5>Tu te moques de moi, mon bonhomme ?</h5>";
	}
	else {
		$message = preg_replace("`\[verifinventaire:$regs[1]\]`i", "", $message);
	}
}*/

$texte = new texte($row['texte'], texte::pnj);
$texte->set_liens('pnj.php?id='.$id.'&amp;poscase='.$W_case, $W_case, true);
$texte->set_perso($joueur);
$texte->set_id_objet('P'.$id);
echo '<ul>'.$texte->parse($reponse).'</ul>';
echo "</fieldset>";
?>
