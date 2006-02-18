<?php
/*
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Get an Newsletter topic by a story id
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['storyId'] id of story to get
 * @returns topic array, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_userapi_gettopicsbyissue($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($issueId) || !is_numeric($issueId)) {
        $invalid[] = 'issue id';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'gettopicsbyissue', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $topicsTable = $xartable['nwsltrTopics'];

    $query = "SELECT xar_issueid,
                     xar_storyid,
                     xar_cid,
                     xar_order
              FROM $topicsTable
              WHERE xar_issueid = ?";

    // Process query
    $result =& $dbconn->Execute($query, array($issueId));

    // Check for an error
    if (!$result) return;

    // Check for no rows found
    if ($result->EOF) {
        $result->Close();
        return;
    }

    // Put items into result array
    $topics = array();
    for (; !$result->EOF; $result->MoveNext()) {
        list($issueId, $storyId, $cid, $order) = $result->fields;

        $topics[] = array('issueId' => $issueId,
                          'storyId' => $storyId,
                          'cid' => $cid,
                          'order' => $order);
    }

    // Close result set
    $result->Close();

    // Return the topics array
    return $topics;
}

?>
