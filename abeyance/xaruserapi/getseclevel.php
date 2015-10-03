<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Standard function to get sec level for current user
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_userapi_getseclevel($args)
{
    extract($args);
    if (empty($catid) || !is_numeric($catid)) $catid = 'All';
    if (empty($fid) || !is_numeric($fid)) $fid = 'All';

    static $levels = array();

    if (isset($levels[$catid][$fid])) {
        return $levels[$catid][$fid];
    }

    $privs = array('View','Read','Post','Moderate','Add','Edit','Delete','Admin');

    $level = 0;

    foreach ($privs as $priv) {
        if (!xarSecurityCheck($priv.'CrispBB', 0, 'Forum', "$catid:$fid")) break;
        $level += 100;
    }

    $levels[$catid][$fid] = $level;

    return $level;

}
?>