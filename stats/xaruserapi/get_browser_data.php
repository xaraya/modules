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

	if (empty($brdata)) {
        $msg = xarML('No browser data available');
        xarExceptionSet(XAR_USER_EXCEPTION, 'NO_BROWSER_DATA',
            new DefaultUserException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    } 	
	foreach($brdata as $browser){
		switch ($browser['agent']) {
			case 'Microsoft Internet Explorer': //TODO: is this really only on MAC??
                $brpic = 'ie5mac.png';
                $brname = xarML('Microsoft Internet Explorer');
                break;
			case 'Msie':
				$brpic = 'msie.png';
                $brname = xarML('Microsoft Internet Explorer');
                break;
            case 'Mozilla':
				$brpic  = 'mozilla.png';
                $brname = xarML('Mozilla');
                break;
			case 'Opera':
				$brpic = 'opera.png';
                $brname = xarML('Opera');
                break;
			case 'ns':         //TODO: is this used like this?
			case 'Netscape':
			case 'Netscape6':
				$brpic = 'netscape7.png';
                $brname = xarML('Netscape');
                break;
            case 'Safari':
				$brpic = 'safari.png';
                $brname = xarML('Safari');
                break;
            case 'Chimera':
            case 'Camino':
				$brpic = 'camino.png';
                $brname = xarML('Camino');
                break;				
            case 'Galeon':
				$brpic = 'galeon.png';
                $brname = xarML('Galeon');
                break;
            case 'Phoenix':
            case 'Firefox':
                $brname = 'Mozilla Firefox';
                // fallthrough => no break;
            case 'Mozilla Firebird':
				$brpic = 'px.png';
                if (empty($brname)) {
                	$brname = xarML('Mozilla Firebird');
                }
                break;
			case 'Konqueror':
				$brpic = 'konqueror.png';
                $brname = xarML('Konqueror');
                break;
            default:
				$brname = xarML('Unknown');
				$brpic  = 'question.gif';
		}
        if(!$top10) $brname .= " $browser[agver]";
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
