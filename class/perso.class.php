<?php
class perso
{
/**
    * @access private
    * @var int(11)
    */
	private $id;

	/**
    * @access private
    * @var int(10)
    */
	private $mort;

	/**
    * @access private
    * @var varchar(50)
    */
	private $nom;

	/**
    * @access private
    * @var varchar(40)
    */
	private $password;

	/**
    * @access private
    * @var int(11)
    */
	private $exp;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $honneur;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $level;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $rang_royaume;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $vie;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $forcex;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $dexterite;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $puissance;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $volonte;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $energie;

	/**
    * @access private
    * @var varchar(20)
    */
	private $race;

	/**
    * @access private
    * @var varchar(20)
    */
	private $classe;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $classe_id;

	/**
    * @access private
    * @var text
    */
	private $inventaire;

	/**
    * @access private
    * @var text
    */
	private $inventaire_slot;

	/**
    * @access private
    * @var smallint(6)
    */
	private $pa;

	/**
    * @access private
    * @var int(11)
    */
	private $dernieraction;

	/**
    * @access private
    * @var int(10)
    */
	private $action_a;

	/**
    * @access private
    * @var int(10)
    */
	private $action_d;

	/**
    * @access private
    * @var text
    */
	private $sort_jeu;

	/**
    * @access private
    * @var text
    */
	private $sort_combat;

	/**
    * @access private
    * @var text
    */
	private $comp_combat;

	/**
    * @access private
    * @var text
    */
	private $comp_jeu;

	/**
    * @access private
    * @var int(11)
    */
	private $star;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $x;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $y;

	/**
    * @access private
    * @var int(11)
    */
	private $groupe;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $hp;

	/**
    * @access private
    * @var float
    */
	private $hp_max;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $mp;

	/**
    * @access private
    * @var float
    */
	private $mp_max;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $melee;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $distance;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $esquive;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $blocage;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $incantation;

	/**
    * @access private
    * @var mediumint(9)
    */
	private $sort_vie;

	/**
    * @access private
    * @var int(11)
    */
	private $sort_element;

	/**
    * @access private
    * @var int(11)
    */
	private $sort_mort;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $identification;

	/**
    * @access private
    * @var int(10)
    */
	private $craft;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $alchimie;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $architecture;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $forge;

	/**
    * @access private
    * @var mediumint(8)
    */
	private $survie;

	/**
    * @access private
    * @var double
    */
	private $facteur_magie;

	/**
    * @access private
    * @var double
    */
	private $facteur_sort_vie;

	/**
    * @access private
    * @var double
    */
	private $facteur_sort_mort;

	/**
    * @access private
    * @var double
    */
	private $facteur_sort_element;

	/**
    * @access private
    * @var int(10)
    */
	private $regen_hp;

	/**
    * @access private
    * @var int(10)
    */
	private $maj_hp;

	/**
    * @access private
    * @var int(10)
    */
	private $maj_mp;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $point_sso;

	/**
    * @access private
    * @var text
    */
	private $quete;

	/**
    * @access private
    * @var text
    */
	private $quete_fini;

	/**
    * @access private
    * @var int(10)
    */
	private $dernier_connexion;

	/**
    * @access private
    * @var varchar(50)
    */
	private $statut;

	/**
    * @access private
    * @var int(11)
    */
	private $fin_ban;

	/**
    * @access private
    * @var int(10)
    */
	private $frag;

	/**
    * @access private
    * @var float
    */
	private $crime;

	/**
    * @access private
    * @var int(10)
    */
	private $amende;

	/**
    * @access private
    * @var enum('true','false')
    */
	private $teleport_roi;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $cache_classe;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $cache_stat;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $cache_niveau;

	/**
    * @access private
    * @var tinyint(3)
    */
	private $beta;

	
	/**
	* @access public

	* @param int(11) id attribut
	* @param int(10) mort attribut
	* @param varchar(50) nom attribut
	* @param varchar(40) password attribut
	* @param int(11) exp attribut
	* @param mediumint(8) honneur attribut
	* @param mediumint(9) level attribut
	* @param mediumint(9) rang_royaume attribut
	* @param mediumint(9) vie attribut
	* @param mediumint(9) forcex attribut
	* @param mediumint(9) dexterite attribut
	* @param mediumint(9) puissance attribut
	* @param mediumint(9) volonte attribut
	* @param mediumint(9) energie attribut
	* @param varchar(20) race attribut
	* @param varchar(20) classe attribut
	* @param tinyint(3) classe_id attribut
	* @param text inventaire attribut
	* @param text inventaire_slot attribut
	* @param smallint(6) pa attribut
	* @param int(11) dernieraction attribut
	* @param int(10) action_a attribut
	* @param int(10) action_d attribut
	* @param text sort_jeu attribut
	* @param text sort_combat attribut
	* @param text comp_combat attribut
	* @param text comp_jeu attribut
	* @param int(11) star attribut
	* @param mediumint(9) x attribut
	* @param mediumint(9) y attribut
	* @param int(11) groupe attribut
	* @param mediumint(9) hp attribut
	* @param float hp_max attribut
	* @param mediumint(8) mp attribut
	* @param float mp_max attribut
	* @param mediumint(9) melee attribut
	* @param mediumint(9) distance attribut
	* @param mediumint(9) esquive attribut
	* @param mediumint(8) blocage attribut
	* @param mediumint(9) incantation attribut
	* @param mediumint(9) sort_vie attribut
	* @param int(11) sort_element attribut
	* @param int(11) sort_mort attribut
	* @param mediumint(8) identification attribut
	* @param int(10) craft attribut
	* @param mediumint(8) alchimie attribut
	* @param mediumint(8) architecture attribut
	* @param mediumint(8) forge attribut
	* @param mediumint(8) survie attribut
	* @param double facteur_magie attribut
	* @param double facteur_sort_vie attribut
	* @param double facteur_sort_mort attribut
	* @param double facteur_sort_element attribut
	* @param int(10) regen_hp attribut
	* @param int(10) maj_hp attribut
	* @param int(10) maj_mp attribut
	* @param tinyint(3) point_sso attribut
	* @param text quete attribut
	* @param text quete_fini attribut
	* @param int(10) dernier_connexion attribut
	* @param varchar(50) statut attribut
	* @param int(11) fin_ban attribut
	* @param int(10) frag attribut
	* @param float crime attribut
	* @param int(10) amende attribut
	* @param enum('true','false') teleport_roi attribut
	* @param tinyint(3) cache_classe attribut
	* @param tinyint(3) cache_stat attribut
	* @param tinyint(3) cache_niveau attribut
	* @param tinyint(3) beta attribut
	* @return none
	*/
	function __construct($id = 0, $mort = 0, $nom = '', $password = '', $exp = '', $honneur = '', $level = '', $rang_royaume = '', $vie = '', $forcex = '', $dexterite = '', $puissance = '', $volonte = '', $energie = '', $race = '', $classe = '', $classe_id = '', $inventaire = '', $inventaire_slot = '', $pa = '', $dernieraction = '', $action_a = '', $action_d = '', $sort_jeu = '', $sort_combat = '', $comp_combat = '', $comp_jeu = '', $star = '', $x = '', $y = '', $groupe = '', $hp = '', $hp_max = '', $mp = '', $mp_max = '', $melee = '', $distance = '', $esquive = '', $blocage = '', $incantation = '', $sort_vie = '', $sort_element = '', $sort_mort = '', $identification = '', $craft = '', $alchimie = '', $architecture = '', $forge = '', $survie = '', $facteur_magie = '', $facteur_sort_vie = '', $facteur_sort_mort = '', $facteur_sort_element = '', $regen_hp = '', $maj_hp = '', $maj_mp = '', $point_sso = '', $quete = '', $quete_fini = '', $dernier_connexion = '', $statut = '', $fin_ban = '', $frag = '', $crime = '', $amende = '', $teleport_roi = '', $cache_classe = '', $cache_stat = '', $cache_niveau = '', $beta = '')
	{
		global $db;
		//Verification nombre et du type d'argument pour construire l'etat adequat.
		if( (func_num_args() == 1) && is_numeric($id) )
		{
			$requeteSQL = $db->query("SELECT mort, nom, password, exp, honneur, level, rang_royaume, vie, forcex, dexterite, puissance, volonte, energie, race, classe, classe_id, inventaire, inventaire_slot, pa, dernieraction, action_a, action_d, sort_jeu, sort_combat, comp_combat, comp_jeu, star, x, y, groupe, hp, hp_max, mp, mp_max, melee, distance, esquive, blocage, incantation, sort_vie, sort_element, sort_mort, identification, craft, alchimie, architecture, forge, survie, facteur_magie, facteur_sort_vie, facteur_sort_mort, facteur_sort_element, regen_hp, maj_hp, maj_mp, point_sso, quete, quete_fini, dernier_connexion, statut, fin_ban, frag, crime, amende, teleport_roi, cache_classe, cache_stat, cache_niveau, beta FROM perso WHERE id = ".$id);
			//Si le thread est dans la base, on le charge sinon on crée un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->mort, $this->nom, $this->password, $this->exp, $this->honneur, $this->level, $this->rang_royaume, $this->vie, $this->forcex, $this->dexterite, $this->puissance, $this->volonte, $this->energie, $this->race, $this->classe, $this->classe_id, $this->inventaire, $this->inventaire_slot, $this->pa, $this->dernieraction, $this->action_a, $this->action_d, $this->sort_jeu, $this->sort_combat, $this->comp_combat, $this->comp_jeu, $this->star, $this->x, $this->y, $this->groupe, $this->hp, $this->hp_max, $this->mp, $this->mp_max, $this->melee, $this->distance, $this->esquive, $this->blocage, $this->incantation, $this->sort_vie, $this->sort_element, $this->sort_mort, $this->identification, $this->craft, $this->alchimie, $this->architecture, $this->forge, $this->survie, $this->facteur_magie, $this->facteur_sort_vie, $this->facteur_sort_mort, $this->facteur_sort_element, $this->regen_hp, $this->maj_hp, $this->maj_mp, $this->point_sso, $this->quete, $this->quete_fini, $this->dernier_connexion, $this->statut, $this->fin_ban, $this->frag, $this->crime, $this->amende, $this->teleport_roi, $this->cache_classe, $this->cache_stat, $this->cache_niveau, $this->beta) = $db->read_array($requeteSQL);
			}
			else $this->__construct();
			$this->id = $id;
		}
		elseif( (func_num_args() == 1) && is_array($id) )
		{
			$this->id = $id['id'];
			$this->mort = $id['mort'];
			$this->nom = $id['nom'];
			$this->password = $id['password'];
			$this->exp = $id['exp'];
			$this->honneur = $id['honneur'];
			$this->level = $id['level'];
			$this->rang_royaume = $id['rang_royaume'];
			$this->vie = $id['vie'];
			$this->forcex = $id['forcex'];
			$this->dexterite = $id['dexterite'];
			$this->puissance = $id['puissance'];
			$this->volonte = $id['volonte'];
			$this->energie = $id['energie'];
			$this->race = $id['race'];
			$this->classe = $id['classe'];
			$this->classe_id = $id['classe_id'];
			$this->inventaire = $id['inventaire'];
			$this->inventaire_slot = $id['inventaire_slot'];
			$this->pa = $id['pa'];
			$this->dernieraction = $id['dernieraction'];
			$this->action_a = $id['action_a'];
			$this->action_d = $id['action_d'];
			$this->sort_jeu = $id['sort_jeu'];
			$this->sort_combat = $id['sort_combat'];
			$this->comp_combat = $id['comp_combat'];
			$this->comp_jeu = $id['comp_jeu'];
			$this->star = $id['star'];
			$this->x = $id['x'];
			$this->y = $id['y'];
			$this->groupe = $id['groupe'];
			$this->hp = $id['hp'];
			$this->hp_max = $id['hp_max'];
			$this->mp = $id['mp'];
			$this->mp_max = $id['mp_max'];
			$this->melee = $id['melee'];
			$this->distance = $id['distance'];
			$this->esquive = $id['esquive'];
			$this->blocage = $id['blocage'];
			$this->incantation = $id['incantation'];
			$this->sort_vie = $id['sort_vie'];
			$this->sort_element = $id['sort_element'];
			$this->sort_mort = $id['sort_mort'];
			$this->identification = $id['identification'];
			$this->craft = $id['craft'];
			$this->alchimie = $id['alchimie'];
			$this->architecture = $id['architecture'];
			$this->forge = $id['forge'];
			$this->survie = $id['survie'];
			$this->facteur_magie = $id['facteur_magie'];
			$this->facteur_sort_vie = $id['facteur_sort_vie'];
			$this->facteur_sort_mort = $id['facteur_sort_mort'];
			$this->facteur_sort_element = $id['facteur_sort_element'];
			$this->regen_hp = $id['regen_hp'];
			$this->maj_hp = $id['maj_hp'];
			$this->maj_mp = $id['maj_mp'];
			$this->point_sso = $id['point_sso'];
			$this->quete = $id['quete'];
			$this->quete_fini = $id['quete_fini'];
			$this->dernier_connexion = $id['dernier_connexion'];
			$this->statut = $id['statut'];
			$this->fin_ban = $id['fin_ban'];
			$this->frag = $id['frag'];
			$this->crime = $id['crime'];
			$this->amende = $id['amende'];
			$this->teleport_roi = $id['teleport_roi'];
			$this->cache_classe = $id['cache_classe'];
			$this->cache_stat = $id['cache_stat'];
			$this->cache_niveau = $id['cache_niveau'];
			$this->beta = $id['beta'];
			}
		else
		{
			$this->mort = $mort;
			$this->nom = $nom;
			$this->password = $password;
			$this->exp = $exp;
			$this->honneur = $honneur;
			$this->level = $level;
			$this->rang_royaume = $rang_royaume;
			$this->vie = $vie;
			$this->forcex = $forcex;
			$this->dexterite = $dexterite;
			$this->puissance = $puissance;
			$this->volonte = $volonte;
			$this->energie = $energie;
			$this->race = $race;
			$this->classe = $classe;
			$this->classe_id = $classe_id;
			$this->inventaire = $inventaire;
			$this->inventaire_slot = $inventaire_slot;
			$this->pa = $pa;
			$this->dernieraction = $dernieraction;
			$this->action_a = $action_a;
			$this->action_d = $action_d;
			$this->sort_jeu = $sort_jeu;
			$this->sort_combat = $sort_combat;
			$this->comp_combat = $comp_combat;
			$this->comp_jeu = $comp_jeu;
			$this->star = $star;
			$this->x = $x;
			$this->y = $y;
			$this->groupe = $groupe;
			$this->hp = $hp;
			$this->hp_max = $hp_max;
			$this->mp = $mp;
			$this->mp_max = $mp_max;
			$this->melee = $melee;
			$this->distance = $distance;
			$this->esquive = $esquive;
			$this->blocage = $blocage;
			$this->incantation = $incantation;
			$this->sort_vie = $sort_vie;
			$this->sort_element = $sort_element;
			$this->sort_mort = $sort_mort;
			$this->identification = $identification;
			$this->craft = $craft;
			$this->alchimie = $alchimie;
			$this->architecture = $architecture;
			$this->forge = $forge;
			$this->survie = $survie;
			$this->facteur_magie = $facteur_magie;
			$this->facteur_sort_vie = $facteur_sort_vie;
			$this->facteur_sort_mort = $facteur_sort_mort;
			$this->facteur_sort_element = $facteur_sort_element;
			$this->regen_hp = $regen_hp;
			$this->maj_hp = $maj_hp;
			$this->maj_mp = $maj_mp;
			$this->point_sso = $point_sso;
			$this->quete = $quete;
			$this->quete_fini = $quete_fini;
			$this->dernier_connexion = $dernier_connexion;
			$this->statut = $statut;
			$this->fin_ban = $fin_ban;
			$this->frag = $frag;
			$this->crime = $crime;
			$this->amende = $amende;
			$this->teleport_roi = $teleport_roi;
			$this->cache_classe = $cache_classe;
			$this->cache_stat = $cache_stat;
			$this->cache_niveau = $cache_niveau;
			$this->beta = $beta;
			$this->id = $id;
		}
	}

	/**
	* Sauvegarde automatiquement en base de donnée. Si c'est un nouvel objet, INSERT, sinon UPDATE
	* @access public
	* @param bool $force force la mis à jour de tous les attributs de l'objet si true, sinon uniquement ceux qui ont été modifiés
	* @return none
	*/
	function sauver($force = false)
	{
		global $db;
		if( $this->id > 0 )
		{
			if(count($this->champs_modif) > 0)
			{
				if($force) $champs = 'mort = '.$this->mort.', nom = "'.mysql_escape_string($this->nom).'", password = "'.mysql_escape_string($this->password).'", exp = "'.mysql_escape_string($this->exp).'", honneur = "'.mysql_escape_string($this->honneur).'", level = "'.mysql_escape_string($this->level).'", rang_royaume = "'.mysql_escape_string($this->rang_royaume).'", vie = "'.mysql_escape_string($this->vie).'", forcex = "'.mysql_escape_string($this->forcex).'", dexterite = "'.mysql_escape_string($this->dexterite).'", puissance = "'.mysql_escape_string($this->puissance).'", volonte = "'.mysql_escape_string($this->volonte).'", energie = "'.mysql_escape_string($this->energie).'", race = "'.mysql_escape_string($this->race).'", classe = "'.mysql_escape_string($this->classe).'", classe_id = "'.mysql_escape_string($this->classe_id).'", inventaire = "'.mysql_escape_string($this->inventaire).'", inventaire_slot = "'.mysql_escape_string($this->inventaire_slot).'", pa = "'.mysql_escape_string($this->pa).'", dernieraction = "'.mysql_escape_string($this->dernieraction).'", action_a = "'.mysql_escape_string($this->action_a).'", action_d = "'.mysql_escape_string($this->action_d).'", sort_jeu = "'.mysql_escape_string($this->sort_jeu).'", sort_combat = "'.mysql_escape_string($this->sort_combat).'", comp_combat = "'.mysql_escape_string($this->comp_combat).'", comp_jeu = "'.mysql_escape_string($this->comp_jeu).'", star = "'.mysql_escape_string($this->star).'", x = "'.mysql_escape_string($this->x).'", y = "'.mysql_escape_string($this->y).'", groupe = "'.mysql_escape_string($this->groupe).'", hp = "'.mysql_escape_string($this->hp).'", hp_max = "'.mysql_escape_string($this->hp_max).'", mp = "'.mysql_escape_string($this->mp).'", mp_max = "'.mysql_escape_string($this->mp_max).'", melee = "'.mysql_escape_string($this->melee).'", distance = "'.mysql_escape_string($this->distance).'", esquive = "'.mysql_escape_string($this->esquive).'", blocage = "'.mysql_escape_string($this->blocage).'", incantation = "'.mysql_escape_string($this->incantation).'", sort_vie = "'.mysql_escape_string($this->sort_vie).'", sort_element = "'.mysql_escape_string($this->sort_element).'", sort_mort = "'.mysql_escape_string($this->sort_mort).'", identification = "'.mysql_escape_string($this->identification).'", craft = "'.mysql_escape_string($this->craft).'", alchimie = "'.mysql_escape_string($this->alchimie).'", architecture = "'.mysql_escape_string($this->architecture).'", forge = "'.mysql_escape_string($this->forge).'", survie = "'.mysql_escape_string($this->survie).'", facteur_magie = "'.mysql_escape_string($this->facteur_magie).'", facteur_sort_vie = "'.mysql_escape_string($this->facteur_sort_vie).'", facteur_sort_mort = "'.mysql_escape_string($this->facteur_sort_mort).'", facteur_sort_element = "'.mysql_escape_string($this->facteur_sort_element).'", regen_hp = "'.mysql_escape_string($this->regen_hp).'", maj_hp = "'.mysql_escape_string($this->maj_hp).'", maj_mp = "'.mysql_escape_string($this->maj_mp).'", point_sso = "'.mysql_escape_string($this->point_sso).'", quete = "'.mysql_escape_string($this->quete).'", quete_fini = "'.mysql_escape_string($this->quete_fini).'", dernier_connexion = "'.mysql_escape_string($this->dernier_connexion).'", statut = "'.mysql_escape_string($this->statut).'", fin_ban = "'.mysql_escape_string($this->fin_ban).'", frag = "'.mysql_escape_string($this->frag).'", crime = "'.mysql_escape_string($this->crime).'", amende = "'.mysql_escape_string($this->amende).'", teleport_roi = "'.mysql_escape_string($this->teleport_roi).'", cache_classe = "'.mysql_escape_string($this->cache_classe).'", cache_stat = "'.mysql_escape_string($this->cache_stat).'", cache_niveau = "'.mysql_escape_string($this->cache_niveau).'", beta = "'.mysql_escape_string($this->beta).'"';
				else
				{
					$champs = '';
					foreach($this->champs_modif as $champ)
					{
						$champs[] .= $champ.' = "'.mysql_escape_string($this->{$champ}).'"';
					}
					$champs = implode(', ', $champs);
				}
				$requete = 'UPDATE perso SET ';
				$requete .= $champs;
				$requete .= ' WHERE id = '.$this->id;
				$db->query($requete);
				$this->champs_modif = array();
			}
		}
		else
		{
			$requete = 'INSERT INTO perso (mort, nom, password, exp, honneur, level, rang_royaume, vie, forcex, dexterite, puissance, volonte, energie, race, classe, classe_id, inventaire, inventaire_slot, pa, dernieraction, action_a, action_d, sort_jeu, sort_combat, comp_combat, comp_jeu, star, x, y, groupe, hp, hp_max, mp, mp_max, melee, distance, esquive, blocage, incantation, sort_vie, sort_element, sort_mort, identification, craft, alchimie, architecture, forge, survie, facteur_magie, facteur_sort_vie, facteur_sort_mort, facteur_sort_element, regen_hp, maj_hp, maj_mp, point_sso, quete, quete_fini, dernier_connexion, statut, fin_ban, frag, crime, amende, teleport_roi, cache_classe, cache_stat, cache_niveau, beta) VALUES(';
			$requete .= ''.$this->mort.', "'.mysql_escape_string($this->nom).'", "'.mysql_escape_string($this->password).'", "'.mysql_escape_string($this->exp).'", "'.mysql_escape_string($this->honneur).'", "'.mysql_escape_string($this->level).'", "'.mysql_escape_string($this->rang_royaume).'", "'.mysql_escape_string($this->vie).'", "'.mysql_escape_string($this->forcex).'", "'.mysql_escape_string($this->dexterite).'", "'.mysql_escape_string($this->puissance).'", "'.mysql_escape_string($this->volonte).'", "'.mysql_escape_string($this->energie).'", "'.mysql_escape_string($this->race).'", "'.mysql_escape_string($this->classe).'", "'.mysql_escape_string($this->classe_id).'", "'.mysql_escape_string($this->inventaire).'", "'.mysql_escape_string($this->inventaire_slot).'", "'.mysql_escape_string($this->pa).'", "'.mysql_escape_string($this->dernieraction).'", "'.mysql_escape_string($this->action_a).'", "'.mysql_escape_string($this->action_d).'", "'.mysql_escape_string($this->sort_jeu).'", "'.mysql_escape_string($this->sort_combat).'", "'.mysql_escape_string($this->comp_combat).'", "'.mysql_escape_string($this->comp_jeu).'", "'.mysql_escape_string($this->star).'", "'.mysql_escape_string($this->x).'", "'.mysql_escape_string($this->y).'", "'.mysql_escape_string($this->groupe).'", "'.mysql_escape_string($this->hp).'", "'.mysql_escape_string($this->hp_max).'", "'.mysql_escape_string($this->mp).'", "'.mysql_escape_string($this->mp_max).'", "'.mysql_escape_string($this->melee).'", "'.mysql_escape_string($this->distance).'", "'.mysql_escape_string($this->esquive).'", "'.mysql_escape_string($this->blocage).'", "'.mysql_escape_string($this->incantation).'", "'.mysql_escape_string($this->sort_vie).'", "'.mysql_escape_string($this->sort_element).'", "'.mysql_escape_string($this->sort_mort).'", "'.mysql_escape_string($this->identification).'", "'.mysql_escape_string($this->craft).'", "'.mysql_escape_string($this->alchimie).'", "'.mysql_escape_string($this->architecture).'", "'.mysql_escape_string($this->forge).'", "'.mysql_escape_string($this->survie).'", "'.mysql_escape_string($this->facteur_magie).'", "'.mysql_escape_string($this->facteur_sort_vie).'", "'.mysql_escape_string($this->facteur_sort_mort).'", "'.mysql_escape_string($this->facteur_sort_element).'", "'.mysql_escape_string($this->regen_hp).'", "'.mysql_escape_string($this->maj_hp).'", "'.mysql_escape_string($this->maj_mp).'", "'.mysql_escape_string($this->point_sso).'", "'.mysql_escape_string($this->quete).'", "'.mysql_escape_string($this->quete_fini).'", "'.mysql_escape_string($this->dernier_connexion).'", "'.mysql_escape_string($this->statut).'", "'.mysql_escape_string($this->fin_ban).'", "'.mysql_escape_string($this->frag).'", "'.mysql_escape_string($this->crime).'", "'.mysql_escape_string($this->amende).'", "'.mysql_escape_string($this->teleport_roi).'", "'.mysql_escape_string($this->cache_classe).'", "'.mysql_escape_string($this->cache_stat).'", "'.mysql_escape_string($this->cache_niveau).'", "'.mysql_escape_string($this->beta).'")';
			$db->query($requete);
			//Récuperation du dernier ID inséré.
			$this->id = $db->last_insert_id();
		}
	}

	/**
	* Supprime de la base de donnée
	* @access public
	* @param none
	* @return none
	*/
	function supprimer()
	{
		global $db;
		if( $this->id > 0 )
		{
			$requete = 'DELETE FROM perso WHERE id = '.$this->id;
			$db->query($requete);
		}
	}

	/**
	* Supprime de la base de donnée
	* @access static
	* @param array|string $champs champs servant a trouver les résultats
	* @param array|string $valeurs valeurs servant a trouver les résultats
	* @param string $ordre ordre de tri
	* @param bool|string $keys Si false, stockage en tableau classique, si string stockage avec sous tableau en fonction du champ $keys
	* @return array $return liste d'objets
	*/
	static function create($champs, $valeurs, $ordre = 'id ASC', $keys = false, $where = false)
	{
		global $db;
		$return = array();
		if(!$where)
		{
			if(!is_array($champs))
			{
				$array_champs[] = $champs;
				$array_valeurs[] = $valeurs;
			}
			else
			{
				$array_champs = $champs;
				$array_valeurs = $valeurs;
			}
			foreach($array_champs as $key => $champ)
			{
				$where[] = $champ .' = "'.mysql_escape_string($array_valeurs[$key]).'"';
			}
			$where = implode(' AND ', $where);
			if($champs === 0)
			{
				$where = ' 1 ';
			}
		}

		$requete = "SELECT id, mort, nom, password, exp, honneur, level, rang_royaume, vie, forcex, dexterite, puissance, volonte, energie, race, classe, classe_id, inventaire, inventaire_slot, pa, dernieraction, action_a, action_d, sort_jeu, sort_combat, comp_combat, comp_jeu, star, x, y, groupe, hp, hp_max, mp, mp_max, melee, distance, esquive, blocage, incantation, sort_vie, sort_element, sort_mort, identification, craft, alchimie, architecture, forge, survie, facteur_magie, facteur_sort_vie, facteur_sort_mort, facteur_sort_element, regen_hp, maj_hp, maj_mp, point_sso, quete, quete_fini, dernier_connexion, statut, fin_ban, frag, crime, amende, teleport_roi, cache_classe, cache_stat, cache_niveau, beta FROM perso WHERE ".$where." ORDER BY ".$ordre;
		$req = $db->query($requete);
		if($db->num_rows() > 0)
		{
			while($row = $db->read_assoc($req))
			{
				if(!$keys) $return[] = new perso($row);
				else $return[$row[$keys]][] = new perso($row);
			}
		}
		else $return = false;
		return $return;
	}

	/**
	* Affiche l'objet sous forme de string
	* @access public
	* @param none
	* @return string objet en string
	*/
	function __toString()
	{
		return 'id = '.$this->id.', mort = '.$this->mort.', nom = '.$this->nom.', password = '.$this->password.', exp = '.$this->exp.', honneur = '.$this->honneur.', level = '.$this->level.', rang_royaume = '.$this->rang_royaume.', vie = '.$this->vie.', forcex = '.$this->forcex.', dexterite = '.$this->dexterite.', puissance = '.$this->puissance.', volonte = '.$this->volonte.', energie = '.$this->energie.', race = '.$this->race.', classe = '.$this->classe.', classe_id = '.$this->classe_id.', inventaire = '.$this->inventaire.', inventaire_slot = '.$this->inventaire_slot.', pa = '.$this->pa.', dernieraction = '.$this->dernieraction.', action_a = '.$this->action_a.', action_d = '.$this->action_d.', sort_jeu = '.$this->sort_jeu.', sort_combat = '.$this->sort_combat.', comp_combat = '.$this->comp_combat.', comp_jeu = '.$this->comp_jeu.', star = '.$this->star.', x = '.$this->x.', y = '.$this->y.', groupe = '.$this->groupe.', hp = '.$this->hp.', hp_max = '.$this->hp_max.', mp = '.$this->mp.', mp_max = '.$this->mp_max.', melee = '.$this->melee.', distance = '.$this->distance.', esquive = '.$this->esquive.', blocage = '.$this->blocage.', incantation = '.$this->incantation.', sort_vie = '.$this->sort_vie.', sort_element = '.$this->sort_element.', sort_mort = '.$this->sort_mort.', identification = '.$this->identification.', craft = '.$this->craft.', alchimie = '.$this->alchimie.', architecture = '.$this->architecture.', forge = '.$this->forge.', survie = '.$this->survie.', facteur_magie = '.$this->facteur_magie.', facteur_sort_vie = '.$this->facteur_sort_vie.', facteur_sort_mort = '.$this->facteur_sort_mort.', facteur_sort_element = '.$this->facteur_sort_element.', regen_hp = '.$this->regen_hp.', maj_hp = '.$this->maj_hp.', maj_mp = '.$this->maj_mp.', point_sso = '.$this->point_sso.', quete = '.$this->quete.', quete_fini = '.$this->quete_fini.', dernier_connexion = '.$this->dernier_connexion.', statut = '.$this->statut.', fin_ban = '.$this->fin_ban.', frag = '.$this->frag.', crime = '.$this->crime.', amende = '.$this->amende.', teleport_roi = '.$this->teleport_roi.', cache_classe = '.$this->cache_classe.', cache_stat = '.$this->cache_stat.', cache_niveau = '.$this->cache_niveau.', beta = '.$this->beta;
	}
	
	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $id valeur de l'attribut id
	*/
	function get_id()
	{
		return $this->id;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $mort valeur de l'attribut mort
	*/
	function get_mort()
	{
		return $this->mort;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $nom valeur de l'attribut nom
	*/
	function get_nom()
	{
		return $this->nom;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(40) $password valeur de l'attribut password
	*/
	function get_password()
	{
		return $this->password;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $exp valeur de l'attribut exp
	*/
	function get_exp()
	{
		return $this->exp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $honneur valeur de l'attribut honneur
	*/
	function get_honneur()
	{
		return $this->honneur;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $level valeur de l'attribut level
	*/
	function get_level()
	{
		return $this->level;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $rang_royaume valeur de l'attribut rang_royaume
	*/
	function get_rang_royaume()
	{
		return $this->rang_royaume;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $vie valeur de l'attribut vie
	*/
	function get_vie()
	{
		return $this->vie;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $forcex valeur de l'attribut forcex
	*/
	function get_forcex()
	{
		return $this->forcex;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $dexterite valeur de l'attribut dexterite
	*/
	function get_dexterite()
	{
		return $this->dexterite;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $puissance valeur de l'attribut puissance
	*/
	function get_puissance()
	{
		return $this->puissance;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $volonte valeur de l'attribut volonte
	*/
	function get_volonte()
	{
		return $this->volonte;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $energie valeur de l'attribut energie
	*/
	function get_energie()
	{
		return $this->energie;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(20) $race valeur de l'attribut race
	*/
	function get_race()
	{
		return $this->race;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(20) $classe valeur de l'attribut classe
	*/
	function get_classe()
	{
		return $this->classe;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $classe_id valeur de l'attribut classe_id
	*/
	function get_classe_id()
	{
		return $this->classe_id;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $inventaire valeur de l'attribut inventaire
	*/
	function get_inventaire()
	{
		return $this->inventaire;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $inventaire_slot valeur de l'attribut inventaire_slot
	*/
	function get_inventaire_slot()
	{
		return $this->inventaire_slot;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return smallint(6) $pa valeur de l'attribut pa
	*/
	function get_pa()
	{
		return $this->pa;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $dernieraction valeur de l'attribut dernieraction
	*/
	function get_dernieraction()
	{
		return $this->dernieraction;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $action_a valeur de l'attribut action_a
	*/
	function get_action_a()
	{
		return $this->action_a;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $action_d valeur de l'attribut action_d
	*/
	function get_action_d()
	{
		return $this->action_d;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $sort_jeu valeur de l'attribut sort_jeu
	*/
	function get_sort_jeu()
	{
		return $this->sort_jeu;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $sort_combat valeur de l'attribut sort_combat
	*/
	function get_sort_combat()
	{
		return $this->sort_combat;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $comp_combat valeur de l'attribut comp_combat
	*/
	function get_comp_combat()
	{
		return $this->comp_combat;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $comp_jeu valeur de l'attribut comp_jeu
	*/
	function get_comp_jeu()
	{
		return $this->comp_jeu;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $star valeur de l'attribut star
	*/
	function get_star()
	{
		return $this->star;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $x valeur de l'attribut x
	*/
	function get_x()
	{
		return $this->x;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $y valeur de l'attribut y
	*/
	function get_y()
	{
		return $this->y;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $groupe valeur de l'attribut groupe
	*/
	function get_groupe()
	{
		return $this->groupe;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $hp valeur de l'attribut hp
	*/
	function get_hp()
	{
		return $this->hp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return float $hp_max valeur de l'attribut hp_max
	*/
	function get_hp_max()
	{
		return $this->hp_max;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $mp valeur de l'attribut mp
	*/
	function get_mp()
	{
		return $this->mp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return float $mp_max valeur de l'attribut mp_max
	*/
	function get_mp_max()
	{
		return $this->mp_max;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $melee valeur de l'attribut melee
	*/
	function get_melee()
	{
		return $this->melee;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $distance valeur de l'attribut distance
	*/
	function get_distance()
	{
		return $this->distance;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $esquive valeur de l'attribut esquive
	*/
	function get_esquive()
	{
		return $this->esquive;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $blocage valeur de l'attribut blocage
	*/
	function get_blocage()
	{
		return $this->blocage;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $incantation valeur de l'attribut incantation
	*/
	function get_incantation()
	{
		return $this->incantation;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(9) $sort_vie valeur de l'attribut sort_vie
	*/
	function get_sort_vie()
	{
		return $this->sort_vie;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $sort_element valeur de l'attribut sort_element
	*/
	function get_sort_element()
	{
		return $this->sort_element;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $sort_mort valeur de l'attribut sort_mort
	*/
	function get_sort_mort()
	{
		return $this->sort_mort;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $identification valeur de l'attribut identification
	*/
	function get_identification()
	{
		return $this->identification;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $craft valeur de l'attribut craft
	*/
	function get_craft()
	{
		return $this->craft;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $alchimie valeur de l'attribut alchimie
	*/
	function get_alchimie()
	{
		return $this->alchimie;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $architecture valeur de l'attribut architecture
	*/
	function get_architecture()
	{
		return $this->architecture;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $forge valeur de l'attribut forge
	*/
	function get_forge()
	{
		return $this->forge;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return mediumint(8) $survie valeur de l'attribut survie
	*/
	function get_survie()
	{
		return $this->survie;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return double $facteur_magie valeur de l'attribut facteur_magie
	*/
	function get_facteur_magie()
	{
		return $this->facteur_magie;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return double $facteur_sort_vie valeur de l'attribut facteur_sort_vie
	*/
	function get_facteur_sort_vie()
	{
		return $this->facteur_sort_vie;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return double $facteur_sort_mort valeur de l'attribut facteur_sort_mort
	*/
	function get_facteur_sort_mort()
	{
		return $this->facteur_sort_mort;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return double $facteur_sort_element valeur de l'attribut facteur_sort_element
	*/
	function get_facteur_sort_element()
	{
		return $this->facteur_sort_element;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $regen_hp valeur de l'attribut regen_hp
	*/
	function get_regen_hp()
	{
		return $this->regen_hp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $maj_hp valeur de l'attribut maj_hp
	*/
	function get_maj_hp()
	{
		return $this->maj_hp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $maj_mp valeur de l'attribut maj_mp
	*/
	function get_maj_mp()
	{
		return $this->maj_mp;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $point_sso valeur de l'attribut point_sso
	*/
	function get_point_sso()
	{
		return $this->point_sso;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $quete valeur de l'attribut quete
	*/
	function get_quete()
	{
		return $this->quete;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return text $quete_fini valeur de l'attribut quete_fini
	*/
	function get_quete_fini()
	{
		return $this->quete_fini;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $dernier_connexion valeur de l'attribut dernier_connexion
	*/
	function get_dernier_connexion()
	{
		return $this->dernier_connexion;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return varchar(50) $statut valeur de l'attribut statut
	*/
	function get_statut()
	{
		return $this->statut;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(11) $fin_ban valeur de l'attribut fin_ban
	*/
	function get_fin_ban()
	{
		return $this->fin_ban;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $frag valeur de l'attribut frag
	*/
	function get_frag()
	{
		return $this->frag;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return float $crime valeur de l'attribut crime
	*/
	function get_crime()
	{
		return $this->crime;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return int(10) $amende valeur de l'attribut amende
	*/
	function get_amende()
	{
		return $this->amende;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return enum('true','false') $teleport_roi valeur de l'attribut teleport_roi
	*/
	function get_teleport_roi()
	{
		return $this->teleport_roi;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $cache_classe valeur de l'attribut cache_classe
	*/
	function get_cache_classe()
	{
		return $this->cache_classe;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $cache_stat valeur de l'attribut cache_stat
	*/
	function get_cache_stat()
	{
		return $this->cache_stat;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $cache_niveau valeur de l'attribut cache_niveau
	*/
	function get_cache_niveau()
	{
		return $this->cache_niveau;
	}

	/**
	* Retourne la valeur de l'attribut
	* @access public
	* @param none
	* @return tinyint(3) $beta valeur de l'attribut beta
	*/
	function get_beta()
	{
		return $this->beta;
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $id valeur de l'attribut
	* @return none
	*/
	function set_id($id)
	{
		$this->id = $id;
		$this->champs_modif[] = 'id';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $mort valeur de l'attribut
	* @return none
	*/
	function set_mort($mort)
	{
		$this->mort = $mort;
		$this->champs_modif[] = 'mort';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $nom valeur de l'attribut
	* @return none
	*/
	function set_nom($nom)
	{
		$this->nom = $nom;
		$this->champs_modif[] = 'nom';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(40) $password valeur de l'attribut
	* @return none
	*/
	function set_password($password)
	{
		$this->password = $password;
		$this->champs_modif[] = 'password';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $exp valeur de l'attribut
	* @return none
	*/
	function set_exp($exp)
	{
		$this->exp = $exp;
		$this->champs_modif[] = 'exp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $honneur valeur de l'attribut
	* @return none
	*/
	function set_honneur($honneur)
	{
		$this->honneur = $honneur;
		$this->champs_modif[] = 'honneur';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $level valeur de l'attribut
	* @return none
	*/
	function set_level($level)
	{
		$this->level = $level;
		$this->champs_modif[] = 'level';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $rang_royaume valeur de l'attribut
	* @return none
	*/
	function set_rang_royaume($rang_royaume)
	{
		$this->rang_royaume = $rang_royaume;
		$this->champs_modif[] = 'rang_royaume';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $vie valeur de l'attribut
	* @return none
	*/
	function set_vie($vie)
	{
		$this->vie = $vie;
		$this->champs_modif[] = 'vie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $forcex valeur de l'attribut
	* @return none
	*/
	function set_forcex($forcex)
	{
		$this->forcex = $forcex;
		$this->champs_modif[] = 'forcex';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $dexterite valeur de l'attribut
	* @return none
	*/
	function set_dexterite($dexterite)
	{
		$this->dexterite = $dexterite;
		$this->champs_modif[] = 'dexterite';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $puissance valeur de l'attribut
	* @return none
	*/
	function set_puissance($puissance)
	{
		$this->puissance = $puissance;
		$this->champs_modif[] = 'puissance';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $volonte valeur de l'attribut
	* @return none
	*/
	function set_volonte($volonte)
	{
		$this->volonte = $volonte;
		$this->champs_modif[] = 'volonte';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $energie valeur de l'attribut
	* @return none
	*/
	function set_energie($energie)
	{
		$this->energie = $energie;
		$this->champs_modif[] = 'energie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(20) $race valeur de l'attribut
	* @return none
	*/
	function set_race($race)
	{
		$this->race = $race;
		$this->champs_modif[] = 'race';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(20) $classe valeur de l'attribut
	* @return none
	*/
	function set_classe($classe)
	{
		$this->classe = $classe;
		$this->champs_modif[] = 'classe';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $classe_id valeur de l'attribut
	* @return none
	*/
	function set_classe_id($classe_id)
	{
		$this->classe_id = $classe_id;
		$this->champs_modif[] = 'classe_id';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $inventaire valeur de l'attribut
	* @return none
	*/
	function set_inventaire($inventaire)
	{
		$this->inventaire = $inventaire;
		$this->champs_modif[] = 'inventaire';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $inventaire_slot valeur de l'attribut
	* @return none
	*/
	function set_inventaire_slot($inventaire_slot)
	{
		$this->inventaire_slot = $inventaire_slot;
		$this->champs_modif[] = 'inventaire_slot';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param smallint(6) $pa valeur de l'attribut
	* @return none
	*/
	function set_pa($pa)
	{
		$this->pa = $pa;
		$this->champs_modif[] = 'pa';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $dernieraction valeur de l'attribut
	* @return none
	*/
	function set_dernieraction($dernieraction)
	{
		$this->dernieraction = $dernieraction;
		$this->champs_modif[] = 'dernieraction';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $action_a valeur de l'attribut
	* @return none
	*/
	function set_action_a($action_a)
	{
		$this->action_a = $action_a;
		$this->champs_modif[] = 'action_a';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $action_d valeur de l'attribut
	* @return none
	*/
	function set_action_d($action_d)
	{
		$this->action_d = $action_d;
		$this->champs_modif[] = 'action_d';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $sort_jeu valeur de l'attribut
	* @return none
	*/
	function set_sort_jeu($sort_jeu)
	{
		$this->sort_jeu = $sort_jeu;
		$this->champs_modif[] = 'sort_jeu';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $sort_combat valeur de l'attribut
	* @return none
	*/
	function set_sort_combat($sort_combat)
	{
		$this->sort_combat = $sort_combat;
		$this->champs_modif[] = 'sort_combat';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $comp_combat valeur de l'attribut
	* @return none
	*/
	function set_comp_combat($comp_combat)
	{
		$this->comp_combat = $comp_combat;
		$this->champs_modif[] = 'comp_combat';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $comp_jeu valeur de l'attribut
	* @return none
	*/
	function set_comp_jeu($comp_jeu)
	{
		$this->comp_jeu = $comp_jeu;
		$this->champs_modif[] = 'comp_jeu';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $star valeur de l'attribut
	* @return none
	*/
	function set_star($star)
	{
		$this->star = $star;
		$this->champs_modif[] = 'star';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $x valeur de l'attribut
	* @return none
	*/
	function set_x($x)
	{
		$this->x = $x;
		$this->champs_modif[] = 'x';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $y valeur de l'attribut
	* @return none
	*/
	function set_y($y)
	{
		$this->y = $y;
		$this->champs_modif[] = 'y';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $groupe valeur de l'attribut
	* @return none
	*/
	function set_groupe($groupe)
	{
		$this->groupe = $groupe;
		$this->champs_modif[] = 'groupe';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $hp valeur de l'attribut
	* @return none
	*/
	function set_hp($hp)
	{
		$this->hp = $hp;
		$this->champs_modif[] = 'hp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param float $hp_max valeur de l'attribut
	* @return none
	*/
	function set_hp_max($hp_max)
	{
		$this->hp_max = $hp_max;
		$this->champs_modif[] = 'hp_max';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $mp valeur de l'attribut
	* @return none
	*/
	function set_mp($mp)
	{
		$this->mp = $mp;
		$this->champs_modif[] = 'mp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param float $mp_max valeur de l'attribut
	* @return none
	*/
	function set_mp_max($mp_max)
	{
		$this->mp_max = $mp_max;
		$this->champs_modif[] = 'mp_max';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $melee valeur de l'attribut
	* @return none
	*/
	function set_melee($melee)
	{
		$this->melee = $melee;
		$this->champs_modif[] = 'melee';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $distance valeur de l'attribut
	* @return none
	*/
	function set_distance($distance)
	{
		$this->distance = $distance;
		$this->champs_modif[] = 'distance';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $esquive valeur de l'attribut
	* @return none
	*/
	function set_esquive($esquive)
	{
		$this->esquive = $esquive;
		$this->champs_modif[] = 'esquive';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $blocage valeur de l'attribut
	* @return none
	*/
	function set_blocage($blocage)
	{
		$this->blocage = $blocage;
		$this->champs_modif[] = 'blocage';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $incantation valeur de l'attribut
	* @return none
	*/
	function set_incantation($incantation)
	{
		$this->incantation = $incantation;
		$this->champs_modif[] = 'incantation';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(9) $sort_vie valeur de l'attribut
	* @return none
	*/
	function set_sort_vie($sort_vie)
	{
		$this->sort_vie = $sort_vie;
		$this->champs_modif[] = 'sort_vie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $sort_element valeur de l'attribut
	* @return none
	*/
	function set_sort_element($sort_element)
	{
		$this->sort_element = $sort_element;
		$this->champs_modif[] = 'sort_element';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $sort_mort valeur de l'attribut
	* @return none
	*/
	function set_sort_mort($sort_mort)
	{
		$this->sort_mort = $sort_mort;
		$this->champs_modif[] = 'sort_mort';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $identification valeur de l'attribut
	* @return none
	*/
	function set_identification($identification)
	{
		$this->identification = $identification;
		$this->champs_modif[] = 'identification';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $craft valeur de l'attribut
	* @return none
	*/
	function set_craft($craft)
	{
		$this->craft = $craft;
		$this->champs_modif[] = 'craft';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $alchimie valeur de l'attribut
	* @return none
	*/
	function set_alchimie($alchimie)
	{
		$this->alchimie = $alchimie;
		$this->champs_modif[] = 'alchimie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $architecture valeur de l'attribut
	* @return none
	*/
	function set_architecture($architecture)
	{
		$this->architecture = $architecture;
		$this->champs_modif[] = 'architecture';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $forge valeur de l'attribut
	* @return none
	*/
	function set_forge($forge)
	{
		$this->forge = $forge;
		$this->champs_modif[] = 'forge';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param mediumint(8) $survie valeur de l'attribut
	* @return none
	*/
	function set_survie($survie)
	{
		$this->survie = $survie;
		$this->champs_modif[] = 'survie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param double $facteur_magie valeur de l'attribut
	* @return none
	*/
	function set_facteur_magie($facteur_magie)
	{
		$this->facteur_magie = $facteur_magie;
		$this->champs_modif[] = 'facteur_magie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param double $facteur_sort_vie valeur de l'attribut
	* @return none
	*/
	function set_facteur_sort_vie($facteur_sort_vie)
	{
		$this->facteur_sort_vie = $facteur_sort_vie;
		$this->champs_modif[] = 'facteur_sort_vie';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param double $facteur_sort_mort valeur de l'attribut
	* @return none
	*/
	function set_facteur_sort_mort($facteur_sort_mort)
	{
		$this->facteur_sort_mort = $facteur_sort_mort;
		$this->champs_modif[] = 'facteur_sort_mort';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param double $facteur_sort_element valeur de l'attribut
	* @return none
	*/
	function set_facteur_sort_element($facteur_sort_element)
	{
		$this->facteur_sort_element = $facteur_sort_element;
		$this->champs_modif[] = 'facteur_sort_element';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $regen_hp valeur de l'attribut
	* @return none
	*/
	function set_regen_hp($regen_hp)
	{
		$this->regen_hp = $regen_hp;
		$this->champs_modif[] = 'regen_hp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $maj_hp valeur de l'attribut
	* @return none
	*/
	function set_maj_hp($maj_hp)
	{
		$this->maj_hp = $maj_hp;
		$this->champs_modif[] = 'maj_hp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $maj_mp valeur de l'attribut
	* @return none
	*/
	function set_maj_mp($maj_mp)
	{
		$this->maj_mp = $maj_mp;
		$this->champs_modif[] = 'maj_mp';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $point_sso valeur de l'attribut
	* @return none
	*/
	function set_point_sso($point_sso)
	{
		$this->point_sso = $point_sso;
		$this->champs_modif[] = 'point_sso';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $quete valeur de l'attribut
	* @return none
	*/
	function set_quete($quete)
	{
		$this->quete = $quete;
		$this->champs_modif[] = 'quete';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param text $quete_fini valeur de l'attribut
	* @return none
	*/
	function set_quete_fini($quete_fini)
	{
		$this->quete_fini = $quete_fini;
		$this->champs_modif[] = 'quete_fini';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $dernier_connexion valeur de l'attribut
	* @return none
	*/
	function set_dernier_connexion($dernier_connexion)
	{
		$this->dernier_connexion = $dernier_connexion;
		$this->champs_modif[] = 'dernier_connexion';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param varchar(50) $statut valeur de l'attribut
	* @return none
	*/
	function set_statut($statut)
	{
		$this->statut = $statut;
		$this->champs_modif[] = 'statut';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(11) $fin_ban valeur de l'attribut
	* @return none
	*/
	function set_fin_ban($fin_ban)
	{
		$this->fin_ban = $fin_ban;
		$this->champs_modif[] = 'fin_ban';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $frag valeur de l'attribut
	* @return none
	*/
	function set_frag($frag)
	{
		$this->frag = $frag;
		$this->champs_modif[] = 'frag';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param float $crime valeur de l'attribut
	* @return none
	*/
	function set_crime($crime)
	{
		$this->crime = $crime;
		$this->champs_modif[] = 'crime';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param int(10) $amende valeur de l'attribut
	* @return none
	*/
	function set_amende($amende)
	{
		$this->amende = $amende;
		$this->champs_modif[] = 'amende';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param enum('true','false') $teleport_roi valeur de l'attribut
	* @return none
	*/
	function set_teleport_roi($teleport_roi)
	{
		$this->teleport_roi = $teleport_roi;
		$this->champs_modif[] = 'teleport_roi';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $cache_classe valeur de l'attribut
	* @return none
	*/
	function set_cache_classe($cache_classe)
	{
		$this->cache_classe = $cache_classe;
		$this->champs_modif[] = 'cache_classe';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $cache_stat valeur de l'attribut
	* @return none
	*/
	function set_cache_stat($cache_stat)
	{
		$this->cache_stat = $cache_stat;
		$this->champs_modif[] = 'cache_stat';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $cache_niveau valeur de l'attribut
	* @return none
	*/
	function set_cache_niveau($cache_niveau)
	{
		$this->cache_niveau = $cache_niveau;
		$this->champs_modif[] = 'cache_niveau';
	}

	/**
	* Modifie la valeur de l'attribut
	* @access public
	* @param tinyint(3) $beta valeur de l'attribut
	* @return none
	*/
	function set_beta($beta)
	{
		$this->beta = $beta;
		$this->champs_modif[] = 'beta';
	}
//fonction
	function get_buff($nom = false, $champ = false)
	{
		if(!$nom)
		{
			$this->buff = buff::create(array('id_perso', 'debuff'), array($this->id, 0), 'type');
			return $this->buff;
		}
		else
		{
			if(!isset($this->buff)) $this->get_buff();
			return $this->buff[$nom][$champ];
		}
	}

	function get_debuff($nom = false, $champ = false)
	{
		if(!$nom)
		{
			$this->debuff = buff::create(array('id_perso', 'debuff'), array($this->id, 1));
			return $this->debuff;
		}
		else
		{
			if(!isset($this->debuff)) $this->get_debuff();
			return $this->debuff[$nom][$champ];
		}
	}

	function is_buff($nom)
	{
		if(!isset($this->buff)) $this->get_buff();
		if(is_array($this->buff)) return array_key_exists($nom, $this->buff);
		else return false;
	}

	function is_debuff()
	{
		if(!isset($this->debuff)) $this->get_debuff();
		if(is_array($this->debuff)) return array_key_exists($nom, $this->debuff);
		else return false;
	}

	function get_grade()
	{
		if(!isset($this->grade)) $this->grade = new grade($this->rang_royaume);
		return $this->grade;
	}

	function get_enchantement()
	{
		return array();
	}

	function is_enchantement()
	{
		return false;
	}

	function get_competence()
	{
		return array();
	}

	function get_arme_degat()
	{
	}

	function get_artisanat()
	{
		return round(sqrt(($this->architecture + $this->forge + $this->alchimie) * 10));
	}

	function get_inventaire_partie($partie)
	{
		if(!isset($this->inventaire_array)) $this->inventaire_array = unserialize($this->get_inventaire());
		return $this->inventaire_array[$partie];
	}

	function get_inventaire_slot_partie($partie = false)
	{
		if(!isset($this->inventaire_slot_array)) $this->inventaire_slot_array = unserialize($this->get_inventaire_slot());
		if(!$partie) return $this->inventaire_slot_array;
		else return $this->inventaire_slot_array[$partie];
	}

	function get_armure()
	{
		if(!isset($this->armure))
		{
			$this->pp = 0;
			$this->pm = 0;
			// Pièces d'armure
			$partie_armure = array('tete', 'torse', 'main', 'ceinture', 'jambe', 'chaussure', 'dos', 'cou', 'doigt');
			foreach($partie_armure as $partie)
			{
				if($partie != '')
				{
					$partie_d = decompose_objet($this->get_inventaire_partie($partie));
					if($partie_d['id_objet'] != '')
					{
						$requete = "SELECT PP, PM, effet FROM armure WHERE id = ".$partie_d['id_objet'];
						$req = $db->query($requete);
						$row = $db->read_row($req);
						$this->pp += $row[0];
						$this->pm += $row[1];
						// Effets magiques
						$effet = explode(';', $row[2]);
						foreach($effet as $eff)
						{
							$explode = explode('-', $eff);
							$R_perso['objet_effet'][$objet_effet_id]['id'] = $explode[0];
							$R_perso['objet_effet'][$objet_effet_id]['effet'] = $explode[1];
						}
						$objet_effet_id++;
					}
					// Gemmes
					if($partie_d['enchantement'] > 0)
					{
						$R_perso = enchant($partie_d['enchantement'], $R_perso);
					}
				}
			}
		}
		return $this->armure;
	}

	function get_pm($base = false)
	{
		if(!isset($this->pm))
		{
			$this->get_armure();
		}
		if(!$base) return $this->pm;
		else return $this->pm_base;
	}

	function get_pp($base = false)
	{
		if(!isset($this->pp))
		{
			$this->get_armure();
		}
		if(!$base) return $this->pp;
		else return $this->pp_base;
	}

	function get_reserve($base = false)
	{
		if(!isset($this->reserve)) $this->reserve = ceil(2.1 * ($this->energie + floor(($rhis->energie - 8) / 2)));
		return $this->reserve;
	}

	function get_coef_melee()
	{
		if(!isset($this->coef_melee)) $this->coef_melee = $this->forcex * $this->melee;
		return $this->coef_melee;
	}

	function get_coef_incantation()
	{
		if(!isset($this->coef_incantation)) $this->coef_incantation = $this->puissance * $this->incantation;
		return $this->coef_incantation;
	}

	function get_coef_distance()
	{
		if(!isset($this->coef_distance)) $this->coef_distance = round(($this->forcex + $this->dexterite) / 2) * $this->distance;
		return $this->coef_distance;
	}

	function get_coef_blocage()
	{
		if(!isset($this->coef_blocage)) $this->coef_blocage = round(($this->forcex + $this->dexterite) / 2) * $this->blocage;
		return $this->coef_blocage;
	}

	function get_pos()
	{
		return convert_in_pos($this->x, $this->y);
	}

	function get_force() { return $this->get_forcex(); }
	function set_force($force) { $this->set_forcex($force); }

	function inventaire()
	{
		return unserialize($this->inventaire);
	}

	function get_distance_tir()
	{
		$arme = $this->inventaire()->main_droite;
		if ($arme != '')
		{
			global $db;
			$arme_d = decompose_objet($R_perso['arme']);
			$requete = "SELECT distance_tir FROM arme WHERE id = ".$arme_d['id_objet'];
			$req = $db->query($requete);
			$row = $db->read_array($req);
			return $row['distance_tir'];
		}
		return 0;
	}
}
?>
