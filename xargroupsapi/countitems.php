<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
/**
 * utility function to count the number of items held by this module
 *
 * @author the XProject module development team
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 * @todo MichelV: can groups be in categories? Then add category select
 */
function xproject_groupsapi_countitems()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $groupstable = $xartable['groups'];

    $sql = "SELECT COUNT(1)
            FROM $groupstable";
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('Database error for #(1) function #(2)() in module #(3)',
                    'user', 'countitems', 'groups');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return false;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}
?>