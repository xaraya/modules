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
/**
 * Get misc stats from different core modules
 *
 * @param   none
 * @return  array - misc data
 */
function stats_userapi_getmisc()
{
    // core
    $countArgs = array('include_anonymous' => false,
                       'include_myself'    => false);
    $data['users'] = xarModAPIFunc('roles','user','countall',$countArgs);
    $data['sysversion'] = xarConfigGetVar('System.Core.VersionNum');

    //TODO:
    // articles
    // comments
    // waiting content
    // categories
    unset($countArgs);

    return $data;
}

?>