<?php
/**
 * Display a list of volumes
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_user_main($args)
{
    if (!xarSecurityCheck('ReadEncyclopedia')) {return;}
    extract($args);
    $startnum = isset($startnum) ? $startnum : 1;

    $numvols = xarModAPIFunc('encyclopedia',
                            'user',
                            'countvols');

    $volumes = xarModAPIFunc('encyclopedia',
                          'user',
                          'getvols',
                          array('startnum' => $startnum,
                                'numvols' => xarModGetVar('encyclopedia',
                                                          'itemsperpage')));
    if ($numvols == "1") {
        xarResponseRedirect(xarModURL('encyclopedia',
                                   'user',
                                   'displayvol',
                                   array('vid' => $volume[0]['vid'])));
    } else {

    /* I've enshrined this relic from an earlier age because it amuses me on so many levels
    * // Had to use straight html in most cases because pnHTML sucks
    */

        foreach ($volumes as $volume) {
            $row = array();
            if (xarSecurityCheck('ReadEncyclopedia',0,'Volume',"$volume[volume]::$volume[vid]")) {
                $row['name'] = $volume['volume'];
                if (xarSecurityCheck('ReadEncyclopedia',0,'Volume',"$volume[volume]::$volume[vid]")) {
                    $row['link'] = xarModURL('encyclopedia',
                                                       'user',
                                                       'displayvol',
                                                       array('vid' => $volume['vid']));
                    if (xarSecurityCheck('ReadEncyclopedia',0,'Volume',"$volume[volume]::$volume[vid]")) {
                        $row['description'] = $volume['description'];
                    }
                }
            }
            $rows[] = $row;
        }
        $data['rows'] = $rows;
        $data['pager'] = xarTplGetPager($startnum,
                        xarModAPIFunc('encyclopedia', 'user', 'countvols'),
                        xarModURL('encyclopedia',
                                 'user',
                                 'volumes',
                                array('startnum' => '%%')),
                        xarModGetVar('encyclopedia', 'itemsperpage'));
        $data['numitems'] = xarModAPIFunc('encyclopedia', 'user', 'countitems');
    }
    return $data;
}
?>