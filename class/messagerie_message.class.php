<?php
class messagerie_message
{
	public $id_message;
	public $id_auteur;
	public $id_dest;
	public $titre;
	public $message;
	public $id_thread;
	public $date;
	
	/**	
	    *  	Constructeur permettant la cr�ation d'un message.
	    *	Les valeurs par d�faut sont celles de la base de donn�e.
	    *	Le constructeur accepte plusieurs types d'appels:
	    *		-Objets() qui construit un message "vide".
	    *		-Objets($id) qui va chercher le message dont l'id est $id_message dans la base.
	**/
	function __construct($id_message = 0, $id_auteur = 0, $id_dest = 0, $titre = 'Sans titre', $message = '', $id_thread = 0, $date = null)
	{
		global $db;
		if($date == null) $date = time();
		//Verification du nombre et du type d'argument pour construire le message adequat.
		if( (func_num_args() == 1) && is_numeric($id_message) )
		{
			$requeteSQL = $db->query('SELECT id_auteur, id_dest, titre, message, id_thread, date FROM messagerie_message WHERE id_message = '.$id_message);
			//Si le thread est dans la base, on le charge sinon on cr�e un thread vide.
			if( $db->num_rows($requeteSQL) > 0 )
			{
				list($this->id_auteur, $this->id_dest, $this->titre, $this->message, $this->id_thread, $this->date) = $db->read_row($requeteSQL);
			}
			else
				$this->__construct();
		}
		else
		{
			$this->id_auteur = $id_auteur;
			$this->id_dest = $id_dest;
			$this->titre = $titre;
			$this->message = $message;
			$this->id_thread = $id_thread;
			$this->date = $date;
		}
		$this->id_message = $id_message;
	}
	
	//Fonction d'ajout/modification.
	function sauver()
	{
		global $db;
		if( $id_message > 0 )
		{
			$requete = 'UPDATE TABLE messagerie_message SET ';
			$requete .= 'id_auteur = '.$this->id_auteur.', id_dest = '.$this->id_dest.', titre = "'.$this->dest.'", message = "'.$this->message.'", id_thread = '.$this->id_thread.', date = '.$this->date;
			$requete .= ' WHERE id_message = '.$this->id_message;
			$db->query($requete);
		}
		else
		{
			$requete = 'INSERT INTO messagerie_message (id_auteur, id_dest, titre, message, id_thread, date) VALUES(';
			$requete .= $this->id_auteur.', '.$this->id_dest.', "'.$this->message.'", "'.$this->message.'", '.$this->id_thread.', '.$this->date.')';
			$db->query($requete);
			//R�cuperation du dernier ID ins�r�.
			list($this->id_message) = $db->last_insert_id();
		}
	}
	
	//supprimer le message de la base.
	function supprimer()
	{
		global $db;
		if( $this->id_message > 0 )
		{
			$requete = 'DELETE FROM messagerie_message WHERE id_message = '.$this->id_message;
			$db->query($requete);
		}
	}
	
	function __toString()
	{
		return $this->id_auteur.', '.$this->id_dest.', '.$this->titre.', '.$this->message.', '.$this->id_thread.', '.$this->date;
	}
}
?>