<?php
if (file_exists('root.php'))
  include_once('root.php');
?><?php
$connexion = true;
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

echo "<fieldset>";

if($W_row['type'] == 1)
{
	//-- On verifie que le joueur est bien sur la ville ($W_distance)
	echo "<script type='text/javascript'>return nd();</script>";
	echo "<legend>
		   <a href=\"ville.php\" onclick=\"return envoiInfo(this.href, 'centre');\">".$R->get_nom()."</a> >
		   <a href=\"vente_terrain.php\" onclick=\"return envoiInfo(this.href, 'carte');\"> Vente de terrains </a>
		  </legend>";
	include_once(root."ville_bas.php");
	?>
	<div class="ville_test">
	<?php
	if(isset($_GET['action']))
	{
		switch ($_GET['action'])
		{
			case 'enchere' :
				$vente_terrain = new vente_terrain($_GET['id_vente_terrain']);
				$verif = $vente_terrain->verif_joueur($joueur);
				if($verif)
				{
					$vente_terrain->enchere($joueur->get_id());
				}
				else
				{
					switch($vente_terrain->erreur)
					{
						case 'star' :
							echo '<h5>Vous n\'avez pas assez de stars</h5>';
						break;
						case 'royaume' :
							echo '<h5>Vous ne faites pas parti de ce royaume</h5>';
						break;
						case 'terrain' :
							echo '<h5>Vous possédez déjà un terrain</h5>';
						break;
						case 'enchere' :
							echo '<h5>Vous avez déjà une enchère en cours</h5>';
						break;
					}
				}
			break;
		}
	}
	else
	{
		echo '<h3>Liste des terrains à vendre</h3>';
		$requete = "SELECT id, id_royaume, date_fin, id_joueur, prix FROM vente_terrain WHERE id_royaume = ".$R->get_id()." AND date_fin > ".time();
		$req = $db->query($requete);
		?>
		<table>
		<tr>
			<td>
			</td>
			<td>
				Id
			</td>
			<td>
				Prix
			</td>
			<td>
				Fin
			</td>
			<td>
				Enchérir
			</td>
		</tr>
		<?php
		while($row = $db->read_assoc($req))
		{
			$vente_terrain = new vente_terrain($row);
			if($vente_terrain->id_joueur == $joueur->get_id()) $check = '*';
			else $check = '';
			?>
			<tr>
				<td>
					<?php echo $check; ?>
				</td>
				<td>
					Terrain #<?php echo $vente_terrain->id; ?>
				</td>
				<td>
					<?php echo $vente_terrain->prix; ?>
				</td>
				<td>
					<?php echo date("d-m-Y", $vente_terrain->date_fin); ?>
				</td>
				<td>
					<a href="vente_terrain.php?action=enchere&amp;id_vente_terrain=<?php echo $vente_terrain->id; ?>" onclick="return envoiInfo(this.href, 'carte');">Enchérir <?php echo $vente_terrain->prochain_prix(); ?> stars</a><br />
				</td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
}
?>
</fieldset>