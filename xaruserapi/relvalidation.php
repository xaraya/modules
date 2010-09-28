<?php
/**
 
/**
 * 
 */
function menutree_userapi_relvalidation($args)
{

	$arr = xarMod::apiFunc('menutree','user','getoptions');

	function leadingZeros($num,$numDigits) {
		return sprintf("%0".$numDigits."d",$num);
	}

	foreach ($arr as $key => $value) {
		$the_key = $value['seq'] . str_pad($value['itemid'], 15, "0", STR_PAD_LEFT);
		$prepend = '';
		for ($i=1; $i < $value['level']; $i++) {
			$prepend .= '&#8212;';
		}
		//$prepend .= '+';
		$items[$the_key] = array($prepend . '&#160;' . $value['link'], $key, $value['itemid']);
	}

	ksort($items);

	unset($arr);

	foreach ($items as $key => $value) {
		$the_key = (int)$value[2]; 
		$arr[] = array('id' => $value[2], 'name' => $value[0]);
	}

	return $arr;

}
 

?>