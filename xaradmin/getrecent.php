<?php
/**
 * Display a list of recent entries
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_admin_getrecent()
{
    if (!xarSecurityCheck('ViewEncyclopedia')) {return;}

    $items = xarModAPIFunc('encyclopedia',
                          'admin',
                          'getrecent');

    $rows = array();
    foreach ($items as $item) {

        if (xarSecurityCheck('EditEncyclopedia',0,'Volume',$item['term'] . "::" . $item['id'])) {
            $row['term'] = $item['term'];
            $row['author'] = $item['author'];
            if (xarSecurityCheck('EditEncyclopedia',0,'Volume',$item['term'] . "::" . $item['id'])) {
                $row['edit'] = xarModURL('encyclopedia',
                                                   'admin',
                                                   'modifyentry',
                                                   array('itemid' => $item['id'])                                        );
            if (xarSecurityCheck('DeleteEncyclopedia',0,'Volume',$item['term'] . "::" . $item['id'])) {
                $row['delete'] = xarModURL('encyclopedia',
                                                   'admin',
                                                   'deleteentry',
                                                   array('itemid' => $item['id'])
                                        );
                }
            }
        }
        $rows[] = $row;
    }
    $data['rows'] = $rows;
    return $data;
}
?>