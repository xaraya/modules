<?php
/**
 * Get the volumes in the encyclopedia
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_userapi_getvols($args)
{
    if (!xarSecurityCheck('ReadEncyclopedia')) {return;}
    extract($args);

    // Get the array volume information
    // Throw away the keys not required here
    $vols = xarModAPIFunc('categories', 'user', 'getchildren',
                    array('cid' => xarModGetVar('encyclopedia','volumes')));
    $vols = array_values($vols);

    // Set the boundaries of the recordset to be returned
    $startnum = isset($startnum) ? $startnum : 1;
    $numvols = isset($numvols) ? $numvols : 1;
    $upperlimit = min($startnum+$numvols-1, count($vols));

    // Pull together the records to be displayed
    $volumes = array();
    for($i=$startnum-1;$i<$upperlimit;$i++) {
        if (xarSecurityCheck('ReadEncyclopedia',0,'Volume',$vols[$i]['name'] . "::" . $vols[$i]['cid'])) {
            $volumes[] = array('vid' => $vols[$i]['cid'],
                             'volume' => $vols[$i]['name'],
                             'description' => $vols[$i]['description']);
        } else {
            $i--;
        }
    }
    return $volumes;
}

?>