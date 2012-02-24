<?php
$this->Html->addCrumb (__('Field Editor', true));
$this->Html->addCrumb ("{$field['Facility']['name']} ({$field['Facility']['code']}) {$field['Field']['num']}");
?>

<?php
$map_vars = array('id', 'num', 'latitude', 'longitude', 'angle', 'width', 'length', 'zoom');

$zuluru_base = Configure::read('urls.zuluru_base');
$gmaps_key = Configure::read('site.gmaps_key');
$address = "{$field['Facility']['location_street']}, {$field['Facility']['location_city']}";
$full_address = "{$field['Facility']['location_street']}, {$field['Facility']['location_city']}, {$field['Facility']['location_province']}";

// We use these as last-ditch emergency values, if the field has neither
// a valid lat/long or an address that Google can find.
$leaguelat = Configure::read('organization.latitude');
$leaguelng = Configure::read('organization.longitude');

// Build the list of variables to set for the JS.
// The blank line before END_OF_VARIABLES is required.
$variables = <<<END_OF_VARIABLES
zuluru_path = "$zuluru_base/";
leaguelat = $leaguelat;
leaguelng = $leaguelng;
name = "{$field['Facility']['name']}";
address = "$address";
full_address = "$full_address";

END_OF_VARIABLES;

echo $this->Form->create('Field', array('url' => Router::normalize($this->here), 'name' => 'layout'));

$vals = array();
foreach ($map_vars as $var) {
	$val = $field['Field'][$var];
	if (!empty ($val)) {
		if (!is_numeric($val)) {
			$val = "\"$val\"";
		}
		$vals[] = "'$var': $val";
	}
	echo $this->Form->hidden("{$field['Field']['id']}.$var", array('value' => $field['Field'][$var]));
}
$variables .= "fields[{$field['Field']['id']}] = { " . implode(', ', $vals) . " };\n";

// Handle other fields at this site
foreach ($field['Facility']['Field'] as $related) {
	$vals = array();
	foreach ($map_vars as $var) {
		$val = $related[$var];
		if (!empty ($val)) {
			if (!is_numeric($val)) {
				$val = "\"$val\"";
			}
			$vals[] = "'$var': $val";
		}
		echo $this->Form->hidden("{$related['id']}.$var", array('value' => $related[$var]));
	}
	$variables .= "fields[{$related['id']}] = { " . implode(', ', $vals) . " };\n";
}

// Handle parking
if ($field['Facility']['parking']) {
	$parking = explode ('/', $field['Facility']['parking']);
	foreach ($parking as $i => $pt) {
		list($lat,$lng) = explode(',', $pt);
		$variables .= "parking[$i] = { 'position': new google.maps.LatLng($lat, $lng) };\n";
	}
}
echo $this->Form->hidden('Facility.id', array('value' => $field['Facility']['id']));
echo $this->Form->hidden('Facility.parking');

// TODO: Handle more than one sport in a site
$sport = array_shift(array_keys(Configure::read('options.sport')));
$this->ZuluruHtml->script (array(
		"http://maps.googleapis.com/maps/api/js?key=$gmaps_key&libraries=geometry&sensor=false",
		'map_common.js',
		'map_edit.js',
		"sport_$sport.js",
), false);
$this->Html->scriptBlock ($variables, array('inline' => false));
?>

<h3><?php echo $field['Facility']['name']; ?></h3>
<p><?php echo $address; ?></p>
<h4 id="show_num"></h4>

<?php echo $this->element("maps/edit/$sport"); ?>

<p>
<input type="submit" onclick="return addParking()" value="Add Parking">
</p>

<?php
echo $this->Form->end(array(
		'label' => __('Save Changes', true),
		'onclick' => 'return check();',
));
?>

<?php
$this->Js->buffer("initializeEdit({$field['Field']['id']});");
?>
