<?php
/// @addtogroup Interface
/**
 * @file interf_factory.class.php
 * Fabrique (abstraite) pour les classes de l'interface
 */
 
include_once(root.'class/interface.class.php');
include_once(root.'class/interface_avance.class.php');

/**
 * Fabrique (abstraite) pour les classes de l'interface par défaut.
 * Correspond à l'interface par défaut.
 */
class interf_factory
{
  /**
   * Fabrique de la fabrique abstraite : renvoie une instance de la bonne fabrique abstaite suivant l'interface à utiliser.
   */
  static function factory()
  {
    return new interf_factory();
  }
  /**
   * Renvoie la bonne instance de la classe pour afficher quelque chose dans le panneau droit
   * @param $titre  titre du panneau.
   */
  function creer_princ_droit($titre)
  {
    return new interf_princ_droit($titre);
  }
  /**
   * Renvoie la bonne instance de la classe pour afficher l'inventaire
   * @param $perso      Objet représentant le personnage dont il faut afficher l'inventaire
   * @param $adresse    Adresse de la page
   * @param $slot       slot à afficher
   */
  function creer_inventaire(&$perso, $adresse, $slot)
  {
    return new interf_inventaire($perso, $adresse, $slot);
  }
  /**
   * Renvoie la bonne instance de la classe pour afficher un slot de l'inventaire
   * @param $perso      Objet représentant le personnage dont il faut afficher l'inventaire
   * @param $adresse    Adresse de la page
   * @param $slot       slot à afficher
   * @param $slot       Slot à afficher
   * @param $modif      indique si on peut modifier l'inventaire
   */
  function creer_inventaire_slot(&$perso, $adresse, $slot, $modif)
  {
    return new interf_inventaire_slot($perso, $adresse, $slot, $modif);
  }
}
?>
