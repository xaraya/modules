<?php

function stats_userapi_get_browser_data($args)
{
	extract($args);
	unset($args);
	$args = compact('top10');
	
    // API function to get the hits by browsers
    list($brdata, $brsum, $brmax) = xarModAPIFunc('stats',
												  'user',
												  'getbybrowser',
												  $args);
	$browsers = array();
	foreach($brdata as $browser){
		if ($top10) {
            $brname = $browser['agent'];
		} else {
            $brname = $browser['agent'].' '.$browser['agver'];
		}
		switch ($browser['agent']) {
			case 'Microsoft Internet Explorer': //TODO: is this really only on MAC??
                $brpic = 'ie5mac.png';
				break;
			case 'Msie':
				$brpic = 'msie.png';
				break;
            case 'Mozilla':
				$brpic  = 'mozilla.png';
				break;
			case 'Opera':
				$brpic = 'opera.png';
				break;
			case 'ns':         //TODO: is this used like this?
			case 'Netscape':
			case 'Netscape6':
				$brpic = 'netscape7.png';
				break;
            case 'Safari':
				$brpic = 'safari.png';
				break;
            case 'Chimera':
            case 'Camino':
				$brpic = 'camino.png';
				break;				
            case 'Galeon':
				$brpic = 'galeon.png';
				break;
            case 'Phoenix':
            case 'Mozilla Firebird':
				$brpic = 'px.png';
				break;
			case 'Konqueror':
				$brpic = 'konqueror.png';
				break;
            default:
				//$brname = xarML('Unknown');
				$brpic  = 'question.gif';
		}
		$browsers[] = array('name' => $brname,
							'rel'  => sprintf('%01.2f',(100*$browser['hits']/$brsum)),
							'abs'  => $browser['hits'],
							'wid'  => round(($barlen*$browser['hits']/$brmax)),
							'pic'  => $brpic);
	}
	unset($brdata, $brsum, $brmax, $browser, $brname, $brpic);

	$data = compact('browsers');
	return $data;
}

?>