<?php

function stats_userapi_get_os_data($args)
{
	extract($args);
	unset($args);
	$args = compact('top10');
	
    // API function to get the hits by browsers
    list($osdata, $ossum, $osmax) = xarModAPIFunc('stats',
												  'user',
												  'getbyos',
												  $args);
	$os = array();
	foreach($osdata as $ositem) {
        $osname = $ositem['os'].' '.$ositem['osver'];
		switch ($ositem['os']) {
			case 'win':
				$ospic = ($ositem['osver'] == 'xp')
					   ? 'winxp.png'
					   : 'win.png';
				break;
			case '*nix':
				switch ($ositem['osver']) {
					case 'linux':
						$osname = 'Linux';
						$ospic = 'linux.gif';
						break;
					default:
						$osname = xarML('Unknown');
						$ospic = 'question.gif';
				}
				break;
			case 'mac':
				switch ($ositem['osver']) {
					case 'osx':
						$ospic = 'osx.png';
						break;
					default:
						$ospic = 'mac.png';
						break;
				}
				break;
			default:
				$osname = xarML('Unknown');
				$ospic = 'question.gif';
		}
		$os[] = array('name' => $osname,
					  'rel'  => sprintf('%01.2f',(100*$ositem['hits']/$ossum)),
					  'abs'  => $ositem['hits'],
					  'wid'  => round(($barlen*$ositem['hits']/$osmax)),
					  'pic'  => $ospic);
	}
	unset($osdata, $ossum, $osmax, $ositem, $osname, $ospic);
	
	$data = compact('os');
	return $data;
}

?>