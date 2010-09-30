<?php
/**
 * Validation for the relative dropdown
 *
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Menu Tree Module
 * @link http://xaraya.com/index.php/release/eid/1162
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  
 */
function menutree_userapi_relvalidation($args) {

	$arr = xarMod::apiFunc('menutree','user','getitemlevels');

	foreach ($arr as $key => $value) {
		// start the key with the seq for sorting purposes, but add the padded itemid for uniqueness
		$sortkey = $value['seq'] . str_pad($value['itemid'], 25, "0", STR_PAD_LEFT);
		$prepend = '';
		for ($i=1; $i < $value['level']; $i++) {
			$prepend .= '&#8212;';
		}
		$items[$sortkey] = array($value['itemid'], $prepend . '&#160;' . $value['link']);
	}

	ksort($items);

	unset($arr);

	foreach ($items as $key => $value) {
		$arr[] = array('id' => $value[0], 'name' => $value[1]);
	}

	return $arr;

}
 

?>