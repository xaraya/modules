<?php
/**
* Validate an issue
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Validate an issue
*
* @stuff here???????
*/
function ebulletin_userapi_validateissue($args)
{
    extract($args);

    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();

    $ebulletintable = $xartable['ebulletin_issues'];

    $query = "SELECT *
            FROM $ebulletintable
            WHERE xar_issuedate = ?";

    $result = $dbconn->Execute($query,array($issuedate));

    if (!$result) return;
    list($iid, $pid, $issuedate, $startdate, $enddate) = $result->fields;

    $result->Close();

    if (!empty($pid)) {

        // get parent publication, so we can do the security check
        $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('pid' => $pid));

        if (!xarSecurityCheck('ReadeBulletin', 1, 'Publication', "$pid[name]:All")) {
            return;
        }
    }

    // Create the item array
    $issue = array('pid' => $pid,
                   'issuedate' => $issuedate,
                   'startdate' => $startdate,
                   'enddate' => $enddate);

    // Return the item array
    return $issue;
}

?>
