<?php
/**
 * Get the score for a user
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Userpoints Module
 * @link http://xaraya.com/index.php/release/782.html
 * @author Userpoints Module Development Team
 */
/**
 * get the score of a particular user
 * @param $args['uid'] user id
 * @returns int
 * @return score the current score of this user
 */
function userpoints_userapi_get($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (empty($uid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    xarML('user id'), 'user', 'get', 'userpoints');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security Check
    if(!xarSecurityCheck('ReadUserpoints')) return;

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $scoretable = $xartable['userpoints_score'];

    // Get items
    $query = "SELECT xar_totalscore
            FROM $scoretable
            WHERE xar_authorid = ?";
    $result =& $dbconn->Execute($query, array((int)$uid));
    if (!$result) return;

    if ($result->EOF) {
        $score = NULL;
    } else {
        $score = $result->fields[0];
        // FIXME: score is currently saved as x 100 (bigint)
        $score = (float) $score / 100.0;
    }
    $result->Close();

    return $score;
}
?>
