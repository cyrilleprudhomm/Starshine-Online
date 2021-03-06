<?php
if (file_exists('root.php'))
  include_once('root.php');

//Inclusion du haut du document html
include_once(root.'haut_ajax.php');

$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$joueur->get_x().' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());

if ($joueur->get_race() != $R->get_race())
{
	echo "<h5>Impossible d'accéder à un tribunal d'une autre race</h5>";
	exit (0);
}
?>
   	<h2 class="ville_titre"><?php echo '<a href="ville.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'centre\')">';?><?php echo $R->get_nom();?></a> - <?php echo '<a href="tribunal.php?poscase='.$W_case.'" onclick="return envoiInfo(this.href, \'carte\')">';?> Tribunal </a></h2>
		<?php include_once(root.'ville_bas.php');?>
<?php
//Affichage des quêtes
if($R->get_nom() != 'Neutre') $return = affiche_quetes('poste', $joueur);
if($return[1] > 0 AND !array_key_exists('fort', $_GET))
{
	echo '<div class="ville_test"><span class="texte_normal">';
	echo 'Voici quelques petits services que j\'ai à vous proposer :';
	echo $return[0];
	echo '</span></div><br />';
}
?>

	<div class="ville_test">
	<?php
if($W_row['type'] == 1)
{
	if(isset($_GET['action']))
	{
		switch ($_GET['action'])
		{
			//Vérification si le personnage existe
			case 'prime' :
				$perso = $_GET['id_criminel'];
				?>
				<form method="post" action="javascript:envoiInfoPost('tribunal.php?poscase=<?php echo $W_case; ?>&amp;action=prime2&amp;id_criminel=<?php echo $perso; ?>&amp;prime=' + document.getElementById('prime').value, 'carte');">
					Combien de stars voulez vous mettre sur sa tête ? :<br />
					<input type="text" name="prime" id="prime" size="30" /><br />
					<input type="submit" value="Valider" />
				</form>
				<?php
			break;
			//Envoi du message
			case 'prime2' :
				$criminel = sSQL($_GET['id_criminel']);
				$prime = sSQL($_GET['prime']);
				if($prime <= $joueur['star'])
				{
					if($prime > 0)
					{
						$amende = recup_amende($criminel);
						//On supprime les stars au joueur
						$requete = "UPDATE perso SET star = star - ".$prime." WHERE ID = ".$joueur->get_id();
						$db->query($requete);
						//On ajoute la prime dans la liste des primes
						$requete = "INSERT INTO prime_criminel VALUES('', ".$criminel.", ".$joueur->get_id().", ".$amende['id'].", ".$prime.")";
						$db->query($requete);
						//On totalise la prime avec les autres
						$requete = "UPDATE amende SET prime = prime + ".$prime." WHERE id = ".$amende['id'];
						$db->query($requete);
						echo '<h5>Vous avez bien mis une prime sur la tête du criminel !</h5>';
					}
					else
					{
						echo '<h5>Erreur de saisi des stars</h5>';
					}
				}
				else
				{
					echo '<h5>Vous n\'avez pas assez de stars.</h5>';
				}
			break;
		}
	}
	else
	{
		//Affichage des plus grands criminels
		?>
		Voici la liste des criminels de votre royaume :
		<table>
			<tr>
				<td>
					Nom
				</td>
				<td>
					Points de crime
				</td>
				<td>
					Prime
				</td>
				<td>
				</td>
			</tr>
		<?php
		$requete = "SELECT * FROM perso RIGHT JOIN amende ON amende.id_joueur = perso.id WHERE perso.amende > 0 AND amende.statut = 'criminel' AND race = '".$R->get_race()."'";
		$req = $db->query($requete);
		while($row = $db->read_assoc($req))
		{
			$perso = new perso($row['id_joueur']);
			?>
			<tr>
				<td>
					<?php echo $perso->get_nom(); ?>
				</td>
				<td>
					<?php echo $row['crime']; ?>
				</td>
				<td>
					<?php echo $row['prime']; ?>
				</td>
				<td>
					<a href="tribunal.php?poscase=<?php echo $W_case; ?>&amp;action=prime&amp;id_criminel=<?php echo $perso->get_id(); ?>" onclick="return envoiInfo(this.href, 'carte')">Mettre une prime sur sa tête</a>
				</td>
			</tr>
			<?php
		}
		?>
		</table>
		</div>
		<?php
	}
}
?>