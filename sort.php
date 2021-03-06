<?php // -*- mode: php -*-
if (file_exists('root.php'))
include_once('root.php');

include_once(root.'haut_ajax.php');

if(array_key_exists('tri', $_GET)) $tris = $_GET['tri']; else $tris = 'favoris';

if(array_key_exists('type', $_GET))	$type_cible = $_GET['type'];
else $type_cible = 'joueur';

if(array_key_exists('lanceur', $_GET)) $type_lanceur = $_GET['lanceur'];
else $type_lanceur = 'joueur';

$joueur = new perso($_SESSION['ID']);

switch($type_cible)
{
  case 'joueur':
    if(array_key_exists('id_joueur', $_GET)) $perso = new perso($_GET['id_joueur']);
    else $perso = new perso($joueur->get_id());
     
    $perso->check_perso(false);
    $cible = $perso;
    break;
  case 'monstre':
    $map_monstre = new map_monstre($_GET['id_monstre']);
    $monstre = new monstre($map_monstre->get_type());
    $monstre->hp_max = $monstre->get_hp();
    $monstre->set_hp($map_monstre->get_hp());
    $monstre->x = $map_monstre->get_x();
    $monstre->y = $map_monstre->get_y();
    $cible = new entite('monstre', $monstre);
    $cible->set_id($map_monstre->get_id());
    break;
}

$lanceur_url = '';
switch($type_lanceur)
{
  case 'joueur':
    $lanceur = &$joueur;
    $possible_augmentation = true;
    break;
  case 'monstre':
    $lanceur = new pet($_GET['id_pet']);
    $possible_augmentation = false;
    // Check des spells du mob
    $monstre = new monstre($lanceur->get_id_monstre());
    $spells = explode(';', $monstre->get_sort_dressage());
    if (!in_array("s$_GET[ID]", $spells)) security_block(URL_MANIPULATION);
    $lanceur_url = "&amp;lanceur=monstre&amp;id_pet=$_GET[id_pet]";
    break;
}
if($type_lanceur == 'joueur') include_once ('livre.php');
?>
<hr>
<?php
$lancement = false;
$buff = false;
$debuff = false;

if($joueur->get_groupe() != 0) $groupe_joueur = new groupe($joueur->get_groupe());

if (isset($_GET['ID']) && !$joueur->is_buff('bloque_sort'))
{
  $no_req = false;
  //$sort = new sort_jeu($_GET['ID']);
  $sort = sort_jeu::factory($_GET['ID']);

  if ($type_lanceur == 'joueur') {
    // Check des spells du joueur
    $spells = explode(';', $joueur->get_sort_jeu());
    if (!in_array($_GET['ID'], $spells)) security_block(URL_MANIPULATION);
    // Check prérequis
    $prerequis = explode(';', $sort->get_requis());
    foreach ($prerequis as $requis) {
      $regs = array();
      if (mb_ereg('^classe:(.*)$', $requis, $regs)) {
        if ($regs[1] != mb_strtolower($joueur->get_classe())) {
          print_debug("La classe $regs[1] est requise pour ce sort (".
          $joueur->get_classe().")");
          $no_req = true;
        }
      }
      if (mb_ereg('^([0-9]+)$', $requis, $regs)) {
        if (!in_array($regs[1], explode(';', $joueur->get_sort_jeu()))) {
          print_debug("Il vous manque le sort $regs[1] pour lancer ce sort");
          $no_req = true;
        }
      }
    }
    if ($sort->get_incantation()*$joueur->get_facteur_magie() > $joueur->get_incantation() && $sort->get_special() == false) {
      print_debug("Il vous faut ".$sort->get_incantation()*$joueur->get_facteur_magie()." en incantation pour lancer ce sort");
      $no_req = true;
    }
  }

  $W_distance = calcul_distance_pytagore($cible->get_pos(), $joueur->get_pos());

  if ($no_req) {
    echo 'Vous n\'avez pas les pré-requis pour lancer ce sort !';
  }
  elseif($W_distance > $sort->get_portee()) {
    echo 'Vous êtes trop loin pour lancer ce sort !';
  }
  elseif($joueur->is_buff('petrifie'))
  {
    echo 'Vous êtes pétrifié, vous ne pouvez pas lancer de sort.';
  }
  else
  {
    if(array_key_exists('groupe', $_GET) AND $_GET['groupe'] == 'yes')
    $groupe = true;
    /*elseif ($sort->get_cible() == 3) {
      $force_groupe = true;
      $groupe = false;
    }*/
    else $groupe = false;
    //Vérification que c'est un buff de groupe
    $sortpa_base = $sort->get_pa();
    $sortmp_base = $sort->get_mp();

    //Vérification que le joueur a le droit aux sorts de groupe
    if ($groupe &&
    !($joueur->is_competence('sort_groupe') ||
    $joueur->is_competence('sort_groupe_'.$sort->get_comp_assoc()) ||
    $type_lanceur == 'monstre'))
    security_block(URL_MANIPULATION, 'Sort de groupe non autorisé');

    // Pas d'affinité si c'est le pet qui lance le sort ou pour les sorts speciaux
    if($type_lanceur != "monstre" && $sort->get_special() == false)
    {
      $joueur->check_sort_jeu_connu($_GET['ID']);
      $sortpa = round($sort->get_pa() * $joueur->get_facteur_magie());
      $sortmp = round($sort->get_mp() * (1 - (($Trace[$joueur->get_race()]['affinite_'.$sort->get_comp_assoc()] - 5) / 10)));
      //Réduction du cout par concentration
      if($joueur->is_buff('buff_concentration', true)) $sortmp = ceil($sortmp * (1 - ($joueur->get_buff('buff_concentration','effet') / 100)));
      //Coût en MP * 1.5 si sort de groupe
      if($groupe) $sortmp = ceil($sortmp * 1.5);
    }
    else
    {
      $sortpa = $sortpa_base;
      $sortmp = $sortmp_base;
      //Réduction du cout par concentration
      if($joueur->is_buff('buff_concentration', true)) $sortmp = ceil($sortmp * (1 - ($joueur->get_buff('buff_concentration','effet') / 100)));
      //Coût en MP * 1.5 si sort de groupe
      if($groupe) $sortmp = ceil($sortmp * 1.5);
    }

    if ($joueur->is_buff('buff_contagion')) {
      if (mb_ereg('^maladie_', $sort->get_type())) {
        $contagion = $joueur->get_buff('buff_contagion');
        print_debug("réduction de coût par la contagion (depuis $sortpa/$sortmp)");
        $sortpa -= $contagion->get_effet();
        $sortmp -= $contagion->get_effet2();
        if ($sortpa < 1) $sortpa = 1;
        if ($sortmp < 0) $sortmp = 0;
        print_debug("-> $sortpa/$sortmp");
      }
    }

    $action = false;

    if($joueur->get_pa() < $sortpa) echo '<h5>Pas assez de PA</h5>';
    elseif($lanceur->get_mp() < $sortmp) echo '<h5>Pas assez de mana</h5>';
    elseif($lanceur->get_hp() <= 0) echo '<h5>Vous êtes mort</h5>';
    else
    {
      $lancement = $sort->lance($joueur, $cible, $groupe, $lanceur_url, $type_cible);
    }
    //On fait le final si le lancement est réussi
    if($lancement)
    {
      $joueur->set_pa($joueur->get_pa() - $sortpa);
      $lanceur->set_mp($lanceur->get_mp() - $sortmp);
      if($possible_augmentation)
      {
        //Augmentation des compétences
        $difficulte_sort = diff_sort($sort->get_difficulte() * 1.1, $joueur, 'incantation', $sortpa_base, $sortmp_base);
        $augmentation = augmentation_competence('incantation', $joueur, $difficulte_sort);
        if ($augmentation[1] == 1)
        {
          $joueur->set_incantation($augmentation[0]);
        }
        $difficulte_sort = diff_sort($sort->get_difficulte() * 1.1, $joueur, $sort->get_comp_assoc(), $sortpa_base, $sortmp_base);
        $augmentation = augmentation_competence($sort->get_comp_assoc(), $joueur, $difficulte_sort);
        if ($augmentation[1] == 1)
        {
          $joueur->set_comp($sort->get_comp_assoc(), $augmentation[0]);
        }
      }
      $joueur->sauver();
      $lanceur->sauver();

      // Augmentation du compteur de l'achievement
      if($buff)
      {
        $achiev = $joueur->get_compteur('buff');
        $achiev->set_compteur($achiev->get_compteur() + 1);
        $achiev->sauver();
      }
      elseif($debuff)
      {
        $achiev = $joueur->get_compteur('debuff');
        $achiev->set_compteur($achiev->get_compteur() + 1);
        $achiev->sauver();
      }
    }
    if($groupe) $cible = $joueur;
    if($type_lanceur == 'joueur') echo '<br /><a href="sort.php?type='.$type_cible.'&amp;id_'.$type_cible.'='.$cible->get_id().'" onclick="return envoiInfo(this.href, \'information\');">Revenir au livre de sort</a>';
    else echo '<br /><a href="gestion_monstre.php" onclick="return envoiInfo(this.href, \'information\');">Revenir à la gestion des monstres</a>';
  }
}
elseif($joueur->is_buff('bloque_sort'))
{
  echo 'Vous êtes sous vunérabilité, vous ne pouvez plus lancer de sorts hors combat.';
}
elseif($type_lanceur == 'joueur')
{
  if(array_key_exists('action', $_GET))
  {
    switch($_GET['action'])
    {
      case 'favoris' :
        $requete = "INSERT INTO sort_favoris(id_sort, id_perso) VALUES(".sSQL($_GET['id']).", ".$joueur->get_id().")";
        $db->query($requete);
        break;
      case 'delfavoris' :
        $requete = "DELETE FROM sort_favoris WHERE id_sort =  ".sSQL($_GET['id'])." AND id_perso = ".$joueur->get_id();
        $db->query($requete);
        break;
    }
  }
  $i = 0;
  $type = '';
  $magies = array('favoris');
  $magie = '';
  $requete = "SELECT * FROM sort_jeu GROUP BY comp_assoc";
  $req = $db->query($requete);
  while($row = $db->read_array($req))
  {
    if($magie != $row['comp_assoc'])
    {
      $magie = $row['comp_assoc'];
      $magies[] = $row['comp_assoc'];
    }
  }
  $groupe_href = '&amp;type='.$type_cible.'&amp;id_'.$type_cible.'='.$cible->get_id();
  foreach($magies as $magie)
  {
    echo '<a href="sort.php?tri='.$magie.$groupe_href.'" onclick="return envoiInfo(this.href, \'information\');"><img src="image/icone_'.$magie.'.png" alt="'.$Gtrad[$magie].'" title="'.$Gtrad[$magie].'" style="vertical-align : middle;" onmouseover="this.src = \'image/icone/'.$magie.'hover.png\'" onmouseout="this.src = \'image/icone_'.$magie.'.png\'" /></a> ';
  }
  echo 'Cible : '.$cible->get_nom();
  $where = '';

  if(array_key_exists('tri', $_GET))
  $where = 'comp_assoc = \''.$_GET['tri'].'\'';
  else
  $_GET['tri'] = 'favoris';

  if($_GET['tri'] == 'favoris')
  $where = 'id IN (SELECT id_sort FROM sort_favoris WHERE id_perso = \''.$joueur->get_id().'\')';

  $test = false;
  $sorts = sort_jeu::create('', '', 'comp_assoc ASC, type ASC', false, ''.$where);
  //$req = $db->query($requete);
  $magie = '';
  echo '<table width="97%" class="information_case">';
  foreach($sorts as $sort)
  {
    if ($sort->get_special() == false)
    {
      $sortpa = round($sort->get_pa() * $joueur->get_facteur_magie());
      $sortmp = round($sort->get_mp() * (1 - (($Trace[$joueur->get_race()]['affinite_'.$sort->get_comp_assoc()] - 5) / 10)));
    }
    else
    {
      $sortpa = $sort->get_pa();
      $sortmp = $sort->get_mp();
    }

    //Réduction du cout par concentration
    if($joueur->is_buff('buff_concentration', true)) $sortmp = ceil($sortmp * (1 - ($joueur->get_buff('buff_concentration','effet') / 100)));
    if($magie != $sort->get_comp_assoc())
    {
      $magie = $sort->get_comp_assoc();
      $type = '';
      echo '<tr><td colspan="6"><h3>'.$Gtrad[$magie].'</h3></td></tr>';
    }
    if(in_array($sort->get_id(), explode(';',$joueur->get_sort_jeu())))
    {
      $image = image_sort($sort->get_type());
      $incanta = $sort->get_incantation();
      echo '
			<div style="z-index: 3;">
				<tr>';
      //On ne peut uniquement faire que les sorts qui nous target ou target tous le groupe
      $affiche = false;
      if($type_cible == 'joueur')
      {
        $sort_groupe = false;
        if($cible->get_id() == $joueur->get_id())
        {
          $cond = ($sort->get_cible() == comp_sort::cible_perso OR $sort->get_cible() == comp_sort::cible_case OR $sort->get_cible() == comp_sort::cible_unique OR $sort->get_cible() == comp_sort::cible_groupe OR $sort->get_cible() == comp_sort::cible_9cases) && $sort->get_type() != 'rez';
          if($cond)
          $sort_groupe = true;
        }
        else
        $cond = ($sort->get_cible() == comp_sort::cible_unique OR $sort->get_cible() == comp_sort::cible_autre || $sort->get_cible() == comp_sort::cible_autregrp || $sort->get_cible() == comp_sort::cible_9cases);

        if($cond)
        {
          $href = 'envoiInfo(\'sort.php?ID='.$sort->get_id().'&amp;type=joueur&amp;id_joueur='.$cible->get_id().'\', \'information\')';
          $href2 = 'envoiInfo(\'sort.php?ID='.$sort->get_id().'&amp;groupe=yes&amp;type=joueur&amp;id_joueur='.$cible->get_id().'\', \'information\')';
          $color = '#444';
          $cursor = 'cursor : pointer;';
          $affiche = true;
        }
      }
      else if($type_cible == 'monstre')
      {
        if(($sort->get_cible() == comp_sort::cible_unique OR $sort->get_cible() == comp_sort::cible_autre || $sort->get_cible() == comp_sort::cible_autregrp ) AND $sort->get_type() != 'rez')
        {
          $href = 'envoiInfo(\'sort.php?ID='.$sort->get_id().'&amp;type=monstre&amp;id_monstre='.$cible->get_id().'\', \'information\')';
          $color = '#444';
          $cursor = 'cursor : pointer;';
          $affiche = true;
        }
      }
      if(!$affiche)
      {
        $href = '';
        $href2 = '';
        $cursor = '';
        $color = 'black';
      }
      ?>
<td style="width: 36px;"><?php echo $image; ?>
</td>
<td><span style="<?php echo $cursor; ?>; text-decoration : none; color : <?php echo $color; ?>;" onclick="<?php echo $href; ?>; return nd();" onmouseover="return <?php echo make_overlib(description($sort->get_description(), $sort).'<br/><span class=&quot;xmall&quot;>Incantation : '.$incanta.'</span>'); ?>" onmouseout="return nd();">
		<strong><?php echo $sort->get_nom(); ?> </strong> </span>
</td>
      <?php
      echo '
			<td>
				<span class="xsmall"> '.$sortpa.' PA 
			</td>
			<td>
				'.$sortmp.' MP
			</td> 
			<td>';
      if($sort->get_cible() == 2 && $sort_groupe)
      if($joueur->is_competence('sort_groupe')|| $joueur->is_competence('sort_groupe_'.$sort->get_comp_assoc())) echo ' <span style="'.$cursor.'text-decoration : none; color : '.$color.';" onclick="'.$href2.'">(groupe - '.ceil($sortmp * 1.5).' MP)</span>';

      if($_GET['tri'] == 'favoris') echo ' <td><a href="sort.php?action=delfavoris&amp;id='.$sort->get_id().'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/interface/croix_quitte.png" alt="Supprimer des favoris" title="Supprimer des favoris" /></a></td>';
      else echo ' <td><a href="sort.php?action=favoris&amp;id='.$sort->get_id().'" onclick="return envoiInfo(this.href, \'information\')"><img src="image/favoris.png" alt="Favoris" title="Ajouter aux sorts favoris" /></a></td>';
      echo '</tr></div>';
      $i++;
    }

  }
  echo '</table>';
}

?>
<img
	src="image/pixel.gif"
	onLoad="envoiInfo('infoperso.php?javascript=oui', 'perso');" />
