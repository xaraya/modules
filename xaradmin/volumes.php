<?php
/**
 * Display the volumes of the encyclopedia
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_admin_volumes()
{
    if (!xarSecurityCheck('EditEncyclopedia')) {return;}
    if(!xarVarFetch('startnum',   'int', $startnum   , 1, XARVAR_NOT_REQUIRED)) {return;}

    $volumes = xarModAPIFunc('encyclopedia',
                          'user',
                          'getvols',
                          array('startnum' => $startnum,
                                'numvols' => xarModGetVar('Encyclopedia',
                                                          'itemsperpage')));
    $rows = array();
    foreach ($volumes as $volume) {
        $row = array();
        if (xarSecurityCheck('EditEncyclopedia',0,'Volume',"$volume[volume]::$volume[vid]")) {
            $row['volume'] = $volume['volume'];
            $row['description'] = $volume['description'];
            if (xarSecurityCheck('EditEncyclopedia',0,'Volume',"$volume[volume]::$volume[vid]")) {
                $row['edit'] = xarModURL('encyclopedia',
                                           'admin',
                                           'modifyvol',
                                           array('vid' => $volume['vid']));
                if (xarSecurityCheck('DeleteEncyclopedia',0,'Volume',"$volume[volume]::$volume[vid]")) {
                    $row['delete'] = xarModURL('encyclopedia',
                                           'admin',
                                           'deletevol',
                                           array('vid' => $volume['vid']));
                }
            }

            $rows[] = $row;
     }
  }
    $data['rows'] = $rows;
    $data['pager'] = xarTplGetPager($startnum,
                    xarModAPIFunc('encyclopedia', 'user', 'countvols'),
                    xarModURL('encyclopedia',
                             'admin',
                             'volumes',
                            array('startnum' => '%%')),
                    xarModGetVar('encyclopedia', 'itemsperpage'));
    return $data;
}
?>