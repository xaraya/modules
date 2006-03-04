<?php
/*
 * Censor Module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage  Censor Module
 * @author John Cox
*/

/**
 * update an censored word
 *
 * @param  $args ['cid'] the ID of the censored word
 * @param  $args ['keyword'] the new censored word
 */
function censor_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);


    // Argument check
    if ((!isset($cid)) ||
            (!isset($keyword))) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // The user API function is called
    $link = xarModAPIFunc('censor','user','get',array('cid' => $cid));

    if ($link == false) {
        $msg = xarML('No Such Censored Word Present');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }


    // Security Check
    if (!xarSecurityCheck('EditCensor')) return;
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $censortable = $xartable['censor'];
    // Update the link
    $query = "UPDATE $censortable
            SET xar_keyword         = ?,
                xar_case_sensitive  = ?,
                xar_match_case      = ?,
                xar_locale          = ?
            WHERE xar_cid           = ?";
    $bindvars = array($keyword, $case, $matchcase, serialize($locale), $cid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Let the calling process know that we have finished successfully
    return true;
}
?>
