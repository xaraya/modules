<?php
/**
 * Return a list of all the volumes with detail info
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_userapi_vols($args)
{
    extract($args);
    $vols = xarModAPIFunc('categories','user','getchildren',array('cid' => xarModGetVar('encyclopedia', 'volumes')));
    if(isset($search) && $search == 'search') {
        $volumes[] = array('id' => 'allvols',
                     'name' => 'All Volumes',
                     'description' => 'All Volumes');
    }
    foreach($vols as $vol) {
        if (xarSecurityCheck('ViewEncyclopedia',0, 'Volume',$vol['name'] . "::" . $vol['cid'])) {
                $volumes[] = array('id' => $vol['cid'],
                                 'name' => $vol['name'],
                                 'description' => $vol['description']);
        }
    }
    return $volumes;
}
?>