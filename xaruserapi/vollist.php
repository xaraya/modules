<?php
/**
 * Return an array of the volumes (for dropdowns)
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_userapi_vollist()
{
    if (!xarSecurityCheck('ReadEncyclopedia')) {return;}

    $vols = xarModAPIFunc('categories', 'user', 'getchildren',
                    array('cid' => xarModGetVar('encyclopedia','volumes')));
    foreach($vols as $vol) $volumes[$vol['cid']] = $vol['name'];

    return $volumes;
}

?>