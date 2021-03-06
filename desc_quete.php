<?php
if (file_exists('root.php'))
  include_once('root.php');

//Affiche et gère les quêtes du personnage

//Inclusion des fichiers indispensables
//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
include_once(root.'haut_ajax.php');
include_once(root.'inc/diplo.inc.php');
$joueur = new perso($_SESSION['ID']);
$requete = 'SELECT * FROM quete WHERE id = '.sSQL($_GET['id_quete']);
$req = $db->query($requete);
$row = $db->read_assoc($req);
if($row['repete'] == 'y') $repetable = ' - Répétable'; else $repetable = '';
if($row['mode'] == 's') $mode = 'Solo'; else $mode = 'Groupe';

$echo_quete = '';
$objectif = unserialize($row['objectif']);
$i = 0;
$quetes = unserialize($joueur->get_quete());
$show_only = array();
if(is_array($quetes))
{
  foreach($quetes[$_GET['quete_joueur']]['objectif'] as $objectif_fait)
  {
    $total_fait = $objectif_fait->nombre;
    $total = $objectif[$i]->nombre;
    if($total_fait >= $total) $objectif[$i]->termine = true;
    else $objectif[$i]->termine = false;
    if($objectif_fait->requis == '' OR $objectif[$objectif_fait->requis]->termine)
    {
      $cible = $objectif[$i]->cible;
      $type = $cible[0];
      $cible = mb_substr($cible, 1);
      switch($type)
      {
        case 'M' :
          $afaire = 'Tuer ';
          $table = 'monstre';
          $monstre = new monstre($cible);
          $cible_nom = $monstre->get_nom();
          $show_only[] = $monstre->get_id();
          $total_o = $total;
					break;
        case 'J' :
          $afaire = 'Tuer ';
          $table = 'diplomatie';
          $cible_nom = 'joueurs en '.$DIPLO[$cible];
          $total_o = $total;
					break;
        case 'P' :
          $afaire = 'Parler à ';
          $table = 'pnj';
          if($cible != 0)
          {
            $requete = "SELECT * FROM pnj WHERE id = ".$cible;
            $req_m = $db->query($requete);
            $row_m = $db->read_assoc($req_m);
            $cible_nom = $row_m['nom'];
          }
          else
          {
            $cible_nom = 'n\'importe quel PNJ';
          }
          $total_o = '';
					break;
        case 'L' :
          $afaire = 'Trouver ';
          $table = 'objet';
          if($cible != 0)
          {
            $requete = "SELECT * FROM ".$table." WHERE id = ".$cible;
            $req_m = $db->query($requete);
            $row_m = $db->read_assoc($req_m);
            $cible_nom = $row_m['nom'];
          }
          else
          {
            $cible_nom = 'n\'importe quel PNJ';
          }
          $total_o = '';
					break;
					case 'O' :
						$afaire = 'Rapporter ';
						$objet = decompose_objet($cible);
						$table = $objet['table_categorie'];
						$requete = "SELECT * FROM ".$table." WHERE id = ".$objet['id_objet'];
						$req_m = $db->query($requete);
						$row_m = $db->read_assoc($req_m);
						$cible_nom = $row_m['nom'];
						if($objet['slot'] != '') $cible_nom .= ' slot niveau '.$objet['slot'];
						$total_o = '';
            break;
        case 'X':
          $afaire = $objectif[$i]->info;
          $cible_nom = '';
          break;
      }
      $echo_quete .= $afaire.' '.$total_o.' '.$cible_nom.' => '.$total_fait.' / '.$total.'<br />';
    }
    $i++;
  }
}

$show_only = implode(',', $show_only);
echo '
<h2 style="margin : 0px; padding : 0px;margin-bottom : 3px;"">'.$row['nom'].' <a href="" onclick="javascript:show_only=\''.$show_only.'\'; deplacement(\'centre\', \'normal\', show_only); return false;"><img src="image/icone/eye.png" style="vertical-align : -18%;" title="Afficher uniquement les monstres de ce type sur la carte" alt="O" /></a></h2>
<div class="description_quete">
	<span style="font-style : italic;">Niveau conseillé '.$row['lvl_joueur'].'<br />
	'.$mode.' '.$repetable.'</span><br />
	<br /><span class="small">'.nl2br($row['description']).'</span>
	<h3 style="margin : 0px; padding : 0px; margin-top : 5px;">Objectifs</h3>';
	echo $echo_quete;
	echo '
	<h3 style="margin : 0px; padding : 0px; margin-top : 5px;">Récompense</h3>
	<ul>
		<li>Stars : '.$row['star'].'</li>
		<li>Expérience : '.$row['exp'].'</li>
		<li>Honneur : '.$row['honneur'].'</li>
		<li><strong>Objets</strong> :</li>';
		$rewards = explode(';', $row['reward']);
		$r = 0;
		while($r < count($rewards))
		{
			$reward_exp = explode('-', $rewards[$r]);
			$reward_id = $reward_exp[0];
			$reward_id_objet = mb_substr($reward_id, 1);
			$reward_nb = $reward_exp[1];
			switch($reward_id[0])
			{
				case 'r' :
					$requete = "SELECT * FROM recette WHERE id = ".$reward_id_objet;
					$req_r = $db->query($requete);
					$row_r = $db->read_assoc($req_r);
					echo '<li>Recette de '.$row_r['nom'].' X '.$reward_nb.'</li>';
				break;
				case 'x' :
					echo '<li>Un objet au hasard</li>';
				break;
        case 'p':
					$requete = "SELECT nom FROM armure WHERE id = ".$reward_id_objet;
					$req_r = $db->query($requete);
					$row_r = $db->read_assoc($req_r);
					echo '<li>'.$row_r['nom'].' X '.$reward_nb.'</li>';
			}
			$r++;
		}
		?>
	</ul>
	<a href="quete.php?id_quete=<?php echo $_GET['id_quete']; ?>&amp;quete_joueur=<?php echo $_GET['quete_joueur']; ?>&amp;action=delete" onclick="if(confirm('Voulez-vous vraiment abandonner cette quête ?')) return envoiInfo(this.href, 'information'); else return false;">Abandonner cette quête</a>
</div>
