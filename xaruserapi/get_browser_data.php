<?php
/**
 * Event API functions of Stats module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Stats Module
 * @link http://xaraya.com/index.php/release/34.html
 * @author Frank Besler <frank@besler.net>
 */
function stats_userapi_get_browser_data($args)
{
    extract($args);

    // API function to get the hits by browsers
    list($brdata, $brsum, $brmax) = xarModAPIFunc('stats',
                                                  'user',
                                                  'getbybrowser',
                                                  $args);
    $browsers = array();
    if (empty($brdata)) {
        $data = compact('browsers');
        return $data;
    }

    foreach($brdata as $browser){
        $brname = '';
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
                $brpic = 'px.png';
                $brname = xarML('Phoenix');
            case 'Firebird':
            case 'Mozilla Firebird':
                $brpic = 'firebird.png';
                $brname = xarML('Mozilla Firebird');
                break;
            case 'Firefox':
            case 'Mozilla Firefox':
                $brpic  = 'firefox.png';
                $brname = xarML('Mozilla Firefox');
                break;
            case 'Konqueror':
                $brpic = 'konqueror.png';
                $brname = xarML('Konqueror');
                break;
            default:
                $brpic  = 'question.gif';
                $brname = xarML('Unknown');
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
