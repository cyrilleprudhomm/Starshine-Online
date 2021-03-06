<?php
/**
 * @file joueur.class.php
 * Définition de la classe gérant les comptes joueur
 */

/// Classe gérant un compte joueur
class joueur extends table
{
	protected $login;  ///< login du joueur.
	protected $mdp;  ///< mot de passe.
	protected $pseudo;  ///< pseudo du joueur.
	protected $droits;  ///< droits d'accès.
	protected $email;  ///< adresse e-mail.
	protected $mdp_forum;  ///< hash du mot de passe pour le forum.
	protected $mdp_jabber;  ///< hash du mot de passe pour jabber.
	
	// Droits possibles
	const droit_prog = 1;  ///< Droit pour la programmation.
	const droit_modo = 2;  ///< Droit pour la modération.
	const droit_graph = 4;  ///< Droit pour les graphismes.
	const droit_anim = 8;  ///< Droit pour l'animation.
	const droit_concept = 16;  ///< Droit pour la conception.
	const droit_admin = 32;  ///< Droits pour l'administration.
	const droit_jouer = 64;  ///< Droit pour jouer (afin de pouvoir supprimer des comptes).
	const droit_staf = 128;  ///< Droit à faire parti du staf.
	
	// Combinaisons
	const droit_pnj = 128;  ///< Accès aux personnages PNJ
	
	//! Constructeur
	/**
		Le constructeur va initialiser les attributs

		@param int(11) $id  L'id du joueur
		@param String $login  L'identifiant du joueur
		@param String $mdp  Le mot de passe
		@param String $pseudo Le nom du joueur (vide par defaut)
		@param int(11) $droits Niveau d'acces du joueur (0 par défaut)
		@param String $email Adresse internet du joueur(null par défaut)
		@param String $mdp_forum Hash du mot de passe pour le forum (null par défaut)
	*/
	function __construct($id=0, $login = '', $mdp = '', $pseudo = '', $droits = 64, $email = '', $mdp_forum='')
	{
		//Verification nombre d'arguments pour construire l'etat adequat.
		if( func_num_args() == 1 )
		{
			$this->charger($id);
		}
		else
		{
			$this->id = $id;
			$this->login = $login;
			$this->mdp = $mdp;
			$this->pseudo = $pseudo;
			$this->droits = $droits;
			$this->email = $email;
			$this->mdp_forum = $mdp_forum;
		}

	}
	/**
	 * Initialise les données membres à l'aide d'un tableau
	 * @param array $vals    Tableau contenant les valeurs des données.
	 */
  protected function init_tab($vals)
  {
    table::init_tab($vals);
		$this->login = $vals['login'];
		$this->mdp = $vals['mdp'];
		$this->pseudo = $vals['pseudo'];
		$this->droits = $vals['droits'];
		$this->email = $vals['email'];
		$this->mdp_forum = $vals['mdp_forum'];
  }

  /// Renvoie le pseudo
	function get_pseudo()
	{
		return $this->pseudo;
	}

  /// Modifie le pseudo
	function set_pseudo($pseudo)
	{
		$this->pseudo = $pseudo;
		$this->champs_modif[] = 'pseudo';
	}

  /// Renvoie le login
	function get_login()
	{
		return $this->login;
	}

  /// Modifie le login
	function set_login($login)
	{
		$this->login = $login;
		$this->champs_modif[] = 'login';
	}

  /// Renvoie le mot de passe
	function get_mdp()
	{
		return $this->mdp;
	}

  /**
   * Modifie le mot de passe
   * @param $mdp  hash du nouveau mdp
   * @param $sel  true s'il faut saler
   */
	function set_mdp($mdp, $sel=true)
	{
    if( $sel )
		  $this->mdp = $this->sel($mdp);
    else
		  $this->mdp = $mdp;
		$this->champs_modif[] = 'mdp';
	}
	
	/**
	 * Test le mot de passe
	 * @param $mdp   md5 du mot de passe
	 * @return   true si le mot de passe est bon, false sinon
	 */
  function test_mdp($mdp)
  {
    return $this->sel($mdp) == $this->mdp;
  }

  /// Renvoie le mot de passe du forum
	function get_mdp_forum()
	{
		return $this->mdp_forum;
	}

  /// Modifie le mot de passe du forum
	function set_mdp_forum($mdp)
	{
    $this->mdp_forum = $mdp;
		$this->champs_modif[] = 'mdp_forum';
	}

  /// Renvoie le mot de passe de jabber
	function get_mdp_jabber()
	{
		return $this->mdp_jabber;
	}

  /// Modifie le mot de passe de jabber
	function set_mdp_jabber($mdp)
	{
    $this->mdp_jabber = $mdp;
		$this->champs_modif[] = 'mdp_jabber';
	}

  /// Renvoie l'e-mail
	function get_email()
	{
		return $this->email;
	}

  /// Modifie l'e-mail
	function set_email($email)
	{
		$this->email = $email;
		$this->champs_modif[] = 'email';
	}

  /// Renvoie les droits
	function get_droits()
	{
		return $this->droits;
	}

  /// Modifie les droits
	function set_droits($droits)
	{
		$this->droits = $droits;
		$this->champs_modif[] = 'droits';
	}

	/// "Sale" le mot de passe
	function sel($mdp)
	{
    return sha1($this->login.'!$'.$mdp);
  }
  
  /**
   * Cherche et renvoie un compte joueur à partir de son login ou de son pseudo
   * @param $nom    Login ou pseudo
   * @return    Objet représentant le compte joueur ou null
   */
  static function Chercher($nom)
  {
    global $db;
    $requete = 'SELECT * FROM joueur WHERE login = "'.sSQL($nom, SSQL_STRING).'" OR pseudo = "'.sSQL($nom, SSQL_STRING).'"';
	  $req = $db->query($requete);
	  if( $db->num_rows($req) > 0 )
    {
      $row = $db->read_assoc($req);
      return new joueur($row);
    }
    else
      return null;
  }

	/// Renvoie la liste des champs pour une insertion dans la base
	protected function get_liste_champs()
	{
    return 'login, mdp, pseudo, droits, email, mdp_forum';
  }
	/// Renvoie la liste des valeurs des champs pour une insertion dans la base
	protected function get_valeurs_insert()
	{
		return '"'.mysql_escape_string($this->login).'", "'.$this->mdp.'", "'.mysql_escape_string($this->pseudo).'", "'.$this->droits.'", "'.mysql_escape_string($this->email).'", "'.mysql_escape_string($this->mdp_forum).'"';
	}
	/// Renvoie la liste des champs et valeurs pour une mise-à-jour dans la base
	protected function get_liste_update()
	{
		return 'login = "'.mysql_escape_string($this->login).'", mdp = "'.$this->mdp.'", pseudo = "'.mysql_escape_string($this->pseudo).'", droits = "'.$this->droits.'", email = "'.mysql_escape_string($this->email).'", mdp_forum = "'.mysql_escape_string($this->mdp_forum).'"';
	}

}

?>