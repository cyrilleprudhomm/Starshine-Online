<?php
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT royaume, type FROM map WHERE ID =\''.sSQL($joueur->get_pos()).'\'';
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());

if ($joueur->get_race() != $R->get_race() &&
		$R->get_diplo($joueur->get_race()) > 6)
{
	echo "<h5>Impossible de commercer avec un tel niveau de diplomatie</h5>";
	exit (0);
}

$max_ecurie = 10;

?>
<fieldset>
   	<legend><?php echo '<a href="ville.php" onclick="return envoiInfo(this.href, \'centre\')">';?><?php echo $R->get_nom();?></a> > <?php echo '<a href="ecurie.php" onclick="return envoiInfo(this.href, \'carte\')">';?> Ecurie </a></legend>
		<?php include_once(root.'ville_bas.php');?>
<?php
if($W_row['type'] == 1 && $R->get_diplo($joueur->get_race()) == 127)
{
	//Le joueur dépose une créature dans l'écurie
	if(array_key_exists('d', $_GET))
	{
		$joueur->pet_to_ecurie($_GET['d'], 1);
	}
	//Le joueur reprend une créature de l'écurie
	if(array_key_exists('r', $_GET))
	{
		$joueur->pet_from_ecurie($_GET['r']);
	}
	//Soin de la créature
	if(array_key_exists('s', $_GET))
	{
		$pet = new pet($_GET['s']);
		if($pet->get_id_joueur() == $joueur->get_id())
		{
			if($pet->get_hp() > 0)
			{
				if($joueur->get_star() >= $pet->get_cout_soin())
				{
					$pet->get_monstre();
					$pet->set_hp(ceil($pet->get_hp() + 0.1 * $pet->monstre->get_hp()));
					if($pet->get_hp() > $pet->monstre->get_hp()) $pet->set_hp($pet->monstre->get_hp());
					$pet->set_mp(ceil($pet->get_mp() + 0.1 * $pet->get_mp_max()));
					if($pet->get_mp() > $pet->get_mp_max()) $pet->set_mp($pet->get_mp_max());
					$pet->sauver();
					$joueur->set_star($joueur->get_star() - $pet->get_cout_soin());
					$joueur->sauver();
				}
				else
				{
					echo '<h5>Vous n\'avez pas assez de stars !</h5>';
				}
			}
			else
			{
				echo '<h5>Cette créature peut uniquement être réssucitée.</h5>';
			}
		}
		else
		{
			echo '<h5>Cette créature ne vous appartient pas !</h5>';
		}
	}
	//Rez de la créature
	if(array_key_exists('v', $_GET))
	{
		$pet = new pet($_GET['v']);
		if($pet->get_id_joueur() == $joueur->get_id())
		{
			if($joueur->get_star() >= $pet->get_cout_rez())
			{
				$pet->get_monstre();
				$pet->set_hp($pet->monstre->get_hp());
				$pet->set_mp($pet->get_mp_max());
				$pet->sauver();
				$joueur->set_star($joueur->get_star() - $pet->get_cout_rez());
				$joueur->sauver();
			}
			else
			{
				echo '<h5>Vous n\'avez pas assez de stars !</h5>';
			}
		}
		else
		{
			echo '<h5>Cette créature ne vous appartient pas !</h5>';
		}
	}
	$joueur->get_pets(true);
	$joueur->get_ecurie(true);
	?>
	<h3>Créatures en ville (<?php echo $joueur->nb_pet_ecurie(); ?> / <?php echo $max_ecurie; ?>)</h3>
	<ul>
	<?php
	foreach($joueur->ecurie as $pet)
	{
		$pet->get_monstre();
		?>
		<li>
			<?php echo $pet->get_nom(); ?> - <?php echo $pet->monstre->get_nom(); ?> -- HP : <?php echo $pet->get_hp(); ?> / <?php echo $pet->monstre->get_hp(); ?> <a href=""><img src="image/sort/sort_soins1.png" alt="Soigner" title="Soigner" style="width : 16px; height : 16px; vertical-align : top;" /> <span class="small">(<?php echo $pet->get_cout_soin(); ?> stars)</span></a> <a href="ecurie.php?r=<?php echo $pet->get_id(); ?>" onclick="return envoiInfo(this.href, 'carte');"><img src="image/icone/reprendre.png" alt="Reprendre" title="Reprendre" style="width : 16px; height : 16px; vertical-align : top;" /></a>
		</li>
		<?php
	}
	?>
	</ul>
	<h3>Créatures sur vous (<?php echo $joueur->nb_pet(); ?> / <?php echo $joueur->get_comp('max_pet'); ?>)</h3>
	<ul>
	<?php
	foreach($joueur->pets as $pet)
	{
		$pet->get_monstre();
		if($pet->get_hp() <= 0)
		{
			$link = 'v';
			$texte = '<img src="image/buff/rez.jpg" alt="Ressuciter" title="Ressuciter" style="width : 16px; height : 16px; vertical-align : top;" /> <span class="small">('.$pet->get_cout_rez().' stars)</span>';
		}
		else
		{
			$link = 's';
			$texte = '<img src="image/sort/sort_soins1.png" alt="Soigner" title="Soigner" style="width : 16px; height : 16px; vertical-align : top;" /> <span class="small">('.$pet->get_cout_soin().' stars)</span>';
		}
		?>
		<li>
			<?php echo $pet->get_nom(); ?> - <?php echo $pet->monstre->get_nom(); ?> -- HP : <?php echo $pet->get_hp(); ?> / <?php echo $pet->monstre->get_hp(); ?> <a href="ecurie.php?<?php echo $link; ?>=<?php echo $pet->get_id(); ?>" onclick="return envoiInfo(this.href, 'carte');"><?php echo $texte; ?></a> <a href="ecurie.php?d=<?php echo $pet->get_id(); ?>" onclick="return envoiInfo(this.href, 'carte');"><img src="image/icone/deposer.png" alt="Déposer" title="Déposer" style="width : 16px; height : 16px; vertical-align : top;" /> <span class="small">(<?php echo $pet->get_cout_depot(); ?> stars)</span></a>
		</li>
		<?php
	}
	?>
	</ul>
	<?php
	refresh_perso();
}
?>
</fieldset>