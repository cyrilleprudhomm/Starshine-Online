<?php
if (file_exists('root.php'))
  include_once('root.php');

include_once(root.'./inc/fp.php');
$pseudo = $_GET['pseudo'];
//Verification sécuritaire
if(!check_secu($pseudo))
{
	echo 'Les caractères spéciaux ne sont pas autorisés';
	exit;
}
$type = $_GET['type'];
if( $type == 'joueur' )
{
  $mdp = $_GET['mdp'];
  $email = $_GET['email'];
	$login = pseudo_to_login($pseudo);
	
	if( check_existing_account($pseudo, true, true, true) or check_existing_account($login, true, true, true) )
	{
    echo 'Erreur nom déjà utilisé<br /><a href=".">Réessayer</a>';
  }
  else
  {
    $joueur = new joueur(0, $login, '', $pseudo, joueur::droit_jouer, $email);
    $joueur->set_mdp( md5($mdp) );
    $joueur->sauver();
    echo '<h3>Bienvenue dans Starshine-Online</h3>Vous pouvez désormais vous connecté par le biais de votre compte en utilisant votre pseudo ('.$pseudo.') ou votre login ('.$login.') et créer votre personnage.';
  }
}
else if( $type == 'perso' && isset($_SESSION['id_joueur']) )
{
  $race = $_GET['race'];
  $classe = $_GET['classe'];
  if ($classe == 'guerrier') $classe = 'combattant';
  if ($classe == 'mage') $classe = 'magicien';

	//Config punbb groups
	$punbb['elfebois'] = 6;
	$punbb['orc'] = 12;
	$punbb['nain'] = 11;
	$punbb['troll'] = 14;
	$punbb['humain'] = 8;
	$punbb['vampire'] = 15;
	$punbb['elfehaut'] = 7;
	$punbb['humainnoir'] = 9;
	$punbb['scavenger'] = 13;
	$punbb['barbare'] = 5;
	$punbb['mortvivant'] = 10;
	//$requete = "SELECT ID FROM perso WHERE nom = '".$pseudo."'";
	//$req = $db->query($requete);
	//$nombre = $db->num_rows;
	$nombre = check_existing_account($pseudo);
	if ($nombre > 0)
	{
		?>
		Erreur nom déjà utilisé<br />
		<a href=".">Réessayer</a>
		<?php
	}
	else
	{
		include_once(root.'inc/race.inc.php');
		include_once(root.'inc/classe.inc.php');

		echo
		'<div style="padding:5px">
			Vous venez de créer un '.$Gtrad[$race].' '.$classe.' du nom de '.$pseudo.'<br />';
?>
			N'hésitez pas à aller voir régulièrement les informations fournies dans votre forum de race, et de lire le message de votre roi.<br />
			Bon jeu !<br />
		</div>
		<br />
<?php
		$joueur = new perso();
		$caracteristiques = $Trace[$race];
		if ($classe == 'combattant')
		{
			$caracteristiques['vie'] = $caracteristiques['vie'] + 1;
			$caracteristiques['force'] = $caracteristiques['force'] + 1;
			$caracteristiques['dexterite'] = $caracteristiques['dexterite'] + 1;
			$sort_jeu = '';
			$sort_combat = '';
			$comp_combat = '7;8';
		}
		else
		{
			$caracteristiques['energie'] = $caracteristiques['energie'] + 1;
			$caracteristiques['volonte'] = $caracteristiques['volonte'] + 1;
			$caracteristiques['puissance'] = $caracteristiques['puissance'] + 1;
			$sort_jeu = '1';
			$sort_combat = '1';
			$comp_combat = '';
		}
		$royaume = new royaume($caracteristiques['numrace']);

		$joueur->set_nom(trim($pseudo));
		$joueur->set_race($race);
		$joueur->set_level(1);
		$joueur->set_star($royaume->get_star_nouveau_joueur());
		$joueur->set_vie($caracteristiques['vie']);
		$joueur->set_force($caracteristiques['force']);
		$joueur->set_dexterite($caracteristiques['dexterite']);
		$joueur->set_puissance($caracteristiques['puissance']);
		$joueur->set_volonte($caracteristiques['volonte']);
		$joueur->set_energie($caracteristiques['energie']);
		$joueur->set_sort_jeu($sort_jeu);
		$joueur->set_sort_combat($sort_combat);
		$joueur->set_comp_combat($comp_combat);
		$joueur->set_rang_royaume(7);
		// Pas oublier les bases
		$joueur->set_melee(1);
		$joueur->set_distance(1);
		$joueur->set_esquive(1);
		$joueur->set_blocage(1);
		$joueur->set_incantation(1);
		$joueur->set_identification(1);
		$joueur->set_craft(1);
		$joueur->set_alchimie(1);
		$joueur->set_architecture(1);
		$joueur->set_forge(1);
		$joueur->set_survie(1);
		$joueur->set_dressage(1);
		$joueur->set_max_pet(1);

		if($classe == 'combattant')
		{
			$joueur->set_sort_vie(0);
			$joueur->set_sort_element(0);
			$joueur->set_sort_mort(0);
			$joueur->set_facteur_magie(2);
		}
		else
		{
			$joueur->set_sort_vie(1);
			$joueur->set_sort_element(1);
			$joueur->set_sort_mort(1);
			$joueur->set_facteur_magie(1);
		}
		$requete = "SELECT id FROM classe WHERE nom = '".ucwords($classe)."'";
		$req = $db->query($requete);
		$row = $db->read_assoc($req);
		$joueur->set_classe($classe);
		$joueur->set_classe_id($row['id']);
		echo $joueur->get_classe_id(), '...';
		$joueur->set_hp(floor(sqrt($joueur->get_vie()) * 75));
		$joueur->set_hp_max($joueur->get_hp());
		$joueur->set_mp($joueur->get_energie() * $G_facteur_mana);
		$joueur->set_mp_max($joueur->get_mp());
		$joueur->set_regen_hp(time());
		$joueur->set_maj_mp(time());
		$joueur->set_maj_hp(time());

		$joueur->set_x($Trace[$race]['spawn_x']);
		$joueur->set_y($Trace[$race]['spawn_y']);
		$joueur->set_inventaire('O:10:"inventaire":12:{s:4:"cape";N;s:5:"mains";N;s:11:"main_droite";N;s:11:"main_gauche";N;s:5:"torse";N;s:4:"tete";N;s:8:"ceinture";N;s:6:"jambes";N;s:5:"pieds";N;s:3:"dos";N;s:5:"doigt";N;s:3:"cou";N;}');
		$joueur->set_quete('');
		$joueur->set_pa(180);

		$joueur->set_dernieraction(time());
		//($id = 0, $mort = 0, $nom = '', $password = '', $exp = '', $honneur = '', $level = '', $rang_royaume = '', $vie = '', $forcex = '', $dexterite = '', $puissance = '', $volonte = '', $energie = '', $race = '', $classe = '', $classe_id = '', $inventaire = '', $inventaire_slot = '', $pa = '', $dernieraction = '', $action_a = '', $action_d = '', $sort_jeu = '', $sort_combat = '', $comp_combat = '', $comp_jeu = '', $star = '', $x = '', $y = '', $groupe = '', $hp = '', $hp_max = '', $mp = '', $mp_max = '', $melee = '', $distance = '', $esquive = '', $blocage = '', $incantation = '', $sort_vie = '', $sort_element = '', $sort_mort = '', $identification = '', $craft = '', $alchimie = '', $architecture = '', $forge = '', $survie = '', $facteur_magie = '', $facteur_sort_vie = '', $facteur_sort_mort = '', $facteur_sort_element = '', $regen_hp = '', $maj_hp = '', $maj_mp = '', $point_sso = '', $quete = '', $quete_fini = '', $dernier_connexion = '', $statut = '', $fin_ban = '', $frag = '', $crime = '', $amende = '', $teleport_roi = '', $cache_classe = '', $cache_stat = '', $cache_niveau = '', $beta = '')
		/*$requete = "INSERT INTO perso(`ID`,`nom`,`password`,`exp`,`level`,`star`,`vie`,`forcex`,`dexterite`,`puissance`,`volonte`,`energie`,`race`,`classe`, `classe_id`, `inventaire`,`pa`,`dernieraction`, `x`,`y`,`hp`,`hp_max`,`mp`,`mp_max`,`regen_hp`,`maj_mp`,`maj_hp`,`sort_jeu`,`sort_combat`, `comp_combat`, `quete`, `sort_vie`, `sort_element`, `sort_mort`, `facteur_magie`)
		VALUES ('','$nom','$password','$exp','$level','$star','$vie','$force','$dexterite','$puissance','$volonte','$energie','$race','$classe', $classe_id, '$inventaire','180','$time',$x,$y,'$hp','$hp_max','$mp','$mp_max','$regen_hp','$maj_mp','$maj_hp', '$sort_jeu', '$sort_combat', '$comp_combat', '$quete', '$sort_vie', '$sort_element', '$sort_mort', '$facteur_magie')";*/
    $joueur->set_id_joueur( $_SESSION['id_joueur'] );

		$joueur->sauver();
		$jid = replace_all($joueur->get_nom()).'@jabber.starshine-online.com';
		if($joueur->get_id() == -1)
		{
			echo $requete.'<br />';
		}
		else
		{
		    //Envoi du message au joueur
		    $id_groupe = 0;
		    $id_dest = 0;
		    $id_thread = 0;
		    $id_dest = $db->last_insert_id();
		    $messagerie = new messagerie(0);
		    $titre = 'Bienvenue sur Starshine-Online';
		    $message = 'Vous faites maintenant parti de la grande aventure Starshine-Online.
Pour vous aidez plusieurs outils sont a votre disposition :
L\'aide : [url]http://wiki.starshine-online.com[/url]
Le forum : [url]http://forum.starshine-online.com[/url]
Votre forum de race : [url]http://forum.starshine-online.com/viewforum.php?id='.$Trace[$race]['forum_id'].'[/url]
Les logins et mot de passe pour se connecter a ces forums sont les mêmes que ceux du jeu.

En espérant que votre périple se passera bien.
Bon jeu !';
		    $messagerie->envoi_message($id_thread, $id_dest, $titre, $message, $id_groupe);
		}
		require_once('connect_forum.php');
		//Création de l'utilisateur dans le forum
		$requete = "INSERT INTO punbbusers(`group_id`, `username`, `password`, `language`, `style`, `registered`, `jabber`, `email`) VALUES('".$punbb[$race]."', '".$joueur->get_nom()."', '".sha1($mdp)."', 'French', 'SSO', '".time()."', '$jid', '".$joueur->get_email()."')";
		$db_forum->query($requete);
	}
}

function replace_accents($string)
{
  return str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $string);
}

function replace_all($string)
{
  $string = str_replace(' ', '_', $string);
  return replace_accents($string);
  //return strtolower(replace_accents($string));
}
