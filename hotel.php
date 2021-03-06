<?php // -*- mode: php; tab-width:2 -*-
if (file_exists('root.php'))
  include_once('root.php');

require_once("haut_ajax.php");
	
$joueur = new perso($_SESSION['ID']);
$joueur->check_perso();

//Vérifie si le perso est mort
verif_mort($joueur, 1);

$W_requete = 'SELECT royaume, type FROM map WHERE x = '.$joueur->get_x().' and y = '.$joueur->get_y();
$W_req = $db->query($W_requete);
$W_row = $db->read_assoc($W_req);
$R = new royaume($W_row['royaume']);
$R->get_diplo($joueur->get_race());
$mois = 60 * 60 * 24 * 31;

if ($R->is_raz() && $joueur->get_x() <= 190 && $joueur->get_y() <= 190)
{
	echo "<h5>Impossible de commercer dans une ville mise à sac</h5>";
	exit (0);
}

if ($joueur->get_race() != $R->get_race() &&
		$R->get_diplo($joueur->get_race()) > 6)
{
	echo "<h5>Impossible de commercer avec un tel niveau de diplomatie</h5>";
	exit (0);
}
echo "<fieldset>";
if($W_row['type'] == 1)
{//-- On verifie que le joueur est bien sur la ville ($W_distance)
	echo "<script type='text/javascript'>return nd();</script>";
	echo "<legend>
		   <a href=\"ville.php\" onclick=\"return envoiInfo(this.href, 'centre');\">".$R->get_nom()."</a> >
		   <a href=\"hotel.php\" onclick=\"return envoiInfo(this.href, 'carte');\"> Hotel des ventes </a>
		  </legend>";
	include_once(root."ville_bas.php");
	{//-- Traitement d'un achat ou d'une recupération
		if(isset($_GET["action"]))
		{
			$message = "";
			switch ($_GET["action"])
			{
				case "achat" : 	{//-- Achat d'un objet
									unset($objObjetHotel);
									unset($RqObjetHotel);
									$RqObjetHotel = $db->query("SELECT * FROM hotel WHERE id = ".sSQL($_GET["id_vente"]).";");
									$objObjetHotel = $db->read_object($RqObjetHotel);
									if ($joueur->get_star() >= $objObjetHotel->prix)
									{
										if($joueur->prend_objet($objObjetHotel->objet))
										{
											$joueur->set_star($joueur->get_star() - $objObjetHotel->prix);
											$joueur->sauver();
											$vendeur = new perso($_GET["id_vendeur"]);
											$vendeur->set_star($vendeur->get_star() + $objObjetHotel->prix);
											$vendeur->sauver();
											$db->query("DELETE FROM hotel WHERE id=".sSQL($_GET["id_vente"]).";");
											$action_message = "<span class='message_vert'>l&apos;".$_GET["type"]." a bien &eacute;t&eacute; acheté.</span>";
											
											$db->query("INSERT INTO journal VALUES(NULL, ".$_GET["id_vendeur"].", 'vend', '', '', NOW(), '".addslashes(nom_objet($objObjetHotel->objet))."', '".$objObjetHotel->prix."', 0, 0)");
											
											// Augmentation du compteur de l'achievement
											$achiev = $vendeur->get_compteur('objets_vendus');
											$achiev->set_compteur($achiev->get_compteur() + 1);
											$achiev->sauver();
										}
										else { $action_message = "<span class='message_rouge'>$G_erreur</span>"; };
									}
									else { $action_message = "<span class='message_rouge'>Vous n&apos;avez pas assez de stars</span>"; };
								}
								break;
				case "suppr" :	{//-- Récupération d'un objet
									$RqObjetHotel = $db->query("SELECT id_vendeur, objet FROM hotel WHERE id = ".sSQL($_GET["id_vente"]).";");
									$objObjetHotel = $db->read_object($RqObjetHotel);
									if($objObjetHotel->id_vendeur == $joueur->get_id())
									{//-- vérification que c'est bien l'objet du vendueur
										if($joueur->prend_objet($objObjetHotel->objet))
										{
											$db->query("DELETE FROM hotel WHERE id = ".sSQL($_GET["id_vente"]).";");
											
											$action_message = "<span class='message_vert'>Vous avez bien récupéré votre objet de l&apos;h&ocirc;tel des ventes.</span>";
											
											$db->query("INSERT INTO journal VALUES(NULL, ".$joueur->get_id().", 'recup', '', '', NOW(), '".sSQL(nom_objet($objObjetHotel->objet))."', 0, 0, 0)");
										}
										else { $action_message = "<span class='message_rouge'>$G_erreur</span>"; };
									}
									else { $action_message = "<span class='message_rouge'>Ce n&apos;est pas votre objet !</span>"; };
								}
								break;
			}
		}
	}
	echo "<div class='ville_message'>$action_message</div>";
	echo "<div class='ville_test'>";
	{//-- Catégories de ventes
		$url = "onclick=\"envoiInfo('hotel.php?type=";
		$urlfin = "', 'carte');\">";
		echo " <div class='ville_haut'>
				<ul id='hotel_liste_type'>
				 <li ".$url."arme".$urlfin."Armes|</li>
				 <li ".$url."armure".$urlfin."Armures|</li>
				 <li ".$url."dressage".$urlfin."Dressage|</li>
				 <li ".$url."accessoire".$urlfin."Access.|</li>
				 <li ".$url."objet".$urlfin."Objets|</li>
				 <li ".$url."gemme".$urlfin."Gemmes|</li>
				 <li ".$url."grimoire".$urlfin."Grimoire|</li>
				 <li ".$url."moi".$urlfin."Mes objets</li>
				</ul>
			   </div>";
	}
	{//-- Récupère tout les royaumes qui peuvent avoir des items en commun
		$RqRoyaumes = $db->query("SELECT * FROM diplomatie WHERE race='".sSQL($R->get_race())."';");
		if($db->num_rows($RqRoyaumes) > 0)
		{
			$objRoyaumes = $db->read_object($RqRoyaumes);
			foreach($objRoyaumes as $race => $diplomatie) 
			{ 
				if( (($diplomatie <= 5) || ($diplomatie == 127)) && ($diplomatie != 'race') )
				{
					$royaumes_sharing_tab[count($royaumes_sharing_tab)] = "'".$race."'"; 
				}
			}
		}
	}
	{//-- Filtre & Tri
		if(array_key_exists("type", $_GET)) { $type = $_GET["type"]; } else { $type = "arme"; };
		switch($type)
		{//-- Récupération du filtre
			case "arme" :		$abbr = "a";	break;
			case "armure" :		$abbr = "p";	break;
			case "dressage" :	$abbr = "d";	break;
			case "objet" :		$abbr = "o";	break;
			case "gemme" :		$abbr = "g";	break;
			case "accessoire" :	$abbr = "m";	break;
			case "grimoire" :	$abbr = "l";	break;
			default	:			$abbr = "a";		break;
		}
		{//-- Tri
			if(array_key_exists("tri_champ", $_GET))
			{//-- si le tri est envoyé en GET
				if(array_key_exists("hotel_des_ventes", $_SESSION) )
				{//-- si le tri existe en session et que le meme tri existe deja, alors on inverse le sens
					if($_SESSION["hotel_des_ventes"]["sens"] == "DESC") 	{ $sens = "ASC"; }
					else 											{ $sens = "DESC"; };
				}
				else { $sens = "ASC"; };
				$_SESSION["hotel_des_ventes"] = array("tri_champ" => $_GET["tri_champ"], "sens" => $sens);
			}
			if(array_key_exists("hotel_des_ventes", $_SESSION))
			{//-- On recompose le tri
				$tri_champ = "";
				$ordre["tri_champ"] = $_SESSION["hotel_des_ventes"]["tri_champ"];
				$ordre["sens"] = $_SESSION["hotel_des_ventes"]["sens"];
				
				$tri_champ .= $ordre["tri_champ"]." ".$ordre["sens"];
			}
			else { $tri_champ = "objet ASC, prix ASC"; }
		}
		//-- Recherche tous les objets correspondants à ces races
		if($type == "moi")	{ $queryObjetsHotel = "SELECT * FROM hotel WHERE id_vendeur=".$joueur->get_id()." ORDER BY $tri_champ;"; }
		else				{ $queryObjetsHotel = "SELECT * FROM hotel WHERE race IN (".implode($royaumes_sharing_tab, ",").") AND SUBSTRING(objet FROM 1 FOR 1)='$abbr' AND time>".(time() - $mois)." ORDER BY $tri_champ;"; };
		$RqObjetsHotel = $db->query($queryObjetsHotel);
		if($db->num_rows($RqObjetsHotel) > 0)
		{
			$objet_id = array();
			$objets_tab = array();
			$objet_id_new = array();
			$objets_tab_new = array();
			$tri = array();
			
			unset($RqObjet); unset($RqObjet);	//-- Reinitialisation
			
			while($arrayObjetsHotel = $db->read_assoc($RqObjetsHotel))
			{
				unset($objet_info);
				$objet_info = decompose_objet($arrayObjetsHotel["objet"]);		//-- on décompose l'identification de l'objet
				
				//-- Recherche des infos des objets a afficher
				$RqObjet = $db->query("SELECT * FROM ".$objet_info["table_categorie"]." WHERE id=".$objet_info["id_objet"].";");
				if($db->num_rows($RqObjet) > 0)
				{
					$arrayObjet = $db->read_assoc($RqObjet);

					if(!in_array($arrayObjet["type"], $tri)) 	{ $tri[] = $arrayObjet["type"]; };
					if(empty($_GET["tri"]))						{ $all = true; } else { $all = false; };
					
					if((array_key_exists("tri", $_GET) AND $_GET["tri"] == $arrayObjet["type"]) OR $all)
					{
						$objets_tab_new[$arrayObjetsHotel["objet"]] = $arrayObjet;
						$objets_tab[$arrayObjetsHotel["id"]] = $arrayObjetsHotel;
						
						$objet_id[] = $objet_info["id_objet"];
						$objet_id_new[] = $arrayObjetsHotel["id"];
					}
				}
			}
			$class = 'trcolor1';
			foreach($objet_id_new as $id)
			{
				unset($objet_info);
				$objet_info = decompose_objet($objets_tab[$id]["objet"]);
				$objet = $objets_tab_new[$objet_info["sans_stack"]];
				//Modification du nom des grimoires
				if($objet_info['table_categorie'] == 'grimoire')
				{
					$objet['nom'] = str_replace('Traité de ', 'Grim.', $objet['nom']);
					$objet['nom'] = str_replace('Tome de ', 'Grim.', $objet['nom']);
				}
				if(strlen($objet["nom"]) > 26) { $tmp_nom = mb_substr($objet["nom"], 0, 26)."&hellip;"; } else { $tmp_nom = $objet["nom"]; };
				if($type == "moi")
				{//-- Suivant si c'est un objet que l'on a mis en vente
					$tmp_achat_click = "onclick=\"envoiInfo('hotel.php?action=suppr&amp;id_vente=".$objets_tab[$id]["id"]."&amp;poscase=".$_GET["poscase"]."', 'carte');\"";
					$tmp_achat = "R&eacute;cup&eacute;rer";
				}
				else
				{//-- ou pas
					$tmp_achat_click = "onclick=\"envoiInfo('hotel.php?action=achat&amp;type=".$type."&partie=".$objet["type"]."&amp;id=".$id_objet."&amp;id_vente=".$objets_tab[$id]["id"]."&amp;id_vendeur=".$objets_tab[$id]["id_vendeur"]."&amp;poscase=".$_GET["poscase"]."', 'carte');\"";
					$tmp_achat = "Achat";
				}
				if($objet_info["stack"] > 1) 	{ $tmp_stack = " X ".$objet_info["stack"]; } else { $tmp_stack = ""; };
				if($objet_info["slot"] > 0) 	
				{
					$tmp_slot = "<span class='slot' title='slot de niveau ".$objet_info["slot"]."'>".$objet_info["slot"]."</span>"; 
					$tmp_slot2 = "slot de niveau ".$objet_info["slot"]; 
				}
				elseif($objet_info["slot"] == "0") 	
				{
					$tmp_slot = "<span class='slot' title='slot impossible'>x</span>"; 
					$tmp_slot2 = "slot impossible"; 
				}
				else { $tmp_slot = ""; $tmp_slot2 = "";}
				if($objet_info["enchantement"] > "0")
				{
					$RqEnchantement = $db->query("SELECT * FROM gemme WHERE id=".$objet_info["enchantement"].";");
					$objEnchantement = $db->read_object($RqEnchantement);
					$tmp_enchantement = "<span class='enchantement' title='Enchantement de ".$objEnchantement->enchantement_nom."'>E</span>";
					$tmp_enchantement2 = "Enchantement de ".$objEnchantement->enchantement_nom;
				}
				else { $tmp_enchantement = ""; $tmp_enchantement2 = "";};
				
				{//-- OVERLIB-
					$tmp_overlib = "";
					
					switch($objet_info['table_categorie'])
					{
						case "arme" :		$RqArme = $db->query("SELECT * FROM arme WHERE id=".sSQL($objet_info["id_objet"]).";");
											$objArme = $db->read_object($RqArme);
											$cote_arme = split(";", $objArme->mains);
											$tmp_overlib .= "<ul><li class='overlib_titres'>Arme &agrave; vendre</li>";
											$tmp_overlib .= "<li class='overlib_img_objet' style='background-image:url(image/arme/arme".$objArme->id.".png);'></li>";
											$tmp_overlib .= "<li class='overlib_nom_objet'>".$objArme->nom."$tmp_stack</li>";
											$tmp_overlib .= "<li class='overlib_desc_objet'><span>Type : </span>".$objArme->type."</li>";
											$tmp_overlib .= "<li class='overlib_desc_objet'><span>Degat : </span>".$objArme->degat."</li>";
											$tmp_overlib .= "<li class='overlib_desc_objet'><span>Prix (HT) : </span>".$objArme->prix."</li>";
											$tmp_overlib .= "<li class='overlib_desc_objet'><span>Force : </span>".$objArme->forcex."</li>";
											$tmp_overlib .= "<li class='overlib_desc_objet'><span>Melee : </span>".$objArme->melee."</li>";
											$tmp_overlib .= "<li class='overlib_desc_objet'><span>Distance : </span>".$objArme->distance."</li>";
											$tmp_overlib .= "<li class='overlib_desc_objet'><span>Portée : </span>".$objArme->distance_tir."</li>";
											if(!empty($tmp_slot2)) { $tmp_overlib .= "<li class='overlib_infos'>$tmp_slot2</li>"; }
											if(!empty($tmp_enchantement2)) { $tmp_overlib .= "<li class='overlib_infos'>$tmp_enchantement2</li>"; }
											$tmp_overlib .= "</ul>";
											
											if(in_array("main_droite", $cote_arme) && ($joueur->inventaire()->main_droite != "lock") && ($joueur->inventaire()->main_droite != ""))
											{//-- si elle peut etre porté a droite
												$main_droite = decompose_objet($joueur->inventaire()->main_droite);
												$RqArmeDroite = $db->query("SELECT * FROM `arme` WHERE id=".$main_droite["id_objet"].";");
												$objArmeDroite = $db->read_object($RqArmeDroite);
												$tmp_overlib .= "<ul style='border-top:1px dotted black; margin:5px 0px;'><li class='overlib_titres'>Arme droite &eacute;quip&eacute;e</li>";
												$tmp_overlib .= "<li class='overlib_img_objet' style='background-image:url(image/arme/arme".$objArmeDroite->id.".png);'></li>";
												$tmp_overlib .= "<li class='overlib_nom_objet'>".$objArmeDroite->nom."</li>";
												$tmp_overlib .= "<li class='overlib_desc_objet'><span>Type : </span>".$objArmeDroite->type."</li>";
												$tmp_overlib .= "<li class='overlib_desc_objet'><span>Degat : </span>".$objArmeDroite->degat."</li>";
												$tmp_overlib .= "<li class='overlib_desc_objet'><span>Force : </span>".$objArmeDroite->forcex."</li>";
												$tmp_overlib .= "<li class='overlib_desc_objet'><span>Melee : </span>".$objArmeDroite->melee."</li>";
												$tmp_overlib .= "<li class='overlib_desc_objet'><span>Distance : </span>".$objArmeDroite->distance."</li>";
												$tmp_overlib .= "<li class='overlib_desc_objet'><span>Portée : </span>".$objArmeDroite->distance_tir."</li>";
												$tmp_overlib .= "</ul>";
											}
											if(in_array("main_gauche", $cote_arme) && ($joueur->inventaire()->main_gauche != "lock") && ($joueur->inventaire()->main_gauche != "") )
											{//-- si elle peut etre porté a gauche
												$main_gauche = decompose_objet($joueur->inventaire()->main_gauche);
												$RqArmeGauche = $db->query("SELECT * FROM `arme` WHERE id=".$main_gauche["id_objet"].";");
												$objArmeGauche = $db->read_object($RqArmeGauche);
												$tmp_overlib .= "<ul style='border-top:1px dotted black; margin:5px 0px;'><li class='overlib_titres'>Arme gauche &eacute;quip&eacute;e</li>";
												$tmp_overlib .= "<li class='overlib_img_objet' style='background-image:url(image/arme/arme".$objArmeGauche->id.".png);'></li>";
												$tmp_overlib .= "<li class='overlib_nom_objet'>".$objArmeGauche->nom."</li>";
												$tmp_overlib .= "<li class='overlib_desc_objet'><span>Type : </span>".$objArmeGauche->type."</li>";
												$tmp_overlib .= "<li class='overlib_desc_objet'><span>Degat : </span>".$objArmeGauche->degat."</li>";
												$tmp_overlib .= "<li class='overlib_desc_objet'><span>Force : </span>".$objArmeGauche->forcex."</li>";
												$tmp_overlib .= "<li class='overlib_desc_objet'><span>melee : </span>".$objArmeGauche->melee."</li>";
												$tmp_overlib .= "<li class='overlib_desc_objet'><span>Distance : </span>".$objArmeGauche->distance."</li>";
												$tmp_overlib .= "<li class='overlib_desc_objet'><span>Portée : </span>".$objArmeGauche->distance_tir."</li>";
												$tmp_overlib .= "</ul>";
											}
											break;
											
						case "armure" :		$RqArmure = $db->query("SELECT * FROM `armure` WHERE id=".$objet_info["id_objet"].";");
											$objArmure = $db->read_object($RqArmure);
											$tmp_type = $objArmure->type;
											$tmp_overlib .= "<ul><li class='overlib_titres'>".ucfirst($objArmure->type)." &agrave; vendre</li>";
											$tmp_overlib .= "<li class='overlib_img_objet' style='background-image:url(image/armure/".$objArmure->type."/".$objArmure->type.$objArmure->id.".png);'></li>";
											$tmp_overlib .= "<li class='overlib_nom_objet'>".$objArmure->nom."$tmp_stack</li>";
											$tmp_overlib .= "<li class='overlib_desc_objet'><span>PP : </span>".$objArmure->PP."</li>";
											$tmp_overlib .= "<li class='overlib_desc_objet'><span>PM : </span>".$objArmure->PM."</li>";
											$tmp_overlib .= "<li class='overlib_desc_objet'><span>Prix (HT) : </span>".$objArmure->prix."</li>";
											$tmp_overlib .= "<li class='overlib_desc_objet'><span>Force : </span>".$objArmure->forcex."</li>";
											if(!empty($tmp_slot2)) { $tmp_overlib .= "<li class='overlib_infos'>$tmp_slot2</li>"; }
											if(!empty($tmp_enchantement2)) { $tmp_overlib .= "<li class='overlib_infos'>$tmp_enchantement2</li>"; }
											$tmp_overlib .= "</ul>";
											
											
											if($joueur->inventaire()->$tmp_type != "")
											{
												$armure = decompose_objet($joueur->inventaire()->$tmp_type);
												$RqArmureEquipee = $db->query("SELECT * FROM `armure` WHERE id=".$armure["id_objet"].";");
												$objArmureEquipee = $db->read_object($RqArmureEquipee);
												$tmp_overlib .= "<ul style='border-top:1px dotted black; margin:5px 0px;'><li class='overlib_titres'>".ucfirst($objArmureEquipee->type)." &eacute;quip&eacute;</li>";
												$tmp_overlib .= "<li class='overlib_img_objet' style='background-image:url(image/armure/".$objArmureEquipee->type."/".$objArmureEquipee->type.$objArmureEquipee->id.".png);'></li>";
												$tmp_overlib .= "<li class='overlib_nom_objet'>".$objArmureEquipee->nom."</li>";
												$tmp_overlib .= "<li class='overlib_desc_objet'><span>PP : </span>".$objArmureEquipee->PP."</li>";
												$tmp_overlib .= "<li class='overlib_desc_objet'><span>PM : </span>".$objArmureEquipee->PM."</li>";
												$tmp_overlib .= "<li class='overlib_desc_objet'><span>Force : </span>".$objArmureEquipee->forcex."</li>";
												$tmp_overlib .= "</ul>";
											}
											break;
											
						case "gemme" :
						case "objet" :		$tmp_overlib .= "<ul><li>$tmp_stack ".description($objets_tab_new[$objet_info["id"]]["description"], $objets_tab_new[$objet_info["id"]])."</li></ul>";
											break;
						case "grimoire" :		$tmp_overlib .= "<ul><li>$tmp_stack ".description_objet($objet_info["id"])."</li></ul>";
											break;
					}
				}
				if(!empty($tmp_overlib)) { $tmp_overlib = str_replace("'", "\'", trim($tmp_overlib)); $overlib = "onmouseover=\"return overlib('$tmp_overlib', BGCLASS, 'overlib', BGCOLOR, '', FGCOLOR, '', VAUTO);\" onmouseout=\"return nd();\" "; };
				$objets_liste .= " <li $overlib class='$class'><span class='nom_hotel'>".$tmp_stack.$tmp_slot.$tmp_enchantement.$tmp_nom."</span>
								   <span class='time'>".transform_min_temp(($objets_tab[$id]["time"] + $mois) - time())."</span>
								   <span class='prix_hotel'>".number_format($objets_tab[$id]['prix'], 0, ".", " ")."</span>
								   <span class='achat' $tmp_achat_click>$tmp_achat</span></li>";
				if($class =='trcolor1') { $class = 'trcolor2'; } else { $class = 'trcolor1'; };
			}
			
			$url = "onclick=\"envoiInfo('hotel.php?type=$type&amp;tri_champ=";
			$urlfin = "', 'carte');\"";
			echo "<ul id='boutique'>
			       <li class='head'> <span class='nom_hotel' ".$url."objet".$urlfin.">Nom de l'objet"; 	if($ordre["tri_champ"] == "objet") { if($ordre["sens"] == "ASC"){ echo "<img src='./image/asc.png' style='margin-right:-13px;' alt='ASC' />"; } else { echo "<img src='./image/desc.png' style='margin-right:-13px;' alt='DESC' />"; }; }; echo "</span>
			       <span class='time' ".$url."time".$urlfin.">Tps restant"; 		if($ordre["tri_champ"] == "time") { if($ordre["sens"] == "ASC") { echo "<img src='./image/asc.png' style='margin-right:-13px;' alt='ASC' />"; } else { echo "<img src='./image/desc.png' style='margin-right:-13px;' alt='DESC' />"; }; }; echo "</span>
			       <span class='prix_hotel' ".$url."prix".$urlfin.">Prix"; 					if($ordre["tri_champ"] == "prix") { if($ordre["sens"] == "ASC") { echo "<img src='./image/asc.png' style='margin-right:-13px;' alt='ASC' />"; } else { echo "<img src='./image/desc.png' style='margin-right:-13px;' alt='DESC' />"; }; }; echo "</span>
			       
			       ".$objets_liste."
			      </ul>
			      <div class='spacer'></div>";
		}
		else
		{
			echo "<p style='text-align:center; font-style:italic;'>aucun objet dans cette cat&eacute;gorie&hellip;</p>
			      <div class='spacer'></div>";
		}
	}	
	echo "</div>";
}
echo "</fieldset>";

?>
