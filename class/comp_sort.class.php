<?php
/**
 * @file comp_sort.class.php
 * Définition de la classe comp_sort servant de base aux compétences et sorts
 */

/**
 * Classe comp_sort_buff
 * Classe comp_sort servant de base aux compétences et sorts
 */
class comp_sort extends comp_sort_buff
{
	/**
	 * @name Informations générales.
	 * Donnée et méthode sur les inforamations "générales" : type, niveau, …
	 */
  // @{
	protected $comp_assoc; ///< Compétence associée
	protected $carac_assoc; ///< Caractéristique associée
	protected $comp_requis; ///< Requis dans la compétence
	protected $carac_requis; ///< Requis dans la caractéristique (inutilisé)
	protected $effet2;  ///< Deuxième effet
	protected $requis;  ///< Compétence ou sort requis pour apprendre celui-ci
	protected $cible;  ///< Cible de la compétence ou du sort
	protected $description;  ///< Description du buff
	protected $mp;   ///< Coût en MP ou en RM
	protected $prix;  ///< Prix de la compétence ou le sort
	protected $lvl_batiment;   ///< Niveau de l'école qui vent la compétence ou le sort
	
	const cible_perso = 1;  ///< Valeur de l'attribut cible si celle-ci est le lanceur
	const cible_unique = 2;  ///< Valeur de l'attribut cible si celle-ci est un unique personnage
	const cible_groupe = 3;  ///< Valeur de l'attribut cible si celle-ci est le groupe du lanceur
	const cible_autre = 4;  ///< Valeur de l'attribut cible si celle-ci est un autre personnage
	const cible_autregrp = 5;  ///< Valeur de l'attribut cible si celle-ci est un autre groupe
	const cible_case = 6;  ///< Valeur de l'attribut cible pour les débuffs sur une case
	const cible_9cases = 8;  ///< Valeur de l'attribut cible pour les débuffs sur un carré de 3x3 cases

  /// Renvoie la compétence associée
	function get_comp_assoc()
	{
		return $this->comp_assoc;
	}
	/// Modifie la compétence associée
	function set_comp_assoc($comp_assoc)
	{
		$this->comp_assoc = $comp_assoc;
		$this->champs_modif[] = 'comp_assoc';
	}

  /// Renvoie la caractéristique associée
	function get_carac_assoc()
	{
		return $this->carac_assoc;
	}
	/// Modifie la caractéristique associée
	function set_carac_assoc($carac_assoc)
	{
		$this->carac_assoc = $carac_assoc;
		$this->champs_modif[] = 'carac_assoc';
	}

  /// Renvoie le requis dans la compétence
	function get_carac_requis()
	{
		return $this->carac_requis;
	}
	/// Modifie le requis dans la compétence
	function set_carac_requis($carac_requis)
	{
		$this->carac_requis = $carac_requis;
		$this->champs_modif[] = 'carac_requis';
	}

  /// Renvoie le requis dans la caractéristique
	function get_comp_requis()
	{
		return $this->comp_requis;
	}
	/// Modifie le requis dans la caractéristique
	function set_comp_requis($comp_requis)
	{
		$this->comp_requis = $comp_requis;
		$this->champs_modif[] = 'comp_requis';
	}

  /// Renvoie le deuxième effet
	function get_effet2()
	{
		return $this->effet2;
	}
	/// Modifie le deuxième effet
	function set_effet2($effet2)
	{
		$this->effet2 = $effet2;
		$this->champs_modif[] = 'effet2';
	}

  /// Renvoie la compétence ou sort requis pour apprendre celui-ci
	function get_requis()
	{
		return $this->requis;
	}
	/// Modifie la compétence ou sort requis pour apprendre celui-ci
	function set_requis($requis)
	{
		$this->requis = $requis;
		$this->champs_modif[] = 'requis';
	}

  /// Renvoie la cible de la compétence ou du sort
	function get_cible()
	{
		return $this->cible;
	}
	/// Modifie la cible de la compétence ou du sort
	function set_cible($cible)
	{
		$this->cible = $cible;
		$this->champs_modif[] = 'cible';
	}
	
	/// Renvoie la description de la compétence ou le sort
	function get_description($format=false)
	{
    if($format)
      return $this->formate_description( $this->description );
    else
		  return $this->description;
	}
	/// Modifie la description de la compétence ou le sort
	function set_description($description)
	{
		$this->description = $description;
		$this->champs_modif[] = 'description';
	}
	/// Formate la description
	function formate_description($texte)
	{
  	while(preg_match("`%([a-z0-9]*)%`i",$texte, $regs))
  	{
  		$get = 'get_'.$regs[1];
  		$texte = str_replace('%'.$regs[1].'%', $this->$get(), $texte);
  	}
  	// Evaluation
  	$valeur = '';
  	while(preg_match('`@(.*)@`', $texte, $regs))
  	{
  		$r = $regs[1];
  		eval("\$valeur = ".$r.";");
  		$texte = str_replace('@'.$regs[1].'@', $valeur, $texte);
  	}
  	return $texte;
  }

  /// Renvoie le coût en MP ou en RM
	function get_mp()
	{
		return $this->mp;
	}
	/// Modifie le coût en MP ou en RM
	function set_mp($mp)
	{
		$this->mp = $mp;
		$this->champs_modif[] = 'mp';
	}

  /// Renvoie le prix de la compétence ou le sort
	function get_prix()
	{
		return $this->prix;
	}
	/// Modifie le prix de la compétence ou le sort
	function set_prix($prix)
	{
		$this->prix = $prix;
		$this->champs_modif[] = 'prix';
	}

  /// Renvoie le niveau de l'école qui vent la compétence ou le sort
	function get_lvl_batiment()
	{
		return $this->lvl_batiment;
	}
	/// Modifie le niveau de l'école qui vent la compétence ou le sort
	function set_lvl_batiment($lvl_batiment)
	{
		$this->lvl_batiment = $lvl_batiment;
		$this->champs_modif[] = 'lvl_batiment';
	}
	// @}

	/**
	 * @name Accès à la base de données
	 * Méthode gérant la lecture et l'écriture dans la base de données
	 */
  // @{
	/**
	 * Constructeur
	 * @param id             Id dans la base de donnée ou tableau associatif contenant les informations permettant la création de l'objet
	 * @param type           Type générique.
	 * @param effet          Effet principal.
	 * @param duree          Durée
	 * @param comp_assoc     Compétence associée
	 * @param carac_assoc    Caractéristique associée
	 * @param comp_requis    Requis dans la compétence
	 * @param carac_requis   Requis dans la caractéristique (inutilisé)
	 * @param effet2         Deuxième effet
	 * @param requis         Compétence ou sort requis pour apprendre celui-ci
	 * @param cible          Cible de la compétence ou du sort
	 * @param description    Description du buff
	 * @param mp             Coût en MP ou en RM
	 * @param prix           Prix de la compétence ou le sort
	 * @param lvl_batiment   Niveau de l'école qui vent la compétence ou le sort
	 */
	function __construct($id=0, $nom='', $type='', $effet=0, $duree=0, $comp_assoc='', $carac_assoc='', $comp_requis=0, $carac_requis=0,
    $effet2=0, $requis=0, $cible=0, $description='', $mp=0, $prix=0, $lvl_batiment=0)
	{
		//Verification nombre d'arguments pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
      comp_sort_buff::__construct($id, $type, $effet, $duree);
			$this->comp_assoc = $comp_assoc;
			$this->carac_assoc = $carac_assoc;
			$this->comp_requis = $comp_requis;
			$this->carac_requis = $carac_requis;
			$this->effet2 = $effet2;
			$this->requis = $requis;
			$this->cible = $cible;
			$this->description = $description;
			$this->mp = $mp;
			$this->prix = $prix;
			$this->lvl_batiment = $lvl_batiment;
		}
  }

	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    comp_sort_buff::init_tab($vals);
		$this->comp_assoc = $vals['comp_assoc'];
		$this->carac_assoc = $vals['carac_assoc'];
		$this->comp_requis = $vals['comp_requis'];
		$this->carac_requis = $vals['carac_requis'];
		$this->effet2 = $vals['effet2'];
		$this->requis = $vals['requis'];
		$this->cible = $vals['cible'];
		$this->description = $vals['description'];
		$this->mp = $vals['mp'];
		$this->prix = $vals['prix'];
		$this->lvl_batiment = $vals['lvl_batiment'];
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return comp_sort_buff::get_liste_champs().', comp_assoc, carac_assoc, comp_requis, carac_requis, effet2, requis, cible, description, mp, prix, lvl_batiment';
  }
	/// Renvoie la liste des valeurs des champspour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return comp_sort_buff::get_valeurs_insert().', "'.mysql_escape_string($this->comp_assoc).'", "'.mysql_escape_string($this->carac_assoc).'", '.$this->comp_requis.', '.$this->carac_requis.', '.$this->effet2.', '.$this->requis.', '.$this->cible.', "'.mysql_escape_string($this->description).'", '.$this->mp.', '.$this->prix.', '.$this->lvl_batiment;
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return comp_sort_buff::get_liste_update().', comp_assoc = "'.mysql_escape_string($this->comp_assoc).'", carac_assoc = "'.mysql_escape_string($this->carac_assoc).'", comp_requis = '.$this->comp_requis.', carac_requis = '.$this->carac_requis.', effet2 = '.$this->effet2.', requis = '.$this->requis.', cible = '.$this->cible.', description = "'.mysql_escape_string($this->description).'", mp = '.$this->mp.', prix = '.$this->prix.', lvl_batiment = '.$this->lvl_batiment;
	}
	// @}


	/**
	 * @name Gestion du lancement
	 * Méthodes utilisées lors de l'utilisation (lancement) de la compétence / du sort
	 */
  // @{
	/**
	 * Renvoie la liste des cibles
	 * @param cible  Cible principale telle que donnée à la méthode lancer
	 * @return  liste des cibles sous forme de tableau
	 */
  function get_liste_cibles($cible, $groupe=true)
  {
    print_debug("type de cible : ".$this->get_cible()."<br/>");
    switch( $this->get_cible() )
    {
    case comp_sort::cible_groupe:
    case comp_sort::cible_autregrp:
      $groupe = true;
    case comp_sort::cible_unique:
      if( $cible->get_race() == 'neutre' )
      {
        if( $groupe )
        {
          $champs = array('x', 'y');
          $valeurs = array($cible->get_x(), $cible->get_y());
          $monstres = map_monstre::create($champs, $valeurs, 'id ASC', false, false);
          $entites = array();
          foreach($monstres as $m)
          {
            $entites[] = new entitenj($m);
          }
          return $entites;
        }
        else
          return map_monstre::create(array('x', 'y'), array($cible->get_x(), $cible->get_y()));
      }
      else if($groupe && $cible->get_groupe() != 0)
      {
        $groupe_cible = new groupe( $cible->get_groupe() );
        $cibles = array();
				foreach($groupe_cible->get_membre() as $membre)
				{
					// On peut agir avec les membres du groupe si ils sont a 7 ou moins de distance
					if($membre->get_distance_pytagore($cible) <= 7)
            $cibles[] = new perso($membre->get_id_joueur());
				}
				return $cibles;
      }
    case comp_sort::cible_perso:
    case comp_sort::cible_autre:
      return Array( $cible );
    case comp_sort::cible_case:
      $champs = array('x', 'y', 'statut');
      $valeurs = array($cible->get_x(), $cible->get_y(), 'actif');
      return perso::create($champs, $valeurs, 'id ASC', false, false);
    case comp_sort::cible_9cases:
      $cond = 'ABS(CAST(x AS SIGNED) -'.$cible->get_x().') <= 1 AND ABS(CAST(y AS SIGNED) - '.$cible->get_y().') <= 1 AND statut LIKE "actif"';
      return perso::create(null, null, 'id ASC', false, $cond);
    }
  }

  /**
   * Effectuer un test entre deux potentiel
   * @param  $pot_action    potentiel pour effectuer l'action (offensif)
   * @param  $pot_oppos     potentiel du jet d'opposition (défensif)
   * @return   true si le teste à réussi, false sinon
   */
  static function test_potentiel($pot_action, $pot_oppos)
  {
  	$attaque = rand(0, $pot_action);
  	$defense = rand(0, $pot_oppos);
  	print_debug('Potentiel attaquant : '.$pot_action.
							'<br />Potentiel défenseur : '.$pot_oppos.
							'<br />Résultat => Attaquant : '.$attaque.' | Défense '.
							$defense.'<br />');
    return $attaque > $defense;
  }

  /**
   * Effectuer un test entre un dé et une valeur fixe
   * @param  $de      dé à lancer
   * @param  $seuil   seuil à ne pas dépasser
   * @return   true si le teste à réussi, false sinon
   */
  static function test_de($de, $seuil)
  {
  	$val = rand(0, $de);
		print_debug('1d'.$de.' doit être inférieur a '.$seuil.'<br />
		Résultat => '.$val.' doit être inférieur a '.$seuil.'<br />');
    return $val < $seuil;
  }

  /**
   * Fonction permettant de calculer les dés de dégat en fonction de la force et de l'arme de la personne
   *
   * @param  $force         Force du personnage.
   * @param  $degat_arme    Facteur de dégâts de l'arme.
   *
   * @return  Tableau contenant les dés à lancer (chaque dès apparait autant de fois dans la tableu qu'il faut le lancer).
   */
  static function calcule_des($force, $degat_arme)
  {
    // tableau utilisé pour déterminer les dés
  	$tab_de = array();
  	$tab_de[0][0] = 2;
  	$tab_de[0][1] = 3;
  	$tab_de[0][2] = 4;
  	$tab_de[0][3] = 5;
  	$tab_de[0][4] = 6;
  	$tab_de[0][5] = 7;
  	$tab_de[0][6] = 11;
  	$tab_de[1][0] = 2;
  	$tab_de[1][1] = 4;
  	$tab_de[1][2] = 6;
  	$tab_de[1][3] = 8;
  	$tab_de[1][4] = 10;
  	$tab_de[1][5] = 12;
  	$tab_de[1][6] = 20;
  	// Facteur de dégâts
  	$potentiel = ceil($force / 3) + $degat_arme;
  	// Tableau des dés à lancer
  	$de_degat = array();
  	while($potentiel > 1)
  	{
  		if (($potentiel > 7) AND ($potentiel < 15))
  		{
  			$des = array_search(ceil($potentiel / 2), $tab_de[0]);
  			$potentiel = $potentiel - $tab_de[0][$des];
  			$de[] = $tab_de[1][$des];
  		}
  		else
  		{
  			$z = 6;
  			$check = true;
  			while($z >= 0 && $check)
  			{
  				if ($potentiel >= $tab_de[0][$z])
  				{
  					$potentiel = $potentiel - $tab_de[0][$z];
  					$de[] = $tab_de[1][$z];
  					$check = false;
  				}
  				$z--;
  			}
  		}
  	}
  	return $de;
  }
  
  /**
   * Méthode effecute le lancer de dés
   * @param  $des   Tableau des dés tels que donné par de_degat ou de_soin
   */
  static function lance_des($des)
  {
		$degat = 0;
		$i = 0;
		$dbg_msg = '';
		while($i < count($des))
		{
			$de = rand(1, $des[$i]);
			$degat += $de;
			$dbg_msg .= 'Max : '.$des[$i].' - Dé : '.$de.'<br />';
			$i++;
		}
		print_debug($dbg_msg);
		return $degat;
  }

  /**
   * Calcul le pourcentage de dégâts absorbé en fonction de la PP
   * @param  $pp    PP.
   * @return    Pourcentage de dégâts absorbé.
   */
  static function calcul_pp($pp)
  {
  	return (1 - (sqrt($pp / 10) / 40));
  }
	// @}
}
?>
