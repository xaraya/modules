<?php
/**
 * File: $Id$
 *
 * BlackList API 
 *
 * @package Modules
 * @copyright (C) 2002-2005 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage BlackList
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
*/

/**
 * Get the number of blacklist domain pattterns
 *
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @access public
 * @returns integer  the total number of blacklist domain patterns
 */
function blacklist_adminapi_get_count() 
{
    
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
	$blTable =& $xartable['blacklist'];

    $sql = "SELECT  COUNT(xar_id) as total
              FROM  $blTable";

    $result =& $dbconn->Execute($sql);
    if (!$result)
        return;

    if ($result->EOF) {
        return 0;
    }

    list($total) = $result->fields;
    $result->Close();

    return $total;
}

?>
