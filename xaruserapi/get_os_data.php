<?php
/**
 * Event API functions of Stats module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Stats Module
 * @link http://xaraya.com/index.php/release/34.html
 * @author Frank Besler <frank@besler.net>
 */
function stats_userapi_get_os_data($args)
{
    extract($args);

    // API function to get the hits by browsers
    list($osdata, $ossum, $osmax) = xarModAPIFunc('stats',
                                                  'user',
                                                  'getbyos',
                                                  $args);
    $os = array();
    if (empty($osdata)) {
        $data = compact('os');
        return $data;
    }

    foreach($osdata as $ositem) {
        $osname = $ositem['os'].' '.$ositem['osver'];
        switch ($ositem['os']) {
            case 'win':
                $ospic = 'win.png';
                // add translations for OS Versions
                switch(strtolower($ositem['osver'])) {
                    case 'xp':
                        $osname = xarML('Windows XP');
                        $ospic = 'winxp.png';
                        break;
                    case '2k':
                        $osname = xarML('Windows 2000');
                        break;
                    case 'me':
                        $osname = xarML('Windows ME');
                        break;
                    case '98':
                        $osname = xarML('Windows 98');
                        break;
                    case '95':
                        $osname = xarML('Windows 95');
                        break;
                    case 'nt5.2':
                        $osname = xarML('Windows 2003 Server');
                        break;
                    case 'nt4.0':
                        $osname = xarML('Windows NT 4');
                        break;
                    case 'nt':
                        $osname = xarML('Windows NT 3.x');
                        break;
                    case '31':
                        $osname = xarML('Windows 3.1');
                        break;
                    default:
                        $osname = xarML('Windows');
                        break;
                }
                break;

            case '*nix':
                switch ($ositem['osver']) {
                    case 'linux':
                        $osname = xarML('Linux');
                        $ospic = 'linux.gif';
                        break;
                    case 'freebsd':
                        $osname = xarML('FreeBSD');
                        $ospic = 'bsd.gif';
                        break;
                    case 'bsd':
                        $osname = xarML('*BSD');
                        $ospic = 'bsd.gif';
                        break;
                    case 'sun':
                        $osname = xarML('Solaris');
                        $ospic = 'solaris.png';
                        break;
                    default:
                        $osname = xarML($ositem['osver']);
                        $ospic = 'question.gif';
                }
                break;

            case 'mac':
                $ospic = 'mac.png';
                switch ($ositem['osver']) {
                    case 'osx':
                        $osname = xarML('Mac OSX');
                        $ospic = 'osx.png';
                        break;
                    case 'ppc':
                        $osname = xarML('Mac PowerPC');
                        break;
                    case '68k':
                        $osname = xarML('Mac 68k');
                        break;
                    default:
                        break;
                }
                break;

            default:
                $osname = xarML('Unknown');
                $ospic = 'question.gif';
                break;
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
