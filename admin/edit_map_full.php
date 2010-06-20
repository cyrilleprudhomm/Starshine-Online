<?php // -*- mode: php -*-

if (file_exists('../root.php'))
  include_once('../root.php');
$admin = true;
$textures = false;
include_once(root.'inc/fp.php');

define('MAX_X_NON_DONJON', 190);
define('MAX_Y_NON_DONJON', 190);

if (array_key_exists('change', $_GET)) {
	$first = true;
	$req = "insert into map (x, y, type, decor, info) values \n";
	foreach ($_POST['changes'] as $c) {
		if ($first) $first = false;
		else $req .= ",\n";
		$x = acase::get_x($c['case']);
		$y = acase::get_y($c['case']);
		if (array_key_exists('type', $c)) {
			$type = $c['type'];
		} else {
			$type = 0;
			if ($x > MAX_X_NON_DONJON || $y > MAX_Y_NON_DONJON)
				$type = 2;
		}
		$decor = $c['decor'];
		$info = floor($decor / 100);
		$req .= "($x, $y, $type, $decor, $info)";
	}
	$req .= "\nON DUPLICATE KEY UPDATE type = VALUES(type), decor = VALUES(decor), info = VALUES(info)";
	$res = $db->query($req);
	if ($_POST['showQ'] == 'true')
		$_SESSION['last_query'] = "$req\n".print_r($db->get_mysql_info(), true);
	else
		$_SESSION['last_query'] = null;
	echo '<script type="text/javascript">location.reload();</script>';
	exit(0);
}

class acase {
	var $type = 0;
	var $decor = 0;
	var $info = 0;
	var $id;

	function __construct($x, $y) {
		$this->id = $y * 1000 + $x;
	}

	static function get_x($id) {
		return $id % 1000;
	}

	static function get_y($id) {
		return floor($id / 1000);
	}

	function set_row($row) {
		$this->type = $row['type'];
		$this->decor = $row['decor'];
		$this->info = $row['info'];
	}

	function prnt() {
		echo '<td style="border: 0px; width: 60px; heigh: 60px;" id="case'.
			$this->id.'" onClick="clickTexture('.$this->id.')" ';
		if ($this->decor != 0) {
			echo 'class="decor tex'.$this->decor.'"';
		} else {
			echo 'class="decor texblack"';
		}
		echo '>';
		echo '<span class="casetype">'.$this->type.'</span>';
		echo '</td>';
	}
}

add_css_to_head('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
add_raw_css_to_head('.casetype { font-size: 0.5em; color: black; background-color: yellow; display: none; } ');

include_once(root.'admin/admin_haut.php');

setlocale(LC_ALL, 'fr_FR');
include_once(root.'haut_site.php');

global $xmin;
global $xmax;
global $ymin;
global $ymax;

$xmin = 1;
$ymin = 1;
if (array_key_exists('xmin', $_REQUEST)) $xmin = $_REQUEST['xmin'];
if (array_key_exists('ymin', $_REQUEST)) $ymin = $_REQUEST['ymin'];

$xmax = $xmin + 29;
$ymax = $ymin + 29;
if (array_key_exists('xmax', $_REQUEST)) $xmax = $_REQUEST['xmax'];
if (array_key_exists('ymax', $_REQUEST)) $ymax = $_REQUEST['ymax'];

if ($xmin < 1) {
	$dec = 1 - $xmin;
	$xmin += $dec;
	$xmax += $dec;
}
if ($ymin < 1) {
	$dec = 1 - $ymin;
	$ymin += $dec;
	$ymax += $dec;
}

$cases = array();
for ($x = $xmin; $x <= $xmax; $x++) {
	$cases[$x] = array();
	for ($y = $ymin; $y <= $ymax; $y++) {
		$cases[$x][$y] = new acase($x, $y);
	}
}

$requete = "SELECT x, y, decor, type, info FROM map WHERE x >= $xmin and x <= $xmax and y >= $ymin and y <= $ymax";
$req = $db->query($requete);

while ($row = $db->read_assoc($req)) {
	$cases[$row['x']][$row['y']]->set_row($row);
}

echo '<a href="index.php">Retour</a>';

$size = 'min-width: '.(($xmax - $xmin) * 60 + 45).'px; min-height: '.
(($ymax - $ymin) * 60 + 20).'px;';
echo '<table cellpadding="0" cellspacing="0" style="'.$size.'"><tr><th></th>';
for ($x = $xmin; $x <= $xmax; $x++) {
	echo "<th>$x</th>";
}
echo '</tr>';
for ($y = $ymin; $y <= $ymax; $y++) {
	echo "\n<tr><th>$y</th>";
	for ($x = $xmin; $x <= $xmax; $x++) {
		$cases[$x][$y]->prnt();
	}
	echo '</tr>';
}
echo '</table>';

function decal($sens, $sens2 = '') {
	global $xmin;
	global $xmax;
	global $ymin;
	global $ymax;
	$nxmin = $xmin;
	$nxmax = $xmax;
	$nymin = $ymin;
	$nymax = $ymax;
	switch ($sens) {
	case 'haut':
		$nymin -= 4;
		$nymax -= 4;
		break;
	case 'bas':
		$nymin += 4;
		$nymax += 4;
		break;
	case 'droite':
		$nxmin += 4;
		$nxmax += 4;
		break;
	case 'gauche':
		$nxmin -= 4;
		$nxmax -= 4;
		break;
	}
	switch ($sens2) {
	case 'haut':
		$nymin -= 4;
		$nymax -= 4;
		break;
	case 'bas':
		$nymin += 4;
		$nymax += 4;
		break;
	case 'droite':
		$nxmin += 4;
		$nxmax += 4;
		break;
	case 'gauche':
		$nxmin -= 4;
		$nxmax -= 4;
		break;
	}
	echo "?xmin=${nxmin}&amp;ymin=${nymin}&amp;xmax=${nxmax}&amp;ymax=${nymax}";
}

if (isset($_SESSION['last_query']) && $_SESSION['last_query'] != null) {
	echo '<div id="lastq" title="Last query"><pre>'.
		$_SESSION['last_query'].'</pre></div>';
	$add_js_start = '$("#lastq").dialog({ position: [\'left\',\'top\'] });';
	$_SESSION['last_query'] = null;
}

?>

<div id='rosedesvents'>
  <a id='rose_div_hg' href="<?php decal('haut', 'gauche'); ?>"></a>
  <a id='rose_div_h' href="<?php decal('haut'); ?>"></a>
  <a id='rose_div_hd' href="<?php decal('haut', 'droite'); ?>"></a>
  <a id='rose_div_cg' href="<?php decal('gauche'); ?>"></a>
  <a id='rose_div_c' href="<?php decal(''); ?>"></a>
  <a id='rose_div_cd' href="<?php decal('droite'); ?>"></a>
  <a id='rose_div_bg' href="<?php decal('bas', 'gauche'); ?>"></a>
  <a id='rose_div_b' href="<?php decal('bas'); ?>"></a>
  <a id='rose_div_bd' href="<?php decal('bas', 'droite'); ?>"></a>
</div>


<div class="selecteur" id="selecteur" title="Palette">
  <table>
    <tr>
			<td class="decor" id="texturePreview" style="border: 0px; width: 60px; heigh: 60px;"></td>
			<td><input type="submit" value="ok" onClick="javascript:doPost()" /></td>
      <td><input type="checkbox" id="doShowQ" value="off" />Show Query</td>
		</tr>
	</table>
	<select size="10" class="baseJumpbox" id="selectText" onChange="changeTexture()" style="max-height: 500px">
<?php
include_once('terrain.inc.html');
include_once('donjon.inc.html');
 ?>
	</select>
  <div>Type:
    <select id="type_drop">
      <option value="-1">Auto</option>
      <option value="0">Normal</option>
      <option value="1">Capitale</option>
      <option value="2">Donjon</option>
      <option value="3">Point spécial</option>
    </select>
	  <input type="button" onClick="toggleTypeView()" value="Voir Type" />
  </div>
	<div>
	Starshine Editeur v2.2
	</div>
</div>

<script type="text/javascript">
  var curDec = 120;
  var curChanges = [];

	function doPost() {
		/* $('#theform').submit(); */
		var theShowQ = document.getElementById('doShowQ').checked;
		$.post("?change", { changes: curChanges, showQ: theShowQ }, function(data) { $('body').append(data); });
	}

  function changeTexture() {
		curDec = $("#selectText").val();
		$("#texturePreview").attr({class: "decor tex" + curDec});
	}


  function clickTexture(numeroCase) {
		var lType = $("#type_drop").val();
		if (lType == -1)
			curChanges.push({case: numeroCase, decor: curDec});
		else
			curChanges.push({case: numeroCase, decor: curDec, type: lType });
		$("#case" + numeroCase).attr({class: "decor tex" + curDec});
	}

  var typeView = 0;
  function toggleTypeView() {
		typeView = !typeView;
		if (typeView)
			$('.casetype').show();
		else
			$('.casetype').hide();
	}

	$(function() {
		$("#rosedesvents").addClass('ui-draggable');
		$("#selecteur").dialog({ position: ['right','top'] });
		<?php echo $add_js_start; ?>
	});
</script>

<?php
print_foot();