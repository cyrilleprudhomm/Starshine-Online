<?php // -*- mode: php; tab-width: 2 -*-
if (file_exists('root.php'))
  include_once('root.php');

//Connexion obligatoire
$connexion = true;
//Inclusion du haut du document html
$interface_v2 = true;
include_once(root.'haut.php');
if(array_key_exists('ID', $_SESSION) && !empty($_SESSION['ID']))
	$joueur = new perso($_SESSION['ID']);
else
{
	echo 'Vous êtes déconnecté, veuillez vous reconnecter.';
	var_dump($_SESSION);
	exit();
}
?>
<script type="text/javascript">
window.onload = function()
{
	<?php
	 if ($joueur->get_tuto() > 0 ) echo'affichePopUp(\'texte_tuto.php\');';
	elseif($_COOKIE['dernier_affichage_popup'] < (time() - 3600)) echo 'affichePopUp(\'message_accueil.php\');'; ?>
}
</script>
<?php
//Si c'est pour entrer dans un donjon
if(array_key_exists('donjon_id', $_GET))
{
	$id = $_GET['donjon_id'];

	$requete = "SELECT x, y, x_donjon, y_donjon FROM donjon WHERE id = ".$id;
	
  if (isset($G_disallow_donjon) && $G_disallow_donjon == true) {
    $disallowed = true;
    if (isset($G_allow_donjon_for) && is_array($G_allow_donjon_for))
      foreach ($G_allow_donjon_for as $allowed)
        if ($allowed == $joueur->get_nom())
          $disallowed = false;
    if ($disallowed)
      security_block(URL_MANIPULATION);
  }
	$req = $db->query($requete);
	
	$row = $db->read_assoc($req);

	// Verification que les conditions sont reunies
	$unlock = verif_tp_donjon($row, $joueur);
	if ($unlock == false)
		security_block(URL_MANIPULATION);

	//sortie
	if(array_key_exists('type', $_GET))
	{
		if($joueur->get_x() == $row['x_donjon'] AND $joueur->get_y() == $row['y_donjon'])
		{
			$joueur->set_x($row['x']);
			$joueur->set_y($row['y']);
			$joueur->sauver();
		}
	}
	//Entrée
	else
	{
		if($joueur->get_x() == $row['x'] AND $joueur->get_y() == $row['y'])
		{
			$joueur->set_x($row['x_donjon']);
			$joueur->set_y($row['y_donjon']);
			$joueur->sauver();
		}
	}
}

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$joueur->check_perso();

$_SESSION['position'] = convert_in_pos($joueur->get_x(), $joueur->get_y());
?>


    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <div class="container">

          <div class="nav-collapse collapse">
            <ul class="nav">
             <li><a href="#" onclick="affichePopUp('diplomatie.php');">Diplomatie</a></li>
			 <li><a href="#" onclick="affichePopUp('classement.php');">Classement</a></li>
			 
			 <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Autres<b class="caret"></b></a>
                <ul class="dropdown-menu">
					<li  onclick="affichePopUp('stats2.php?graph=carte_royaume');"><a href="#">Statistiques</li>
		<li  onclick="affichePopUp('message_accueil.php?affiche=all');"><a href="#">Message d'Accueil</li>
		<li  onclick="affichePopUp('liste_monstre.php');"><a href="#">Bestiaire</li>
		<li  onclick="affichePopUp('royaume.php');"><a href="#">Carte</li>
		<li ><a href="http://wiki.starshine-online.com/">Wiki</a></li>
                </ul>
              </li>
			 
			 <li class="dropdown " style='float:right;'>
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><? echo ucwords($joueur->get_nom()); ?><b class="caret"></b></a>
                <ul class="dropdown-menu">
				  <li onclick="affichePopUp('option.php');"><a href="#">Options</a></li>
				  <li onclick='showSoundPanel()'><a href="#">Son</a></li>
				  <li ><a href="http://bug.starshine-online.com/">Signaler un bug</a></li>
                  <li class="divider"></li>

	  			<li ><a href="#" onclick="if(confirm('Voulez vous déconnecter ?')) { document.location.href='index.php?deco=ok'; };">Deconnecter</a></li>
                </ul>
              </li>
              <li  style='float:right;'><a href="http://forum.starshine-online.com">Forum</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>



<div id="conteneur_back">
<div id="conteneur">

<div id="mask" style='display:none;'></div>
<div id="popup" style='display:none;'>
	<div id="popup_menu"><span class='fermer' title='Fermer le popup' onclick="fermePopUp(); return false;">&nbsp;</span></div>
	<div id="popup_marge">
		<div id="popup_content"></div>
	</div>
</div>
<div id="loading" style='display:none'></div>
<div id="loading_information" style='display:none'></div>
	<div id="perso">
		
		<?php
		require_once('infoperso.php');
		?>
		
	</div>

		<?php/*
$arene = $joueur->in_arene();
$time = time();
if ($arene) $time += $arene->decal;
echo '<div id="menu_date"><img src="image/interface/'.moment_jour().
  '.png" alt="'.moment_jour().'" title="'.moment_jour().' - '.date_sso($time).
  '" />'.moment_jour();?>

	<div id='menu_deco'>
	<?php
    if( (array_key_exists('nbr_perso', $_SESSION) && $_SESSION['nbr_perso'] > 1) OR (array_key_exists('droits', $_SESSION) && ($_SESSION['droits'] & joueur::droit_pnj)) )
    {
  ?>
		<span class="changer" title='Changer de personnage' onclick="affichePopUp('changer_perso.php');">&nbsp;</span>
	<?php
    }
    		<span class="fermer" title='Se déconnecter' onclick="if(confirm('Voulez vous déconnecter ?')) { document.location.href='index.php?deco=ok'; };">&nbsp;</span><span class="show_debug_button" id="debug_log_button" title='Voir le debug' onclick="show_debug_log()"><img src="image/interface/debug.png" onclick="show_debug_log()"/></span>
	</div>*/

  ?>
</div>
<div id='contenu_back'>
	<div id="contenu_jeu">
		<div id="centre">
		<?php
		
		//Génération de la carte apparaissant au centre.
		//Si coordonées supérieur à 100 alors c'est un donjon
		if(is_donjon($joueur->get_x(), $joueur->get_y()))
		{
			include_once(root.'donjon.php');
		}
		else include_once(root.'map2.php');
		?>
		</div>
		<?php include_once(root.'menu_carte.php');?>
		<div id="information">
				<h2>Information</h2>
		<?php
		
		$case = convert_in_pos($joueur->get_x(), $joueur->get_y());
		if(array_key_exists('page_info', $_GET)) $page_info = $_GET['page_info']; else $page_info = 'informationcase.php';
		{//-- Javascript
			echo "<script type='text/javascript'>
					// <![CDATA[\n";
			{//-- envoiInfo
				echo "envoiInfo('".$page_info."?case=".$case."', 'information');";
			}
			echo "	// ]]>
				  </script>";
		}

		echo "</div>
	</div>
	
</div>
</div>";

// Les logs de debug ajax
echo '<div id="debug_log" class="debug"></div>';

echo '<div id="ambiance_sound"><div id="ambiance_sound_container"></div>';
if ($joueur->get_option('no_sound')) {
	echo 'Le son est desactivé dans les options';
}
else {
echo '<a href="javascript:stopAmbiance()">Stop</a></div>';
} // !if ($joueur->get_option('no_sound'))
print_js_onload("$('#ambiance_sound').dialog({ autoOpen: false });");

//Inclusion du bas de la page
include_once(root.'bas.php');
?>
