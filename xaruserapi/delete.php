<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
    Deletes the ticket
*/
function helpdesk_userapi_delete($args)
{
    extract($args);

    if( empty($tid) )
        return false;

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable       =& xarDBGetTables();
    $helpdesktable  = $xartable['helpdesk_tickets'];

    $sql = "DELETE FROM  $helpdesktable
                   WHERE xar_id = ?";

    $result = $dbconn->Execute($sql, array($tid));
    if (!$result) return;
    $result->Close();

    return true;
}
?>
